<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
}); */

Route::get('/', function (): RedirectResponse {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/* Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); */

Route::middleware(['auth', 'verified'])->group(function () {
    // 儀表板
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 頁面管理


    // 文章管理


    // 網站管理
    Route::prefix('system')->group(function () {
        Route::get('/menus', [MenuController::class, 'index'])->name('system.menus');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('system.menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('system.menus.store');
        Route::post('/menus/sort', [MenuController::class, 'sort'])->name('menus.sort');
    });

    // 角色和權限

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
