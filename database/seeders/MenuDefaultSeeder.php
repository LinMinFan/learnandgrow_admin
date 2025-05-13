<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenuDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menus')->truncate();
        $now = Carbon::now();

        $menus = [
            // 儀表板
            [
                'title' => '儀錶板',
                'icon' => 'fas fa-tachometer-alt',
                'route' => '/dashboard',
                'parent_id' => null,
                'sort' => 1,
            ],

            // 頁面管理
            [
                'title' => '頁面管理',
                'icon' => 'fas fa-file-alt',
                'route' => null,
                'parent_id' => null,
                'sort' => 2,
            ],
            [
                'title' => '首頁配置',
                'icon' => 'fas fa-home',
                'route' => '/pages/index-settings',
                'parent_id' => 2,
                'sort' => 1,
            ],

            // 文章管理
            [
                'title' => '文章管理',
                'icon' => 'fas fa-edit',
                'route' => null,
                'parent_id' => null,
                'sort' => 3,
            ],
            [
                'title' => '文章分類',
                'icon' => 'fas fa-layer-group',
                'route' => '/posts/categories',
                'parent_id' => 4,
                'sort' => 1,
            ],
            [
                'title' => '文章',
                'icon' => 'fas fa-pen-nib',
                'route' => '/posts/articles',
                'parent_id' => 4,
                'sort' => 2,
            ],

            // 網站管理
            [
                'title' => '網站管理',
                'icon' => 'fas fa-server',
                'route' => null,
                'parent_id' => null,
                'sort' => 4,
            ],
            [
                'title' => '系統參數',
                'icon' => 'fas fa-cogs',
                'route' => '/system/config',
                'parent_id' => 7,
                'sort' => 1,
            ],
            [
                'title' => '後台選單',
                'icon' => 'fas fa-stream',
                'route' => '/system/menus',
                'parent_id' => 7,
                'sort' => 2,
            ],

            // 角色和權限
            [
                'title' => '角色和權限',
                'icon' => 'fas fa-user-group',
                'route' => null,
                'parent_id' => null,
                'sort' => 5,
            ],
            [
                'title' => '系統管理員',
                'icon' => 'fas fa-user-shield',
                'route' => '/admin/accounts',
                'parent_id' => 10,
                'sort' => 1,
            ],
            [
                'title' => '角色',
                'icon' => 'fas fa-user-cog',
                'route' => '/admin/roles',
                'parent_id' => 10,
                'sort' => 2,
            ],
            [
                'title' => '權限',
                'icon' => 'fas fa-shield',
                'route' => '/admin/primissions',
                'parent_id' => 10,
                'sort' => 3,
            ],
        ];

        foreach ($menus as &$menu) {
            $menu['created_at'] = $now;
            $menu['updated_at'] = $now;
        }

        DB::table('menus')->insert($menus);
    }
}
