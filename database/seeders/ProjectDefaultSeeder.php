<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\PermissionGroup;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class ProjectDefaultSeeder extends Seeder
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
        DB::table('permission_groups')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('role_has_permissions')->truncate();
        DB::table('menus')->truncate();

        $now = Carbon::now();

        // 預設管理員帳號
        $user = User::create([
            'name' => 'admin',
            'email' => 'cvse00566@gmail.com',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin')),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 建立最高權限角色
        $role = Role::create([
            'name' => 'super admin',
            'display_name' => '超級管理員',
        ]);

        // 建立預設權限群組
        $permissionGroups = [
            [
                'name' => 'dashboard',
                'display_name' => '儀錶板',
                'description' => '儀錶板相關',
                'sort' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'index',
                'display_name' => '首頁管理',
                'description' => '首頁相關',
                'sort' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'category',
                'display_name' => '文章類別管理',
                'description' => '文章類別相關',
                'sort' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'post',
                'display_name' => '文章管理',
                'description' => '文章相關',
                'sort' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'menu',
                'display_name' => '選單管理',
                'description' => '選單相關',
                'sort' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'system',
                'display_name' => '系統參數管理',
                'description' => '系統參數相關',
                'sort' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'account',
                'display_name' => '系統管理員帳號管理',
                'description' => '系統管理員帳號相關',
                'sort' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'role',
                'display_name' => '角色管理',
                'description' => '角色相關',
                'sort' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'permission',
                'display_name' => '權限管理',
                'description' => '權限相關',
                'sort' => 9,
                'is_active' => true,
            ],
        ];

        foreach ($permissionGroups as &$permissionGroup) {
            $permissionGroup['created_at'] = $now;
            $permissionGroup['updated_at'] = $now;
        }

        DB::table('permission_groups')->insert($permissionGroups);

        $permissionGroupIds  = PermissionGroup::pluck('id', 'name');
        $dashboardId = $permissionGroupIds['dashboard'];
        $indexdId = $permissionGroupIds['index'];
        $categoryId = $permissionGroupIds['category'];
        $postId = $permissionGroupIds['post'];
        $menuId = $permissionGroupIds['menu'];
        $systemId = $permissionGroupIds['system'];
        $accountId = $permissionGroupIds['account'];
        $roleId = $permissionGroupIds['role'];
        $permissionId = $permissionGroupIds['permission'];

        // 建立預設權限
        $permissions = [
            [
                'permission_group_id' => $dashboardId,
                'name' => 'view dashboard',
                'display_name' => '檢視儀錶板',
            ],
            [
                'permission_group_id' => $indexdId,
                'name' => 'view index',
                'display_name' => '首頁配置檢視',
            ],
            [
                'permission_group_id' => $indexdId,
                'name' => 'update index',
                'display_name' => '首頁配置更新',
            ],
            [
                'permission_group_id' => $categoryId,
                'name' => 'view category',
                'display_name' => '文章類別檢視',
            ],
            [
                'permission_group_id' => $categoryId,
                'name' => 'store category',
                'display_name' => '文章類別新增',
            ],
            [
                'permission_group_id' => $categoryId,
                'name' => 'update category',
                'display_name' => '文章類別編輯',
            ],
            [
                'permission_group_id' => $categoryId,
                'name' => 'delete category',
                'display_name' => '文章類別刪除',
            ],
            [
                'permission_group_id' => $postId,
                'name' => 'view post',
                'display_name' => '文章檢視',
            ],
            [
                'permission_group_id' => $postId,
                'name' => 'store post',
                'display_name' => '文章新增',
            ],
            [
                'permission_group_id' => $postId,
                'name' => 'update post',
                'display_name' => '文章編輯',
            ],
            [
                'permission_group_id' => $postId,
                'name' => 'delete post',
                'display_name' => '文章刪除',
            ],
            [
                'permission_group_id' => $menuId,
                'name' => 'view menu',
                'display_name' => '選單檢視',
            ],
            [
                'permission_group_id' => $menuId,
                'name' => 'store menu',
                'display_name' => '選單新增',
            ],
            [
                'permission_group_id' => $menuId,
                'name' => 'update menu',
                'display_name' => '選單編輯',
            ],
            [
                'permission_group_id' => $menuId,
                'name' => 'delete menu',
                'display_name' => '選單刪除',
            ],
            [
                'permission_group_id' => $systemId,
                'name' => 'view system',
                'display_name' => '系統參數檢視',
            ],
            [
                'permission_group_id' => $systemId,
                'name' => 'update system',
                'display_name' => '系統參數更新',
            ],
            [
                'permission_group_id' => $accountId,
                'name' => 'view account',
                'display_name' => '系統管理員檢視',
            ],
            [
                'permission_group_id' => $accountId,
                'name' => 'store account',
                'display_name' => '系統管理員新增',
            ],
            [
                'permission_group_id' => $accountId,
                'name' => 'update account',
                'display_name' => '系統管理員編輯',
            ],
            [
                'permission_group_id' => $accountId,
                'name' => 'delete account',
                'display_name' => '系統管理員刪除',
            ],
            [
                'permission_group_id' => $roleId,
                'name' => 'view role',
                'display_name' => '角色檢視',
            ],
            [
                'permission_group_id' => $roleId,
                'name' => 'store role',
                'display_name' => '角色新增',
            ],
            [
                'permission_group_id' => $roleId,
                'name' => 'update role',
                'display_name' => '角色編輯',
            ],
            [
                'permission_group_id' => $roleId,
                'name' => 'delete role',
                'display_name' => '角色刪除',
            ],
            [
                'permission_group_id' => $permissionId,
                'name' => 'view permission',
                'display_name' => '權限檢視',
            ],
            [
                'permission_group_id' => $permissionId,
                'name' => 'store permission',
                'display_name' => '權限新增',
            ],
            [
                'permission_group_id' => $permissionId,
                'name' => 'update permission',
                'display_name' => '權限編輯',
            ],
            [
                'permission_group_id' => $permissionId,
                'name' => 'delete permission',
                'display_name' => '權限刪除',
            ],
        ];
        
        foreach ($permissions as $permission) {
            Permission::create([
                'permission_group_id' => $permission['permission_group_id'],
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
            ]);
        }

        // 將所有權限給 super admin 角色
        $role->givePermissionTo(Permission::all());

        // 將 super-admin 角色賦予使用者
        $user->assignRole('super admin');

        // 預設選單
        $parentMenus = [
            // 儀表板
            [
                'title' => '儀錶板',
                'icon' => 'fas fa-tachometer-alt',
                'route' => '/dashboard',
                'parent_id' => null,
                'sort' => 1,
                'is_active' => true,
                'permission_id' => null,
            ],

            // 頁面管理
            [
                'title' => '頁面管理',
                'icon' => 'fas fa-file-alt',
                'route' => null,
                'parent_id' => null,
                'sort' => 2,
                'is_active' => true,
                'permission_id' => null,
            ],

            // 文章管理
            [
                'title' => '文章管理',
                'icon' => 'fas fa-edit',
                'route' => null,
                'parent_id' => null,
                'sort' => 3,
                'is_active' => true,
                'permission_id' => null,
            ],

            // 網站管理
            [
                'title' => '網站管理',
                'icon' => 'fas fa-server',
                'route' => null,
                'parent_id' => null,
                'sort' => 4,
                'is_active' => true,
                'permission_id' => null,
            ],

            // 角色和權限
            [
                'title' => '角色和權限',
                'icon' => 'fas fa-user-group',
                'route' => null,
                'parent_id' => null,
                'sort' => 5,
                'is_active' => true,
                'permission_id' => null,
            ],
        ];

        foreach ($parentMenus as &$parentMenu) {
            $parentMenu['created_at'] = $now;
            $parentMenu['updated_at'] = $now;
        }

        DB::table('menus')->insert($parentMenus);

        $parentMenuIds  = Menu::pluck('id', 'title');
        $menuDashboardId = $parentMenuIds['儀錶板'];
        $menuIndexdId = $parentMenuIds['頁面管理'];
        $menuPostId = $parentMenuIds['文章管理'];
        $menuSystemId = $parentMenuIds['網站管理'];
        $menuAdminId = $parentMenuIds['角色和權限'];

        $subMenus = [
            [
                'title' => '首頁配置',
                'icon' => 'fas fa-home',
                'route' => '/front/index-setting',
                'parent_id' => $menuIndexdId,
                'sort' => 1,
                'is_active' => true,
                'permission_id' => null,
            ],
            [
                'title' => '文章分類',
                'icon' => 'fas fa-layer-group',
                'route' => '/post/category',
                'parent_id' => $menuPostId,
                'sort' => 1,
                'is_active' => true,
                'permission_id' => null,
            ],
            [
                'title' => '文章',
                'icon' => 'fas fa-pen-nib',
                'route' => '/post/article',
                'parent_id' => $menuPostId,
                'sort' => 2,
                'is_active' => true,
                'permission_id' => null,
            ],
            [
                'title' => '系統參數',
                'icon' => 'fas fa-cogs',
                'route' => '/system/config',
                'parent_id' => $menuSystemId,
                'sort' => 1,
                'is_active' => true,
                'permission_id' => null,
            ],
            [
                'title' => '後台選單',
                'icon' => 'fas fa-stream',
                'route' => '/system/menu',
                'parent_id' => $menuSystemId,
                'sort' => 2,
                'is_active' => true,
                'permission_id' => null,
            ],
            [
                'title' => '系統管理員',
                'icon' => 'fas fa-user-shield',
                'route' => '/admin/account',
                'parent_id' => $menuAdminId,
                'sort' => 1,
                'is_active' => true,
                'permission_id' => null,
            ],
            [
                'title' => '角色',
                'icon' => 'fas fa-user-cog',
                'route' => '/admin/role',
                'parent_id' => $menuAdminId,
                'sort' => 2,
                'is_active' => true,
                'permission_id' => null,
            ],
            [
                'title' => '權限',
                'icon' => 'fas fa-shield',
                'route' => '/admin/permission',
                'parent_id' => $menuAdminId,
                'sort' => 3,
                'is_active' => true,
                'permission_id' => null,
            ],
        ];

        foreach ($subMenus as &$subMenu) {
            $subMenu['created_at'] = $now;
            $subMenu['updated_at'] = $now;
        }

        DB::table('menus')->insert($subMenus);

        // 恢復外鍵檢查
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
