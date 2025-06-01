<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MediaFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 停用外鍵檢查
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('media_folders')->truncate();

        $defaultFolders = [
            'default',
            'web',
            'profile',
            'image',
            'icon',
            'slider',
            'post',
        ];

        foreach ($defaultFolders as $folderName) {
            MediaFolder::firstOrCreate(
                ['path' => $folderName],
                [
                    'name' => $folderName,
                    'depth' => 0,
                    'parent_id' => null,
                    'is_default' => true,
                ]
            );

            // 建立實體資料夾（使用設定的 disk，例如 'media'）
            $disk = Storage::disk('media');

            if (!$disk->exists($folderName)) {
                $disk->makeDirectory($folderName);
            }
        }

        // 恢復外鍵檢查
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
