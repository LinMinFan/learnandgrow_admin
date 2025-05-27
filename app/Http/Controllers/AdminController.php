<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        $userList = User::with('roles')->get();

        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'admin.account')) {
            return $response;
        };

        return Inertia::render('Admin/Account/Index', compact('userList'));
    }

    public function create()
    {
        $roles = Role::all();
        return Inertia::render('Admin/Account/Create', compact('roles'));
    }

    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            // 驗證 ID 是否存在
            $validIds = Role::whereIn('id', $data['roles'])->get();
            if (count($validIds) !== count($data['roles'])) {
                throw new \Exception('所選的角色無效');
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->syncRoles($data['roles'] ?? []);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '帳號新增成功'], 200);
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return Inertia::render('Admin/Account/Edit', compact([
                'user',
                'roles'
            ]));
    }

    public function update(StoreAccountRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validated();
        
        DB::beginTransaction();
        try {
            $userUpdate = [
                'name' => $data['name'],
                'password' => !empty($data['password']) ? Hash::make($data['password']) : $user->password, // 如果沒有提供新密碼，則保持原有密碼
            ];
    
            $user->update($userUpdate);
            $user->syncRoles($data['roles'] ?? []);
   
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '帳號更新成功'], 200);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            User::findOrFail($id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '帳號刪除成功'], 200);
    }
}
