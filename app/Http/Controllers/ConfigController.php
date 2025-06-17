<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Config;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreAccountRequest;
use App\Traits\RedirectWithFlashTrait;
use App\Traits\PermissionAuthorizer;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    use RedirectWithFlashTrait;
    use PermissionAuthorizer;

    /**
     * 顯示所有設定（可給後台頁面或 API）
     */
    public function config(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'system.config')) {
            return $response;
        };

        // 從 config/sitekeys.php 取得 key 中文對應表
        $keyDefinitions = config('sitekeys');

        $configsRaw = Config::all()->pluck('value', 'key');

        $configs = $this->formatConfigsForFrontend($configsRaw, $keyDefinitions);

        return Inertia::render('System/Config/Index', [
            'configs' => $configs,
            'keyLabels' => $keyDefinitions,
        ]);
    }

    /**
     * 接收批次更新設定
     */
    public function update(Request $request)
    {
        $this->throwUnless('system', 'edit');

        $data = $request->all();

        DB::beginTransaction();
        try {
            foreach ($data as $key => $value) {
                $config = Config::where('key', $key)->first();

                if (!$config) {
                    continue; // 找不到就跳過
                }

                $value = $this->normalizeNullToEmptyString($value);

                $config->value = $value;
                $config->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }

        return response()->json(['message' => '參數更新成功'], 200);
    }

    /**
     * 將資料庫取出的設定值，依據 keyDefinitions 格式化成前端可用的物件結構
     *
     * @param  \Illuminate\Support\Collection|array  $configsRaw  原始 key=>value 設定（可能是字串）
     * @param  array $keyDefinitions  config/sitekeys.php 的設定欄位結構
     * @return array  格式化後的設定陣列
     */
    public function formatConfigsForFrontend($configsRaw, array $keyDefinitions): array
    {
        $configs = [];

        foreach ($keyDefinitions as $key => $def) {
            $value = $configsRaw[$key] ?? null;

            switch ($def['type']) {
                case 'text':
                case 'textarea':
                    $configs[$key] = $value ?? ['text' => ''];
                    break;

                case 'favicon':
                case 'image':
                    $configs[$key] = $value ?? ['url' => ''];
                    break;

                case 'switch':
                    $configs[$key] = $value ?? ['enabled' => false];
                    break;

                case 'og-meta':
                    $configs[$key] = $value ?? ['title' => '', 'description' => '', 'image' => ''];
                    break;

                default:
                    $configs[$key] = $value;
            }
        }

        return $configs;
    }

    public function normalizeNullToEmptyString(array $arr): array
    {
        foreach ($arr as $key => $value) {
            if (is_null($value)) {
                $arr[$key] = '';
            }
        }
        return $arr;
    }
}
