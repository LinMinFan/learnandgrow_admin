<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MediaFolder;
use App\Models\Media;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    use RedirectWithFlashTrait;

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

        $breadcrumbs = [];

        $currentFolder = MediaFolder::with('parent')->findOrFail($id);
        $breadcrumbs = $currentFolder->getBreadcrumbs();

        // 取得當前資料夾下的子資料夾
        $folders = $currentFolder->children;

        // 取得當前資料夾下的檔案（只在非根目錄時顯示）
        $files = [];
        if ($currentFolder->getMediaCount() > 0) {
            $files = $currentFolder->getFolderMedia();
        }

        return Inertia::render('Media/Index', [
            'folders' => $folders,
            'files' => $files,
            'currentFolder' => $currentFolder,
            'breadcrumbs' => $breadcrumbs,
            'canDelete' => true,
            'canUpload' => true,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
            'folder_id' => 'required|exists:media_folders,id'
        ]);

        $uploadedFiles = $request->file('files');
        $folderId = $request->get('folder_id');
        $folder = MediaFolder::findOrFail($folderId);

        DB::beginTransaction();
        try {
            foreach ($uploadedFiles as $file) {
                $folder->addFile($file);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('media.show', $folderId)->with('error', '檔案上傳失敗！' . $e->getMessage());
        }

        return redirect()->route('media.show', $folderId)->with('success', '檔案上傳成功！');
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'folder_id' => 'required|exists:media_folders,id'
        ]);

        $folderId = $request->get('folder_id');

        DB::beginTransaction();
        try {
            $media = Media::findOrFail($id);
            $media->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('media.show', $folderId)->with('error', '檔案刪除失敗！' . $e->getMessage());
        }

        return redirect()->route('media.show', $folderId)->with('success', '檔案刪除成功！');
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array',
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

        DB::beginTransaction();
        try {
            foreach ($mediaIds as $mediaId) {
                $media = Media::findOrFail($mediaId);
                $media->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e->getMessage());
            return redirect()->route('media.show', $folderId)->with('error', '批量刪除失敗！' . $e->getMessage());
        }

        return redirect()->route('media.show', $folderId)->with('success', '選取的檔案已刪除！');
    }
}
