<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MediaFolder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Log;
use App\Services\MediaService;

class MediaApiController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index(Request $request)
    {
        $breadcrumbs = [
            ['id' => null, 'name' => '媒體庫',],
        ];

        // 取得預設資料夾
        $folders = MediaFolder::root()->default()->orderBy('name')->get();
        Log::info($folders);
        return response()->json([
            'folders' => $folders,
            'files' => [],
            'currentFolder' => null,
            'breadcrumbs' => $breadcrumbs,
            'canDelete' => false,
            'canUpload' => false,
        ]);
    }

    public function show(Request $request, $id)
    {
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

            return response()->json([
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
