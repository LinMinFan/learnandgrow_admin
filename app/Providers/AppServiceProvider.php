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
                $user = Auth::user();
                
                return $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ] : null;
            },
            'menus' => function () {
                $user = Auth::user();

                if (!$user) {
                    return [];
                }

                $menus = Menu::orderBy('sort')->get();
                // 過濾沒有權限的項目（只過濾有設權限的項目）
                $filtered = $menus->filter(function ($menu) use ($user) {
                    return !$menu->permission_name || $user->can($menu->permission_name);
                });

                // 重建巢狀結構
                $menuTree = $filtered
                    ->whereNull('parent_id')
                    ->sortBy('sort') // 主選單排序
                    ->map(function ($parent) use ($filtered) {
                        // 子選單排序
                        $parent->children = $filtered
                        ->where('parent_id', $parent->id)
                        ->sortBy('sort')
                        ->values();

                        return $parent;
                    })->values();

                return $menuTree;
            },
        ]);
    }
}
