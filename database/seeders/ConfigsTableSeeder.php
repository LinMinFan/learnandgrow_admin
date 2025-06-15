<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;
use Illuminate\Support\Facades\DB;

class ConfigsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('configs')->truncate();

        $defaultConfigs = [
            ['key' => 'site-name', 'value' => [
                'text' => '工程師的自我修養'
                ]
            ],
            ['key' => 'og-meta', 'value' => [
                'title' => '工程師的自我修養',
                'description' => '作品集,技術分享,開發筆記,閒聊雜談',
                'image' => '',
            ]],
            ['key' => 'site-description', 'value' => [
                'text' => '作品集,技術分享,開發筆記,閒聊雜談'
                ]
            ],
            ['key' => 'site-keywords', 'value' => [
                'text' => 'php,laravel,javascript,vue,redis,linux'
                ]
            ],
            ['key' => 'site-author', 'value' => [
                'text' => 'jack lin'
                ]
            ],
            ['key' => 'site-logo', 'value' => [
                'url' => '',
            ]],
            ['key' => 'favicon', 'value' => [
                'url' => '',
            ]],
            ['key' => 'contact-email', 'value' => [
                'text' => env('SITE_MAIL', 'cvse00566@gmail.com')
                ]
            ],
            ['key' => 'contact-phone', 'value' => [
                'text' => env('SITE_PHONE', '0939646359')
                ]
            ],
            ['key' => 'address', 'value' => [
                'text' => env('SITE_ADDRESS', '新北市新莊區')
                ]
            ],
            ['key' => 'copyright', 'value' => [
                'text' => '© 2025 LIN MIN FAN. All rights reserved.'
            ]],
            ['key' => 'social-facebook', 'value' => [
                'text' => ''
            ]],
            ['key' => 'social-instagram', 'value' => [
                'text' => ''
            ]],
            ['key' => 'analytics-tracking', 'value' => [
                'text' => '', // 可以再擴充 gtm 或其他追蹤碼
            ]],
            ['key' => 'maintenance-mode', 'value' => [
                'enabled' => false
                ]
            ],
        ];

        foreach ($defaultConfigs as $config) {
            Config::updateOrCreate(
                ['key' => $config['key']],
                ['value' => $config['value']]
            );
        }
    }
}
