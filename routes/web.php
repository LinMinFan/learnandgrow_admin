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
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArticleController;

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

Route::middleware(['auth', 'verified', 'menu.permission'])->group(function () {
    // 儀表板
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 媒體庫
    Route::prefix('media')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('media.index');
        Route::get('/{id}', [MediaController::class, 'show'])->name('media.show');
        Route::post('/', [MediaController::class, 'store'])->name('media.store');
        Route::delete('/{id}', [MediaController::class, 'destroy'])->name('media.destroy');
        Route::post('/delete-selected', [MediaController::class, 'deleteSelected'])->name('media.deleteSelected');
    });

    // 表單管理
    Route::prefix('form')->group(function () {
        Route::get('/', [ContactFormController::class, 'index'])->name('form.index');
        Route::delete('/{id}', [ContactFormController::class, 'destroy'])->name('form.destroy');
    });
    

    // 頁面管理


    // 文章管理
    Route::prefix('post')->group(function () {
        Route::get('/category', [CategoryController::class, 'index'])->name('post.category');
        Route::get('/category/create', [CategoryController::class, 'create'])->name('post.category.create');
        Route::post('/category', [CategoryController::class, 'store'])->name('post.category.store');
        Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('post.category.edit');
        Route::put('/category/update/{id}', [CategoryController::class, 'update'])->name('post.category.update');
        Route::delete('/category/destroy/{id}', [CategoryController::class, 'destroy'])->name('post.category.destroy');
        Route::post('/category/sort', [CategoryController::class, 'sort'])->name('post.category.sort');

        Route::get('/article', [ArticleController::class, 'index'])->name('post.article');
        Route::get('/article/create', [ArticleController::class, 'create'])->name('post.article.create');
        Route::post('/article', [ArticleController::class, 'store'])->name('post.article.store');
        Route::get('/article/edit/{id}', [ArticleController::class, 'edit'])->name('post.article.edit');
        Route::put('/article/update/{id}', [ArticleController::class, 'update'])->name('post.article.update');
        Route::delete('/article/destroy/{id}', [ArticleController::class, 'destroy'])->name('post.article.destroy');
    });

    // 網站管理
    Route::prefix('system')->group(function () {
        Route::get('/config', [ConfigController::class, 'config'])->name('system.config');
        Route::put('/config', [ConfigController::class, 'update'])->name('system.config.update');
        
        Route::get('/menu', [MenuController::class, 'index'])->name('system.menu');
        Route::get('/menu/create', [MenuController::class, 'create'])->name('system.menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('system.menu.store');
        Route::get('/menu/edit/{id}', [MenuController::class, 'edit'])->name('system.menu.edit');
        Route::put('/menu/update/{id}', [MenuController::class, 'update'])->name('system.menu.update');
        Route::delete('/menu/destroy/{id}', [MenuController::class, 'destroy'])->name('system.menu.destroy');
        Route::post('/menu/sort', [MenuController::class, 'sort'])->name('system.menu.sort');
    });

    // 角色和權限
    Route::prefix('admin')->group(function () {
        // 帳號
        Route::get('/account', [AdminController::class, 'index'])->name('admin.account');
        Route::get('/account/create', [AdminController::class, 'create'])->name('admin.account.create');
        Route::post('/account', [AdminController::class, 'store'])->name('admin.account.store');
        Route::get('/account/edit/{id}', [AdminController::class, 'edit'])->name('admin.account.edit');
        Route::put('/account/update/{id}', [AdminController::class, 'update'])->name('admin.account.update');
        Route::delete('/account/destroy/{id}', [AdminController::class, 'destroy'])->name('admin.account.destroy');

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
        Route::post('/permission/add-group', [PermissionController::class, 'add_group'])->name('admin.permission.addGroup');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
