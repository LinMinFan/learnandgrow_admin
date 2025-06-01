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

        $folderId = $request->get('media_folder_id', null);
        $currentFolder = null;
        $breadcrumbs = [];

        if ($folderId) {
            $currentFolder = $this->getFolderWithParents($folderId);
            $breadcrumbs = $this->getBreadcrumbs($currentFolder);
        }

        // 取得當前資料夾下的子資料夾
        $folders = MediaFolder::where('parent_id', $folderId)
            ->orderBy('name')
            ->get();

        // 取得當前資料夾下的檔案（只在非根目錄時顯示）
        $files = [];
        if ($folderId) {
            $files = Media::where('media_folder_id', $folderId)
                ->orderBy('name')
                ->get();
        }

        return Inertia::render('Media/Index', [
            'folders' => $folders,
            'files' => $files,
            'currentFolder' => $currentFolder,
            'breadcrumbs' => $breadcrumbs,
            'canDelete' => $folderId !== null, // 第0層不可刪除
            'canUpload' => $folderId !== null, // 第0層不可上傳
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            'media_folder_id' => 'required|exists:media_folders,id'
        ]);

        $uploadedFiles = [];

        DB::beginTransaction();
        try {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('media', $filename, 'public');


                $media = Media::create([
                    'name' => $file->getClientOriginalName(),
                    'filename' => $filename,
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'folder_id' => $request->folder_id,
                ]);

                $uploadedFiles[] = $media;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '檔案上傳成功!'], 200);
    }

    public function deleteSelected(Request $request)
    {
        $request->validate([
            'selected_items' => 'required|array',
            'selected_items.*.type' => 'required|in:folder,file',
            'selected_items.*.id' => 'required|integer',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->selected_items as $item) {
                if ($item['type'] === 'folder') {
                    $folder = MediaFolder::findOrFail($item['id']);
                    // 遞歸刪除子資料夾和檔案
                    $this->deleteFolderRecursive($folder);
                } else {
                    $file = Media::findOrFail($item['id']);
                    // 刪除實際檔案
                    Storage::disk('public')->delete($file->path);
                    $file->delete();
                }
            }
        });

        return back()->with('success', '選取的項目已刪除！');
    }

    private function deleteFolderRecursive($folder)
    {
        // 刪除資料夾內的所有檔案
        foreach ($folder->media as $media) {
            Storage::disk('public')->delete($media->path);
            $media->delete();
        }

        // 遞歸刪除子資料夾
        foreach ($folder->children as $child) {
            $this->deleteFolderRecursive($child);
        }

        $folder->delete();
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
