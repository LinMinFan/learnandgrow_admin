<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MediaFolder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Log;
use App\Traits\RedirectWithFlashTrait;
use App\Traits\PermissionAuthorizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaService;

class MediaController extends Controller
{
    use RedirectWithFlashTrait;
    use PermissionAuthorizer;

    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'media.index')) {
            return $response;
        };

        $breadcrumbs = [
            ['id' => null, 'name' => '媒體庫',],
        ];

        // 取得預設資料夾
        $folders = MediaFolder::root()->default()->orderBy('name')->get();

        return Inertia::render('Media/Index', [
            'folders' => $folders,
            'files' => [],
            'currentFolder' => null,
            'breadcrumbs' => $breadcrumbs,
            'canDelete' => false, // 第0層不可刪除
            'canUpload' => false, // 第0層不可上傳
        ]);
    }

    public function show(Request $request, $id)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'media.index', ['id' => $id])) {
            return $response;
        };

        try {
            $currentFolder = MediaFolder::with('parent')->findOrFail($id);
            $breadcrumbs = $currentFolder->getBreadcrumbs();

            // 取得當前資料夾下的子資料夾
            $folders = $currentFolder->children;

            // 取得當前資料夾下的檔案（只在非根目錄時顯示）
            $mediaFiles = [];
            if ($currentFolder->getMediaCount() > 0) {
                $files = $currentFolder->getFolderMedia();
                $mediaFiles = $this->getFormattedMedia($files);
            }

            return Inertia::render('Media/Index', [
                'folders' => $folders,
                'files' => $mediaFiles,
                'currentFolder' => $currentFolder,
                'breadcrumbs' => $breadcrumbs,
                'canDelete' => true,
                'canUpload' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('載入媒體資料夾失敗', [
                'folder_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('media.index')->with('error', '資料夾不存在或無法存取！');
        }
    }

    public function store(Request $request)
    {
        $this->throwUnless('media', 'create');

        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
            'folder_id' => 'required|exists:media_folders,id'
        ]);

        $uploadedFiles = $request->file('files');
        $folderId = $request->get('folder_id');

        try {
            $folder = MediaFolder::findOrFail($folderId);
        } catch (\Exception $e) {
            return redirect()->route('media.index')->with('error', '指定的資料夾不存在！');
        }

        $uploadResults = ['success' => 0, 'failed' => 0, 'errors' => []];

        DB::beginTransaction();
        try {
            $existingFileNames = $folder->getFolderMedia()->pluck('file_name')->toArray();

            foreach ($uploadedFiles as $file) {
                try {
                    $originalName = $file->getClientOriginalName();

                    if (in_array($originalName, $existingFileNames)) {
                        throw new \Exception("{$originalName}: 檔案已存在");
                    }

                    $folder->addFile($file);
                    $uploadResults['success']++;
                } catch (\Exception $e) {
                    $uploadResults['failed']++;
                    $uploadResults['errors'][] = $file->getClientOriginalName() . ': ' . $e->getMessage();

                    Log::error('檔案上傳失敗', [
                        'file_name' => $file->getClientOriginalName(),
                        'folder_id' => $folderId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            // 根據結果回傳不同訊息
            if ($uploadResults['success'] > 0 && $uploadResults['failed'] === 0) {
                $message = "成功上傳 {$uploadResults['success']} 個檔案！";
                return redirect()->route('media.show', $folderId)->with('success', $message);
            } elseif ($uploadResults['success'] > 0 && $uploadResults['failed'] > 0) {
                $message = "成功上傳 {$uploadResults['success']} 個檔案，{$uploadResults['failed']} 個檔案上傳失敗！";
                return redirect()->route('media.show', $folderId)->with('success', $message);
            } else {
                $message = '所有檔案上傳失敗：' . implode(', ', $uploadResults['errors']);
                throw new \Exception($message);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('media.show', $folderId)->with('error', '檔案上傳失敗：' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->throwUnless('media', 'delete');

        $request->validate([
            'folder_id' => 'required|exists:media_folders,id'
        ]);

        $folderId = $request->get('folder_id');

        try {
            $media = Media::findOrFail($id);
            $fileName = $media->file_name;

            $this->mediaService->deleteMediaFileOnly($media);

            return redirect()->route('media.show', $folderId)->with('success', "檔案「{$fileName}」刪除成功！");
        } catch (\Exception $e) {
            Log::error('刪除媒體檔案失敗', [
                'media_id' => $id,
                'folder_id' => $folderId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('media.show', $folderId)->with('error', '檔案刪除失敗：' . $e->getMessage());
        }
    }

    public function deleteSelected(Request $request)
    {
        $this->throwUnless('media', 'delete');
        
        $request->validate([
            'selected_items' => 'required|array|min:1',
            'selected_items.*.type' => 'required|in:file',
            'selected_items.*.id' => 'required|integer|exists:media,id',
            'folder_id' => 'required|exists:media_folders,id'
        ]);

        $selectedItems = $request->get('selected_items');
        $folderId = $request->get('folder_id');

        // 過濾只處理檔案類型
        $mediaIds = collect($selectedItems)
            ->filter(fn($item) => $item['type'] === 'file')
            ->pluck('id')
            ->toArray();

        if (empty($mediaIds)) {
            return redirect()->route('media.show', $folderId)->with('error', '沒有選取有效的檔案！');
        }

        try {
            // 使用 MediaService 的批量刪除功能
            $results = $this->mediaService->deleteMultipleMediaFiles($mediaIds);

            $successCount = count($results['success']);
            $failedCount = count($results['failed']);

            if ($failedCount === 0) {
                $message = "成功刪除 {$successCount} 個檔案！";
                return redirect()->route('media.show', $folderId)->with('success', $message);
            } elseif ($successCount > 0) {
                $message = "成功刪除 {$successCount} 個檔案，{$failedCount} 個檔案刪除失敗！";
                return redirect()->route('media.show', $folderId)->with('success', $message);
            } else {
                throw new \Exception('所有選取的檔案都刪除失敗！');
            }
        } catch (\Exception $e) {
            Log::error('批量刪除媒體檔案失敗', [
                'media_ids' => $mediaIds,
                'folder_id' => $folderId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('media.show', $folderId)->with('error', '批量刪除失敗：' . $e->getMessage());
        }
    }

    /**
     * 取得格式化後的媒體檔案（含縮圖與 icon 訊息）
     */
    public function getFormattedMedia($medias)
    {

        // 為每個檔案添加縮圖 URL
        $mediaFiles = $medias->map(function ($media) {
            $mediaData = $media->toArray();

            // 檢查是否為圖片類型
            $isImage = str_starts_with($media->mime_type, 'image/');

            if ($isImage) {
                try {
                    // 生成縮圖 URL（如果存在）
                    $mediaData['thumbnail_url'] = $media->hasGeneratedConversion('thumb')
                        ? $media->getUrl('thumb')
                        : $media->getUrl();
                    $mediaData['has_thumbnail'] = true;
                } catch (\Exception $e) {
                    $mediaData['thumbnail_url'] = null;
                    $mediaData['has_thumbnail'] = false;
                }
            } else {
                $mediaData['thumbnail_url'] = null;
                $mediaData['has_thumbnail'] = false;
            }

            return $mediaData;
        });

        return $mediaFiles;
    }
}
