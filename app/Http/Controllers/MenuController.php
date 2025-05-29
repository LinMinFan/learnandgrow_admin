<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\RedirectWithFlashTrait;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    use RedirectWithFlashTrait;

    public function index(Request $request)
    {
        // 如果 URL 有成功參數，設定到 session flash
        if ($response = $this->redirectIfHasFlashParams($request, 'system.menu')) {
            return $response;
        };

        $menuData = Menu::with('children.permission')
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get()
            ->filter(function ($menu) {
                return $menu->route === '/dashboard' || $menu->children->isNotEmpty(); // 過濾掉沒有子選單的父選單
            })
            ->map(function ($menu) {
                $menu->children = $menu->children->sortBy('sort')->values();
                return $menu;
            })
            ->values();

        return Inertia::render('System/Menu/Index', compact('menuData'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->get();

        return Inertia::render('System/Menu/Create', [
            'parents' => $parents
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'route' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:menus,id',
            'sort' => 'nullable|integer',
        ]);

        Menu::create($request->all());

        return redirect()->route('system.menu')->with('success', '選單建立成功');
    }

    // MenuController.php
    public function sort(Request $request)
    {
        $sorted = $request->input('sorted');

        foreach ($sorted as $item) {
            Menu::where('id', $item['id'])->update([
                'sort' => $item['sort'],
                'parent_id' => $item['parent_id'] == 0 ? null : $item['parent_id'],
            ]);
        }
    
        return response()->json(['success' => true], 200);
    }
}
