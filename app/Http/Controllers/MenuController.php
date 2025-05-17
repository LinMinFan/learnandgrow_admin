<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
{
    public function index()
    {
        $menuData = Menu::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort')
                ->get()
                ->map(function ($menu) {
                    $menu->children = $menu->children->sortBy('sort')->values();
                    return $menu;
            });

        return Inertia::render('System/Menus/Index', compact('menuData'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->get();

        return Inertia::render('System/Menus/Create', [
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

        return redirect()->route('system.menus')->with('success', '選單建立成功');
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
