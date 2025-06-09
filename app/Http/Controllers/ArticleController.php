<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'admin.account')) {
            return $response;
        };
        
        $userList = Article::with('roles')->get();

        return Inertia::render('Admin/Account/Index', compact('userList'));
    }
}
