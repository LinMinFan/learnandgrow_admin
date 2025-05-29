<?php
// RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'admin.role')) {
            return $response;
        };
        
        // 取得所有角色
        $roleList = Role::with('permissions')->get();

        return Inertia::render('Admin/Role/Index', compact('roleList'));
    }

    public function create()
    {
        $permissionGroups = PermissionGroup::with('permissions')
            ->where('is_active', true)
            ->orderBy('sort','asc')
            ->get();

        return Inertia::render('Admin/Role/Create', compact('permissionGroups'));
    }

    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            // 驗證 ID 是否存在
            $validIds = Permission::whereIn('id', $data['permissions'])->pluck('id')->toArray();
            if (count($validIds) !== count($data['permissions'])) {
                throw new \Exception('所選的權限無效');
            }

            $role = Role::create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
            ]);

            $role->syncPermissions($validIds);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '角色新增成功'], 200);
    }

    

    public function edit($id)
    {
        $permissionGroups = PermissionGroup::with('permissions')
            ->where('is_active', true)
            ->orderBy('sort','asc')
            ->get();
        
        $getRole = Role::with('permissions')->findOrFail($id);
        $role = [
            'id' => $getRole->id,
            'name' => $getRole->name,
            'display_name' => $getRole->display_name,
            'permissions' => $getRole->permissions->pluck('id')->toArray(), // 只取 id
        ];

        return Inertia::render('Admin/Role/Edit',  compact([
                'permissionGroups',
                'role',
            ]));
    }

    public function update(StoreRoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validated();

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
            ]);
    
            $role->syncPermissions($data['permissions']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '角色更新成功'], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Role::findOrFail($id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '角色刪除成功'], 200);
    }
}
