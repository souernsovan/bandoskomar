<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/history', [HomeController::class, 'history'])->name('history');
Route::get('/programs', [HomeController::class, 'programs'])->name('programs');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit');

Route::get('/resources/annual-report', [HomeController::class, 'annualReport'])->name('resources.annual-report');
Route::get('/resources/publication', [HomeController::class, 'publication'])->name('resources.publication');
Route::get('/resources/photo-gallery', [HomeController::class, 'photoGallery'])->name('resources.photo-gallery');
Route::get('/resources/video-center', [HomeController::class, 'videoCenter'])->name('resources.video-center');

Route::get('/get-involved/support-us', [HomeController::class, 'supportUs'])->name('get-involved.support-us');
Route::get('/get-involved/sponsor-child', [HomeController::class, 'sponsorChild'])->name('get-involved.sponsor-child');
Route::get('/get-involved/ways-to-give', [HomeController::class, 'waysToGive'])->name('get-involved.ways-to-give');
Route::get('/get-involved/career', [HomeController::class, 'career'])->name('get-involved.career');

Route::get('/donate', [HomeController::class, 'donate'])->name('donate');

Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [\App\Http\Controllers\PostController::class, 'show'])->name('posts.show');
Route::get('/pages/{page}', [HomeController::class, 'customPage'])->name('pages.custom');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->except(['show']);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->only(['index', 'store', 'destroy']);
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/donations', [\App\Http\Controllers\Admin\DonationController::class, 'index'])->name('donations.index');
});
