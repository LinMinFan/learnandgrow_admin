<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'post.category')) {
            return $response;
        };

        $categoryData = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($category) {
                return $category->children->isNotEmpty(); // 過濾掉沒有子分類的父分類
            })
            ->map(function ($category) {
                $category->children = $category->children->sortBy('sort_order')->values();
                return $category;
            })
            ->values();

        return Inertia::render('Post/Category/Index', compact('categoryData'));
    }

    public function sort(Request $request)
    {
        $sorted = $request->input('sorted');

        foreach ($sorted as $item) {
            $category = Category::find($item['id']);
    
            $newParentId = $item['parent_id'] == 0 ? null : $item['parent_id'];
            $newSortOrder = $item['sort_order'];
    
            // 只有在實際資料不同時才更新
            if ($category->sort_order != $newSortOrder || $category->parent_id != $newParentId) {
                $category->update([
                    'sort_order' => $newSortOrder,
                    'parent_id' => $newParentId,
                ]);
            }
        }
    
        return response()->json(['success' => true], 200);
    }
}
