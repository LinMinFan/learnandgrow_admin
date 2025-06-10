<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
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
        
        $Categories = Category::with('articles')
            ->orderBy('sort_order','asc')
            ->get();

        $articles = $Categories->flatMap(function ($group) {
            return $group->articles->map(function ($article) use ($group) {
                return [
                    'id' => $article->id,
                    'category_id' => $article->category_id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'excerpt' => $article->excerpt,
                    'content' => $article->content,
                    'cover_image' => $article->cover_image,
                    'status' => $article->status,
                    'published_at' => $article->published_at,
                    'author_id' => $article->author_id,
                    'is_top' => $article->is_top,
                    'author_name' => $article->author->name,
                    'category_name' => $group->name,
                ];
            });
        })->values(); // 重新 index keys，避免前端索引混亂

        return Inertia::render('Post/Article/Index', compact('articles'));
    }
}
