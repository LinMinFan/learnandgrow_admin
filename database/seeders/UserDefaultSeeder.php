<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class UserDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 停用外鍵檢查
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 清空資料表
        DB::table('users')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();

        $now = Carbon::now();

        // 預設管理員帳號
        $user = \App\Models\User::create([
            'name' => 'admin',
            'email' => 'cvse00566@gmail.com',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin')),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 建立最高權限角色
        $role = Role::create(['name' => 'super admin']);

        // 建立預設權限
        $permissions = [
            'view dashboard',
            'view index setting',
            'edit index setting',
        
            'view post category',
            'create post category',
            'edit post category',
            'delete post category',
        
            'view post article',
            'create post article',
            'edit post article',
            'delete post article',
        
            'view system config',
            'edit system config',
        
            'view system menu',
            'create system menu',
            'edit system menu',
            'delete system menu',
        
            'view admin account',
            'create admin account',
            'edit admin account',
            'delete admin account',
        
            'view role',
            'create role',
            'edit role',
            'delete role',
        
            'view permission',
            'create permission',
            'edit permission',
            'delete permission',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 將所有權限給 super admin 角色
        $role->givePermissionTo(Permission::all());

        // 將 super-admin 角色賦予使用者
        $user->assignRole('super admin');

        // 恢復外鍵檢查
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
