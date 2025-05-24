<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        // 取得所有角色
        $permissions = Permission::all();
        
        // 如果 URL 有成功參數，設定到 session flash
        if ($request->has('success')) {
            $this->redirectWithFlash('success', $request->get('success'));
        } else if ($request->has('error')) {
            $this->redirectWithFlash('error', $request->get('error'));
        }

        return Inertia::render('Admin/Permission/Index', compact('permissions'));
    }

    public function redirectWithFlash($status, $message = null)
    {
        return redirect()->route('admin.permission')->with([$status => $message]);
    }
}
