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
                'text' => '網站名稱'
                ]
            ],
            ['key' => 'og-meta', 'value' => [
                'title' => '網站名稱',
                'description' => '網站描述',
                'image' => '/images/og-image.jpg',
            ]],
            ['key' => 'site-description', 'value' => [
                'text' => '網站描述'
                ]
            ],
            ['key' => 'site-keywords', 'value' => [
                'text' => '網站關鍵字'
                ]
            ],
            ['key' => 'site-author', 'value' => [
                'text' => '網站擁有者'
                ]
            ],
            ['key' => 'site-logo', 'value' => [
                'url' => '/images/logo.png',
            ]],
            ['key' => 'favicon', 'value' => [
                'url' => '/favicon.ico',
            ]],
            ['key' => 'contact-email', 'value' => [
                'text' => env('SITE_MAIL', 'support@example.com')
                ]
            ],
            ['key' => 'contact-phone', 'value' => [
                'text' => env('SITE_PHONE', '02-1234-5678')
                ]
            ],
            ['key' => 'address', 'value' => [
                'text' => env('SITE_ADDRESS', '地址')
                ]
            ],
            ['key' => 'copyright', 'value' => [
                'text' => '© 2025 LIN MIN FAN. All rights reserved.'
            ]],
            ['key' => 'social-facebook', 'value' => [
                'text' => 'https://facebook.com/yourpage'
            ]],
            ['key' => 'social-instagram', 'value' => [
                'text' => 'https://instagram.com/yourpage'
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
