<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'post.category')) {
            return $response;
        };

        $categoryData = Category::with(['children' => function ($query) {
            $query->withCount('articles');
        }])
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

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('sort_order')->get();

        return Inertia::render('Post/Category/Create', [
            'parents' => $parents,
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        $maxSort = Category::where('parent_id', $data['parent_id'])->max('sort_order') ?? 0;

        $data['sort_order'] = $maxSort + 1;
        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try {
            Category::create($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '分類新增成功'], 200);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parents = Category::whereNull('parent_id')->orderBy('sort_order')->get();
        $parent = $parents->firstWhere('id', $category->parent_id);
        $category->parent_id = $parent ? $parent : null;

        return Inertia::render('Post/Category/Edit', [
            'parents' => $parents,
            'category' => $category,
        ]);
    }

    public function update(StoreCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();

        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
        $maxSort = Category::where('parent_id', $data['parent_id'])->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSort + 1;

        DB::beginTransaction();
        try {

            $category->update($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '分類編輯成功'], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Category::findOrFail($id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '分類刪除成功'], 200);
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
