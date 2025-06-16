<?php
namespace App\Enums;

enum PermissionMap: string
{
    case MEDIA = 'media';
    case FORM = 'form';
    case INDEX = 'index';
    case CATEGORY = 'category';
    case POST = 'post';
    case MENU = 'menu';
    case SYSTEM = 'system';
    case ACCOUNT = 'account';
    case ROLE = 'role';
    case PERMISSION = 'permission';

    /**
     * 取得指定功能的權限對應（新增與編輯）
     * @param string $key 功能名稱 (enum value)
     * @return array<string, string>|null
     */
    public static function getPermissions(string $key): ?array
    {
        return match ($key) {
            self::MEDIA->value => [
                'create' => 'store media',
                'edit' => 'update media',
                'delete' => 'delete media',
            ],
            self::FORM->value => [
                'create' => '', // 你給的權限沒有 store form，但有 update/delete
                'edit' => 'update form',
                'delete' => 'delete form',
            ],
            self::INDEX->value => [
                'create' => '', // 首頁沒有新增
                'edit' => 'update index',
            ],
            self::CATEGORY->value => [
                'create' => 'store category',
                'edit' => 'update category',
                'delete' => 'delete category',
            ],
            self::POST->value => [
                'create' => 'store post',
                'edit' => 'update post',
                'delete' => 'delete post',
            ],
            self::MENU->value => [
                'create' => 'store menu',
                'edit' => 'update menu',
                'delete' => 'delete menu',
            ],
            self::SYSTEM->value => [
                'create' => '', // 沒有新增權限
                'edit' => 'update system',
            ],
            self::ACCOUNT->value => [
                'create' => 'store account',
                'edit' => 'update account',
                'delete' => 'delete account',
            ],
            self::ROLE->value => [
                'create' => 'store role',
                'edit' => 'update role',
                'delete' => 'delete role',
            ],
            self::PERMISSION->value => [
                'create' => 'store permission',
                'edit' => 'update permission',
                'delete' => 'delete permission',
            ],
            default => null,
        };
    }
}
