<?php

namespace App\Enums;

final class PermissionEnum
{
    // 儀錶板
    public const VIEW_DASHBOARD = 'view dashboard';

    // 頁面管理
    public const VIEW_INDEX_SETTING = 'view index setting';
    public const EDIT_INDEX_SETTING = 'edit index setting';

    // 文章類別管理
    public const VIEW_POST_CATEGORY = 'view post category';
    public const CREATE_POST_CATEGORY = 'create post category';
    public const STORE_POST_CATEGORY = 'store post category';
    public const EDIT_POST_CATEGORY = 'edit post category';
    public const UPDATE_POST_CATEGORY = 'update post category';
    public const DELETE_POST_CATEGORY = 'delete post category';

    // 文章管理
    public const VIEW_POST_ARTICLE = 'view post article';
    public const CREATE_POST_ARTICLE = 'create post article';
    public const STORE_POST_ARTICLE = 'store post article';
    public const EDIT_POST_ARTICLE = 'edit post article';
    public const UPDATE_POST_ARTICLE = 'update post article';
    public const DELETE_POST_ARTICLE = 'delete post article';

    // 網站管理
    public const VIEW_SYSTEM_CONFIG = 'view system config';
    public const UPDATE_SYSTEM_CONFIG = 'update system config';

    // 選單管理
    public const VIEW_SYSTEM_MENU = 'view system menu';
    public const CREATE_SYSTEM_MENU = 'create system menu';
    public const STORE_SYSTEM_MENU = 'store system menu';
    public const EDIT_SYSTEM_MENU = 'edit system menu';
    public const UPDATE_SYSTEM_MENU = 'update system menu';
    public const DELETE_SYSTEM_MENU = 'delete system menu';

    // 系統管理員
    public const VIEW_ADMIN_ACCOUNT = 'view admin account';
    public const CREATE_ADMIN_ACCOUNT = 'create admin account';
    public const STORE_ADMIN_ACCOUNT = 'store admin account';
    public const EDIT_ADMIN_ACCOUNT = 'edit admin account';
    public const UPDATE_ADMIN_ACCOUNT = 'update admin account';
    public const DELETE_ADMIN_ACCOUNT = 'delete admin account';

    // 角色管理
    public const VIEW_ROLE = 'view role';
    public const CREATE_ROLE = 'create role';
    public const STORE_ROLE = 'store role';
    public const EDIT_ROLE = 'edit role';
    public const UPDATE_ROLE = 'update role';
    public const DELETE_ROLE = 'delete role';

    // 權限管理
    public const VIEW_PERMISSION = 'view permission';
    public const CREATE_PERMISSION = 'create permission';
    public const STORE_PERMISSION = 'store permission';
    public const EDIT_PERMISSION = 'edit permission';
    public const UPDATE_PERMISSION = 'update permission';
    public const DELETE_PERMISSION = 'delete permission';

    /**
     * 取得所有權限的對應中文名稱
     */
    public static function labels(): array
    {
        return [
            self::VIEW_DASHBOARD => '檢視儀表板',

            self::VIEW_INDEX_SETTING => '檢視首頁設定',
            self::EDIT_INDEX_SETTING => '編輯首頁設定',

            self::VIEW_POST_CATEGORY => '檢視文章分類',
            self::CREATE_POST_CATEGORY => '新增文章分類',
            self::STORE_POST_CATEGORY => '儲存文章分類',
            self::EDIT_POST_CATEGORY => '編輯文章分類',
            self::UPDATE_POST_CATEGORY => '更新文章分類',
            self::DELETE_POST_CATEGORY => '刪除文章分類',

            self::VIEW_POST_ARTICLE => '檢視文章',
            self::CREATE_POST_ARTICLE => '新增文章',
            self::STORE_POST_ARTICLE => '儲存文章',
            self::EDIT_POST_ARTICLE => '編輯文章',
            self::UPDATE_POST_ARTICLE => '更新文章',
            self::DELETE_POST_ARTICLE => '刪除文章',

            self::VIEW_SYSTEM_CONFIG => '檢視系統參數',
            self::UPDATE_SYSTEM_CONFIG => '更新系統參數',

            self::VIEW_SYSTEM_MENU => '檢視後台選單',
            self::CREATE_SYSTEM_MENU => '新增後台選單',
            self::STORE_SYSTEM_MENU => '儲存後台選單',
            self::EDIT_SYSTEM_MENU => '編輯後台選單',
            self::UPDATE_SYSTEM_MENU => '更新後台選單',
            self::DELETE_SYSTEM_MENU => '刪除後台選單',

            self::VIEW_ADMIN_ACCOUNT => '檢視管理員',
            self::CREATE_ADMIN_ACCOUNT => '新增管理員',
            self::STORE_ADMIN_ACCOUNT => '儲存管理員',
            self::EDIT_ADMIN_ACCOUNT => '編輯管理員',
            self::UPDATE_ADMIN_ACCOUNT => '更新管理員',
            self::DELETE_ADMIN_ACCOUNT => '刪除管理員',

            self::VIEW_ROLE => '檢視角色',
            self::CREATE_ROLE => '新增角色',
            self::STORE_ROLE => '儲存角色',
            self::EDIT_ROLE => '編輯角色',
            self::UPDATE_ROLE => '更新角色',
            self::DELETE_ROLE => '刪除角色',

            self::VIEW_PERMISSION => '檢視權限',
            self::CREATE_PERMISSION => '新增權限',
            self::STORE_PERMISSION => '儲存權限',
            self::EDIT_PERMISSION => '編輯權限',
            self::UPDATE_PERMISSION => '更新權限',
            self::DELETE_PERMISSION => '刪除權限',
        ];
    }

    /**
     * 根據權限名稱取得對應的中文標籤
     */
    public static function label(string $permission): string
    {
        return self::labels()[$permission] ?? $permission;
    }

    public static function groupedLabels(): array
    {
        return [
            '儀錶板' => [
                self::VIEW_DASHBOARD => self::label(self::VIEW_DASHBOARD),
            ],
            '首頁管理' => [
                self::VIEW_INDEX_SETTING => self::label(self::VIEW_INDEX_SETTING),
                self::EDIT_INDEX_SETTING => self::label(self::EDIT_INDEX_SETTING),
            ],
            '文章分類管理' => [
                self::VIEW_POST_CATEGORY => self::label(self::VIEW_POST_CATEGORY),
                self::CREATE_POST_CATEGORY => self::label(self::CREATE_POST_CATEGORY),
                self::STORE_POST_CATEGORY => self::label(self::STORE_POST_CATEGORY),
                self::EDIT_POST_CATEGORY => self::label(self::EDIT_POST_CATEGORY),
                self::UPDATE_POST_CATEGORY => self::label(self::UPDATE_POST_CATEGORY),
                self::DELETE_POST_CATEGORY => self::label(self::DELETE_POST_CATEGORY),
            ],
            '文章管理' => [
                self::VIEW_POST_ARTICLE => self::label(self::VIEW_POST_ARTICLE),
                self::CREATE_POST_ARTICLE => self::label(self::CREATE_POST_ARTICLE),
                self::STORE_POST_ARTICLE => self::label(self::STORE_POST_ARTICLE),
                self::EDIT_POST_ARTICLE => self::label(self::EDIT_POST_ARTICLE),
                self::UPDATE_POST_ARTICLE => self::label(self::UPDATE_POST_ARTICLE),
                self::DELETE_POST_ARTICLE => self::label(self::DELETE_POST_ARTICLE),
            ],
            '網站設定' => [
                self::VIEW_SYSTEM_CONFIG => self::label(self::VIEW_SYSTEM_CONFIG),
                self::UPDATE_SYSTEM_CONFIG => self::label(self::UPDATE_SYSTEM_CONFIG),
            ],
            '選單管理' => [
                self::VIEW_SYSTEM_MENU => self::label(self::VIEW_SYSTEM_MENU),
                self::CREATE_SYSTEM_MENU => self::label(self::CREATE_SYSTEM_MENU),
                self::STORE_SYSTEM_MENU => self::label(self::STORE_SYSTEM_MENU),
                self::EDIT_SYSTEM_MENU => self::label(self::EDIT_SYSTEM_MENU),
                self::UPDATE_SYSTEM_MENU => self::label(self::UPDATE_SYSTEM_MENU),
                self::DELETE_SYSTEM_MENU => self::label(self::DELETE_SYSTEM_MENU),
            ],
            '系統管理員' => [
                self::VIEW_ADMIN_ACCOUNT => self::label(self::VIEW_ADMIN_ACCOUNT),
                self::CREATE_ADMIN_ACCOUNT => self::label(self::CREATE_ADMIN_ACCOUNT),
                self::STORE_ADMIN_ACCOUNT => self::label(self::STORE_ADMIN_ACCOUNT),
                self::EDIT_ADMIN_ACCOUNT => self::label(self::EDIT_ADMIN_ACCOUNT),
                self::UPDATE_ADMIN_ACCOUNT => self::label(self::UPDATE_ADMIN_ACCOUNT),
                self::DELETE_ADMIN_ACCOUNT => self::label(self::DELETE_ADMIN_ACCOUNT),
            ],
            '角色管理' => [
                self::VIEW_ROLE => self::label(self::VIEW_ROLE),
                self::CREATE_ROLE => self::label(self::CREATE_ROLE),
                self::STORE_ROLE => self::label(self::STORE_ROLE),
                self::EDIT_ROLE => self::label(self::EDIT_ROLE),
                self::UPDATE_ROLE => self::label(self::UPDATE_ROLE),
                self::DELETE_ROLE => self::label(self::DELETE_ROLE),
            ],
            '權限管理' => [
                self::VIEW_PERMISSION => self::label(self::VIEW_PERMISSION),
                self::CREATE_PERMISSION => self::label(self::CREATE_PERMISSION),
                self::STORE_PERMISSION => self::label(self::STORE_PERMISSION),
                self::EDIT_PERMISSION => self::label(self::EDIT_PERMISSION),
                self::UPDATE_PERMISSION => self::label(self::UPDATE_PERMISSION),
                self::DELETE_PERMISSION => self::label(self::DELETE_PERMISSION),
            ],
        ];
    }

}
