<?php

namespace App\Enums;

final class PermissionEnum
{
    public const VIEW_DASHBOARD = 'view dashboard';

    public const VIEW_INDEX_SETTING = 'view index setting';
    public const EDIT_INDEX_SETTING = 'edit index setting';

    public const VIEW_POST_CATEGORY = 'view post category';
    public const CREATE_POST_CATEGORY = 'create post category';
    public const STORE_POST_CATEGORY = 'store post category';
    public const EDIT_POST_CATEGORY = 'edit post category';
    public const UPDATE_POST_CATEGORY = 'update post category';
    public const DELETE_POST_CATEGORY = 'delete post category';

    public const VIEW_POST_ARTICLE = 'view post article';
    public const CREATE_POST_ARTICLE = 'create post article';
    public const STORE_POST_ARTICLE = 'store post article';
    public const EDIT_POST_ARTICLE = 'edit post article';
    public const UPDATE_POST_ARTICLE = 'update post article';
    public const DELETE_POST_ARTICLE = 'delete post article';

    public const VIEW_SYSTEM_CONFIG = 'view system config';
    public const UPDATE_SYSTEM_CONFIG = 'update system config';

    public const VIEW_SYSTEM_MENU = 'view system menu';
    public const CREATE_SYSTEM_MENU = 'create system menu';
    public const STORE_SYSTEM_MENU = 'store system menu';
    public const EDIT_SYSTEM_MENU = 'edit system menu';
    public const UPDATE_SYSTEM_MENU = 'update system menu';
    public const DELETE_SYSTEM_MENU = 'delete system menu';

    public const VIEW_ADMIN_ACCOUNT = 'view admin account';
    public const CREATE_ADMIN_ACCOUNT = 'create admin account';
    public const STORE_ADMIN_ACCOUNT = 'store admin account';
    public const EDIT_ADMIN_ACCOUNT = 'edit admin account';
    public const UPDATE_ADMIN_ACCOUNT = 'update admin account';
    public const DELETE_ADMIN_ACCOUNT = 'delete admin account';

    public const VIEW_ROLE = 'view role';
    public const CREATE_ROLE = 'create role';
    public const STORE_ROLE = 'store role';
    public const EDIT_ROLE = 'edit role';
    public const UPDATE_ROLE = 'update role';
    public const DELETE_ROLE = 'delete role';

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
}
