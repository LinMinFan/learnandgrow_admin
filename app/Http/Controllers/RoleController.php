<?php

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
    public function index()
    {
        $roleList = Role::with('permissions')->get();

        return Inertia::render('Admin/Role/Index', compact('roleList'));
    }

    public function create()
    {
        $permissions = Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => PermissionEnum::label($permission->name),
            ];
        });

        return Inertia::render('Admin/Role/Create', compact('permissions'));
    }

    public function store(RoleStoreRequest $request)
    {
        // 轉換物件為 id 陣列
        $permissionIds = collect($request->input('permissions'))
            ->pluck('id')
            ->filter()
            ->toArray();

        // 驗證 ID 是否存在
        $validIds = Permission::whereIn('id', $permissionIds)->pluck('id')->toArray();
        if (count($validIds) !== count($permissionIds)) {
            throw ValidationException::withMessages([
                'message' => ['所選的權限無效']
            ]);
        }

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($permissionIds);

        return redirect()->route('admin.role')->with('success', '角色建立成功');
    }
}
