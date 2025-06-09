<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 停用外鍵檢查
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 清空資料表
        DB::table('categories')->truncate();

        // 主分類
        $mainCategories = [
            [
                'name' => '技術分享',
                'slug' => 'tech',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => '開發筆記',
                'slug' => 'dev-note',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => '工具推薦',
                'slug' => 'tools',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => '閒聊雜談',
                'slug' => 'life',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        $mainIds = [];

        // 插入主分類
        foreach ($mainCategories as $category) {
            $main = Category::create(array_merge($category, [
                'description' => null,
                'parent_id' => null,
            ]));

            $mainIds[$category['slug']] = $main->id;
        }

        // 子分類（依照 parent_slug 對應）
        $childCategories = [
            // 技術分享
            ['name' => '前端技術', 'slug' => 'frontend', 'parent_slug' => 'tech', 'sort_order' => 1],
            ['name' => '後端技術', 'slug' => 'backend', 'parent_slug' => 'tech', 'sort_order' => 2],
            ['name' => '資料庫', 'slug' => 'database', 'parent_slug' => 'tech', 'sort_order' => 3],
            ['name' => 'API 串接', 'slug' => 'api', 'parent_slug' => 'tech', 'sort_order' => 4],
            ['name' => '安全性', 'slug' => 'security', 'parent_slug' => 'tech', 'sort_order' => 5],

            // 開發筆記
            ['name' => '問題解法', 'slug' => 'troubleshoot', 'parent_slug' => 'dev-note', 'sort_order' => 1],
            ['name' => '開發流程', 'slug' => 'workflow', 'parent_slug' => 'dev-note', 'sort_order' => 2],
            ['name' => '重構與優化', 'slug' => 'refactor', 'parent_slug' => 'dev-note', 'sort_order' => 3],
            ['name' => '架構設計', 'slug' => 'architecture', 'parent_slug' => 'dev-note', 'sort_order' => 4],

            // 工具推薦
            ['name' => 'laravel 套件', 'slug' => 'laravel-tool', 'parent_slug' => 'tools', 'sort_order' => 1],
            ['name' => 'js 套件', 'slug' => 'js-tool', 'parent_slug' => 'tools', 'sort_order' => 2],
            ['name' => '專案管理', 'slug' => 'project-mgmt', 'parent_slug' => 'tools', 'sort_order' => 3],
            ['name' => 'DevOps 工具', 'slug' => 'devops', 'parent_slug' => 'tools', 'sort_order' => 4],

            // 閒聊雜談
            ['name' => '生活瑣事', 'slug' => 'daily', 'parent_slug' => 'life', 'sort_order' => 1],
        ];

        // 插入子分類
        foreach ($childCategories as $child) {
            if (!isset($mainIds[$child['parent_slug']])) {
                continue;
            }

            Category::create([
                'name' => $child['name'],
                'slug' => $child['slug'],
                'description' => null,
                'parent_id' => $mainIds[$child['parent_slug']],
                'is_active' => true,
                'sort_order' => $child['sort_order'],
            ]);
        }

        // 開啟外鍵檢查
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
