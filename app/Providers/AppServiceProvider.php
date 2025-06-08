<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'user' => function () {
                $user = auth()->user();
                
                return $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ] : null;
            },
            'menus' => function () {
                $user = auth()->user();

                if (!$user) {
                    return [];
                }

                $isSuperAdmin = $user->roles()->where('name', 'super admin')->exists();

                $menus = Menu::orderBy('sort')->get();

                // 先過濾
                $filtered = $menus->filter(function ($menu) use ($isSuperAdmin) {
                    return $isSuperAdmin || $menu->is_active || $menu->route === '/dashboard' || $menu->route === '/media';
                });

                // 重建巢狀結構
                $menuTree = $filtered
                    ->whereNull('parent_id')
                    ->sortBy('sort') // 主選單排序
                    ->map(function ($parent) use ($filtered) {
                        // 子選單排序
                        $children = $filtered
                            ->where('parent_id', $parent->id)
                            ->sortBy('sort')
                            ->values();

                        $parent->children = $children;
                        return $parent;
                    })
                    ->filter(function ($parent) {
                        // 儀錶板 媒體庫 聯絡表單 一定顯示；其餘若無子選單則不顯示
                        return $parent->route === '/dashboard' || $parent->route === '/media' || $parent->route === '/form' || $parent->children->isNotEmpty();
                    })
                    ->sortBy('sort')
                    ->values();

                return $menuTree;
            },
        ]);
    }
}
