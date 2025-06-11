<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Article;
use App\Models\User;
use App\Http\Requests\StoreArticleRequest;
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
        
        $categories = Category::with('articles')
            ->orderBy('sort_order','asc')
            ->get();

        $articles = $categories->flatMap(function ($group) {
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

    public function create()
    {
        $categories = Category::whereNotNull('parent_id')->get();

        return Inertia::render('Post/Article/Create', compact('categories'));
    }

    public function store(StoreArticleRequest $request)
    {
        DB::beginTransaction();
        try {
            Article::create($request->validated());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '文章新增成功'], 200);
    }

    public function edit($id)
    {
        $categories = Category::whereNotNull('parent_id')->get();

        $article = Article::with(['category','author'])->findOrFail($id);

        return Inertia::render('Post/Article/Edit', compact([
                'categories',
                'article',
            ]));
    }

    public function update(StoreArticleRequest $request, $id)
    {
        $article = Article::findOrFail($id);

        DB::beginTransaction();
        try {
            $article->update($request->validated());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '文章更新成功'], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Article::findOrFail($id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '文章刪除成功'], 200);
    }
}
