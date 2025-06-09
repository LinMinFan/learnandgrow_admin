<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Menu;
use App\Models\PermissionGroup;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'system.menu')) {
            return $response;
        };

        $menuData = Menu::with('children.permission')
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get()
            ->filter(function ($menu) {
                /* return $menu->route === '/dashboard' || $menu->route === '/media' || $menu->children->isNotEmpty(); */
                // 儀表板 媒體庫 聯絡表單 不可修改變更
                return $menu->children->isNotEmpty(); // 過濾掉沒有子選單的父選單
            })
            ->map(function ($menu) {
                $menu->children = $menu->children->sortBy('sort')->values();
                return $menu;
            })
            ->values();

        return Inertia::render('System/Menu/Index', compact('menuData'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->orderBy('sort')->get();
        $permissions = PermissionGroup::with('permissions')
            ->orderBy('sort')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'display_name' => $permission->display_name,
                ];
            })
            ->values();

        return Inertia::render('System/Menu/Create', [
            'parents' => $parents,
            'permissions' => $permissions,
        ]);
    }

    public function store(StoreMenuRequest $request)
    {
        $data = $request->validated();
        
        // 根據是否有 parent_id 來區分主選單或子選單
        if (empty($data['parent_id'])) {
            // 主選單：找出 parent_id 為 null 的最大 sort
            $maxSort = Menu::whereNull('parent_id')->max('sort') ?? 0;
        } else {
            // 子選單：找出 parent_id 為該父 ID 的最大 sort
            $maxSort = Menu::where('parent_id', $data['parent_id'])->max('sort') ?? 0;
        }

        $data['sort'] = $maxSort + 1;
        
        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try {
            Menu::create($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '選單新增成功'], 200);
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);

        $parents = Menu::whereNull('parent_id')->orderBy('sort')->get();
        $permissions = PermissionGroup::with('permissions')
            ->orderBy('sort')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'display_name' => $permission->display_name,
                ];
            })
            ->values();

        $parent = $parents->firstWhere('id', $menu->parent_id);
        $permission = $permissions->firstWhere('id', $menu->permission_id);
        $menu->parent_id = $parent ? $parent : null;
        $menu->permission_id = $permission ? $permission : null;

        return Inertia::render('System/Menu/Edit', [
            'parents' => $parents,
            'permissions' => $permissions,
            'menu' => $menu,
        ]);
    }

    public function update(StoreMenuRequest $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $data = $request->validated();
        
        $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();
        try {
    
            $menu->update($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '選單編輯成功'], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Menu::findOrFail($id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '選單刪除成功'], 200);
    }

    public function sort(Request $request)
    {
        $sorted = $request->input('sorted');

        foreach ($sorted as $item) {
            $menu = Menu::find($item['id']);
    
            $newParentId = $item['parent_id'] == 0 ? null : $item['parent_id'];
            $newSortOrder = $item['sort'];
    
            // 只有在實際資料不同時才更新
            if ($menu->sort != $newSortOrder || $menu->parent_id != $newParentId) {
                $menu->update([
                    'sort' => $newSortOrder,
                    'parent_id' => $newParentId,
                ]);
            }
        }
    
        return response()->json(['success' => true], 200);
    }
}
