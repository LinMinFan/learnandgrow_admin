<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Media;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'media.index')) {
            return $response;
        };

        $mediaItems = Media::all();

        // 根據 path 中的資料夾（去掉檔案名）做分組
        $grouped = $mediaItems->groupBy(function ($item) {
            return dirname($item->path); // 例如 "uploads/avatar"
        });

        return Inertia::render('Media/Index', [
            'groupedMedia' => $grouped,
        ]);
    }
}
