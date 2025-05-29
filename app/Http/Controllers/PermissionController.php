<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Http\Requests\StorePermissionGroupRequest;
use App\Http\Requests\StorePermissionRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'admin.permission')) {
            return $response;
        };
        
        $permissionGroups = PermissionGroup::with('permissions')
            ->orderBy('sort','asc')
            ->get();

        $permissions = $permissionGroups->flatMap(function ($group) {
            return $group->permissions->map(function ($perm) use ($group) {
                return [
                    'id' => $perm->id,
                    'name' => $perm->name,
                    'display_name' => $perm->display_name,
                    'group_display_name' => $group->display_name,
                ];
            });
        })->values(); // 重新 index keys，避免前端索引混亂

        return Inertia::render('Admin/Permission/Index', compact('permissions'));
    }

    public function create()
    {
        $permissionGroups = PermissionGroup::orderBy('sort','asc')->get();

        return Inertia::render('Admin/Permission/Create', compact('permissionGroups'));
    }

    public function store(StorePermissionRequest $request)
    {
        DB::beginTransaction();
        try {
            Permission::create($request->validated());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '權限新增成功'], 200);
    }

    public function edit($id)
    {
        $permissionGroups = PermissionGroup::orderBy('sort','asc')->get();

        $permission = Permission::with('group')->findOrFail($id);

        return Inertia::render('Admin/Permission/Edit', compact([
                'permissionGroups',
                'permission',
            ]));
    }

    public function update(StorePermissionRequest $request, $id)
    {
        $permission = Permission::findOrFail($id);

        DB::beginTransaction();
        try {
            $permission->update($request->validated());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '權限更新成功'], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Permission::findOrFail($id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '權限刪除成功'], 200);
    }

    public function add_group(StorePermissionGroupRequest $request)
    {
        $validated = $request->validated();
        $maxSort = PermissionGroup::max('sort') ?? 0;
        $data = array_merge($validated,[
            'sort' => $maxSort + 1,
            'is_active' => true,
        ]);

        DB::beginTransaction();
        try {

            PermissionGroup::create($data);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '權限群組新增成功'], 200);
    }
}
