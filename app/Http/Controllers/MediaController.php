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
        $folders = MediaFolder::getDefaultFolders();

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

        $currentFolder = $this->getFolderWithParents($id);
        $breadcrumbs = $this->getBreadcrumbs($currentFolder);

        // 取得當前資料夾下的子資料夾
        $folders = [];
        if ($currentFolder->children()->exists()) {
            $folders = $currentFolder->children()->get();
        }

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
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
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
            return back()->with('error', '檔案上傳失敗！' . $e->getMessage());
        }

        return back()->with('success', '檔案上傳成功！');
    }

    public function deleteSelected(Request $request)
    {

        return back()->with('success', '選取的項目已刪除！');
    }

    private function deleteFolderRecursive($folder)
    {

    }

    public function getFolderWithParents($folderId)
    {
        $folder = MediaFolder::findOrFail($folderId);

        $relations = collect();
        for ($i = 0; $i < $folder->depth; $i++) {
            $relations->push(str_repeat('parent.', $i) . 'parent');
        }

        return MediaFolder::with($relations->toArray())->findOrFail($folderId);
    }

    public function getBreadcrumbs($folder)
    {
        $breadcrumbs = [];
        $current = $folder;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name,
            ]);
            $current = $current->parent;
        }

        // 加入根目錄
        array_unshift($breadcrumbs, [
            'id' => null,
            'name' => '媒體庫',
        ]);

        return $breadcrumbs;
    }
}
