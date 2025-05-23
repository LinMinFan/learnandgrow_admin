<?php
// RoleController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RoleStoreRequest;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        // 取得所有角色
        $roleList = Role::with('permissions')->get();
        
        // 如果 URL 有成功參數，設定到 session flash
        if ($request->has('success')) {
            $this->redirectWithFlash('success', $request->get('success'));
        } else if ($request->has('error')) {
            $this->redirectWithFlash('error', $request->get('error'));
        }

        return Inertia::render('Admin/Role/Index', compact('roleList'));
    }

    public function create()
    {
        $permissions = $this->getGroupPermissions();

        return Inertia::render('Admin/Role/Create', compact('permissions'));
    }

    public function store(RoleStoreRequest $request)
    {
        // 轉換物件為 id 陣列
        $permissionIds = collect($request->input('permissions'))
            ->pluck('id')
            ->filter()
            ->toArray();

        try {
            // 驗證 ID 是否存在
            $validIds = Permission::whereIn('id', $permissionIds)->pluck('id')->toArray();
            if (count($validIds) !== count($permissionIds)) {
                throw new \Exception('所選的權限無效');
            }

            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($permissionIds);

            return response()->json(['message' => '角色新增成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        $rolePermissions = $role->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => PermissionEnum::label($permission->name),
            ];
        });

        $permissions = $this->getGroupPermissions();

        return Inertia::render('Admin/Role/Edit', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $rolePermissions,
            ],
            'permissions' => $permissions,
        ]);
    }

    public function update(RoleStoreRequest $request, $id)
    {
        $role = Role::findOrFail($id);

        $permissionIds = collect($request->input('permissions'))->pluck('id')->toArray();

        try {
            // 驗證 ID 是否存在
            $validIds = Permission::whereIn('id', $permissionIds)->pluck('id')->toArray();
            if (count($validIds) !== count($permissionIds)) {
                throw new \Exception('所選的權限無效');
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        

        $role->update(['name' => $request->name]);
        $role->syncPermissions($permissionIds);

        return response()->json(['message' => '角色更新成功'], 200);
    }

    public function destroy($id)
    {
        try {
            Role::findOrFail($id)->delete();

            return response()->json(['message' => '角色刪除成功'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getGroupPermissions()
    {
        $groupedPermissions = collect(PermissionEnum::groupedLabels())
            ->map(function ($items, $group) {
                return [
                    'group' => $group,
                    'permissions' => collect($items)->map(function ($label, $name) {
                        $perm = Permission::where('name', $name)->first();
                        return $perm ? [
                            'id' => $perm->id,
                            'name' => $perm->name,
                            'label' => $label,
                        ] : null;
                    })->filter()->values()
                ];
        })->values();

        return $groupedPermissions;
    }

    public function redirectWithFlash($status, $message = null)
    {
        return redirect()->route('admin.role')->with([$status => $message]);
    }
}
