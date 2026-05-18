<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SystemManagement\UsersController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemManagement\SiteSettingController;
use App\Http\Controllers\Admin\SystemManagement\RoleController;
use App\Http\Controllers\Admin\SystemManagement\AuditLogController;
use App\Http\Controllers\Admin\SystemManagement\PageController;
use App\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| Frontend Routes (default URL)
|--------------------------------------------------------------------------
*/

Route::get('/locale/{locale}', function (string $locale) {
    $supported = ['en', 'id', 'th', 'vi', 'km', 'ms'];
    if (in_array($locale, $supported, true)) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale.switch');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('frontend.sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('frontend.robots');

Route::get('/', [HomeController::class, 'index'])->name('frontend.home');
Route::get('/platform', function () {
    return redirect()->route('frontend.platform');
})->name('legacy.frontend.platform');
Route::get('/product', function () {
    return redirect()->route('frontend.product');
})->name('legacy.frontend.product');
Route::get('/product/{slug}', function (string $slug) {
    return redirect()->route('frontend.product.show', ['slug' => $slug], 301);
})->where('slug', '[a-z0-9\-]+')->name('legacy.frontend.product.show');
Route::get('/products/{product:slug}', function (App\Models\Product $product) {
    return redirect()->route('frontend.product.detail', ['product' => $product->slug], 301);
})->name('legacy.frontend.product.detail');
Route::get('/mission', fn () => app(FrontendPageController::class)->show('platform'))->name('frontend.platform');
Route::get('/history', fn () => app(FrontendPageController::class)->show('history'))->name('frontend.history');
Route::get('/programs', fn () => app(FrontendPageController::class)->show('product'))->name('frontend.product');
Route::get('/programs/{slug}', [FrontendPageController::class, 'showProduct'])
    ->where('slug', '[a-z0-9\-]+')
    ->name('frontend.product.show');
Route::get('/programs/details/{product:slug}', [FrontendPageController::class, 'showProductDetail'])
    ->name('frontend.product.detail');
Route::get('/about-us', fn () => app(FrontendPageController::class)->show('about-us'))->name('frontend.about-us');
Route::get('/contact', fn () => app(FrontendPageController::class)->show('contact'))->name('frontend.contact');
Route::post('/contact', [FrontendPageController::class, 'sendContactMessage'])->name('frontend.contact.send');
Route::get('/donate', fn () => app(FrontendPageController::class)->show('donate'))->name('frontend.donate');
Route::get('/gallery', [FrontendPageController::class, 'gallery'])->name('frontend.gallery');
Route::get('/image-gallery', function () {
    return redirect()->route('frontend.gallery', [], 301);
})->name('legacy.frontend.gallery');
Route::get('/page/image-gallery', function () {
    return redirect()->route('frontend.gallery', [], 301);
})->name('legacy.frontend.page.image-gallery');
Route::get('/page/image', function () {
    return redirect()->route('frontend.gallery', [], 301);
})->name('legacy.frontend.page.image');
Route::get('/page/{slug}', [FrontendPageController::class, 'show'])->where('slug', '[a-z0-9\-]+')->name('frontend.page');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes - Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {
    Route::get('/', function () {
        return auth()->check()
            ? redirect()->route('dashboard')
            : redirect()->route('login');
    })->name('admin');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes - Protected
|--------------------------------------------------------------------------
*/

Route::middleware(['web', Authenticate::class, 'check.admin.permission'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // system management
    Route::resource('users', UsersController::class)->names('system-management.users');
    Route::resource('site-settings', SiteSettingController::class)->names('system-management.site-settings');
    Route::post('pages/{page}/staged-media', [PageController::class, 'stagedMediaUpload'])
        ->name('system-management.pages.staged-media')
        ->middleware('extend.page.multipart');
    Route::resource('pages', PageController::class)
        ->names('system-management.pages')
        ->middleware('extend.page.multipart');
    Route::get('roles', [RoleController::class, 'index'])->name('system-management.roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->name('system-management.roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('system-management.roles.store');
    Route::get('roles/{roleName}', [RoleController::class, 'show'])->name('system-management.roles.show');
    Route::get('roles/{roleName}/edit', [RoleController::class, 'edit'])->name('system-management.roles.edit');
    Route::put('roles/{roleName}', [RoleController::class, 'update'])->name('system-management.roles.update');

    // Products
    Route::resource('products', ProductController::class)->names('admin.products');

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('system-management.audit-logs.index');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('system-management.audit-logs.show');
    Route::post('theme/log', [AuditLogController::class, 'logThemeChange'])->name('admin.theme.log');
});
