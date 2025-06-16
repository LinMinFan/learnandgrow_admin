<?php
// app/Traits/PermissionAuthorizer.php

namespace App\Traits;

use App\Enums\PermissionMap;
use Illuminate\Support\Facades\Lang;
use Illuminate\Auth\Access\AuthorizationException;

trait PermissionAuthorizer
{
    /**
     * FormRequest 使用：自動判斷新增或編輯，並驗證使用者權限
     *
     * @param string $featureKey 功能代稱，如 'menu'
     * @param string $routeKey 用來判斷更新操作的 route 參數名稱，預設 'id'
     * @return bool
     *
     * @throws AuthorizationException
     */
    public function authorizeByPermission(string $featureKey, string $routeKey = 'id'): bool
    {
        $isUpdate = method_exists($this, 'route') && $this->route($routeKey) !== null;
        $action = $isUpdate ? 'edit' : 'create';

        if (!$this->checkPermission($featureKey, $action)) {
            throw new AuthorizationException($this->getAuthErrorMessage($featureKey, $action));
        }

        return true;
    }

    /**
     * Controller 或其他場景使用：檢查權限，無權限直接拋例外
     *
     * @param string $featureKey 功能代稱，如 'menu'
     * @param string $action 權限操作，如 'create','edit','delete','view'
     * @return void
     *
     * @throws AuthorizationException
     */
    public function throwUnless(string $featureKey, string $action): void
    {
        if (!$this->checkPermission($featureKey, $action)) {
            throw new AuthorizationException($this->getAuthErrorMessage($featureKey, $action));
        }
    }

    /**
     * 權限判斷（供內部使用）
     *
     * @param string $featureKey 功能代稱
     * @param string $action 操作類型
     * @return bool
     */
    protected function checkPermission(string $featureKey, string $action): bool
    {
        $permissions = PermissionMap::getPermissions($featureKey);

        if (!$permissions || !isset($permissions[$action])) {
            abort(500, Lang::get('permission.error.invalid_permission_mapping', [
                'feature' => $featureKey,
                'action' => $action,
            ]));
        }

        return auth()->check() && auth()->user()->can($permissions[$action]);
    }

    /**
     * 取得授權失敗訊息（可用多語系，預設為中文）
     *
     * @param string $featureKey 功能代稱
     * @param string $action 操作類型
     * @return string
     */
    protected function getAuthErrorMessage(string $featureKey, string $action): string
    {
        // 你可以在 resources/lang/zh_TW/permission.php 裡定義訊息：
        // 'unauthorized' => '您沒有 :action 權限於 :feature',
        return Lang::get('permission.unauthorized', [
            'feature' => $featureKey,
            'action' => $action,
        ]);
    }
}
