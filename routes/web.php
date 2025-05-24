<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

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
        Route::get('/menu', [MenuController::class, 'index'])->name('system.menu');
        Route::get('/menu/create', [MenuController::class, 'create'])->name('system.menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('system.menu.store');
        Route::post('/menu/sort', [MenuController::class, 'sort'])->name('system.menu.sort');
    });

    // 角色和權限
    Route::prefix('admin')->group(function () {
        // 帳號
        Route::get('/account', [AdminController::class, 'index'])->name('admin.account');
        Route::get('/account/create', [AdminController::class, 'create'])->name('admin.account.create');
        Route::post('/account', [AdminController::class, 'store'])->name('admin.account.store');
        // 角色
        Route::get('/role', [RoleController::class, 'index'])->name('admin.role');
        Route::get('/role/create', [RoleController::class, 'create'])->name('admin.role.create');
        Route::post('/role', [RoleController::class, 'store'])->name('admin.role.store');
        Route::get('/role/edit/{id}', [RoleController::class, 'edit'])->name('admin.role.edit');
        Route::put('/role/update/{id}', [RoleController::class, 'update'])->name('admin.role.update');
        Route::delete('/role/destroy/{id}', [RoleController::class, 'destroy'])->name('admin.role.destroy');
        // 權限
        Route::get('/permission', [PermissionController::class, 'index'])->name('admin.permission');
        Route::get('/permission/create', [PermissionController::class, 'create'])->name('admin.permission.create');
        Route::post('/permission', [PermissionController::class, 'store'])->name('admin.permission.store');
        Route::get('/permission/edit/{id}', [PermissionController::class, 'edit'])->name('admin.permission.edit');
        Route::put('/permission/update/{id}', [PermissionController::class, 'update'])->name('admin.permission.update');
        Route::delete('/permission/destroy/{id}', [PermissionController::class, 'destroy'])->name('admin.permission.destroy');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
