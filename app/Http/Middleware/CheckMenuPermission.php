<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Menu;

class CheckMenuPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $currentPath = '/' . $request->path(); // 加上斜線前綴，與資料庫格式一致

        // 查詢是否有對應 route 的選單
        $menu = Menu::where('route', $currentPath)->first();

        // 如果有綁定權限，檢查使用者是否擁有
        if ($menu && $menu->permission && !$user->can($menu->permission->name)) {
            abort(403, '無權限訪問此頁面');
        }

        return $next($request);
    }
}
