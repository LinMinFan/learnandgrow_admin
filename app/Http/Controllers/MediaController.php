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

        $mediaData = [];

        return Inertia::render('Media/Index', compact('mediaData'));
    }
}
