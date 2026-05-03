<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TwoFactorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/


// Public registration endpoint



Route::middleware('api.key')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/verify-login-2fa', [TwoFactorController::class, 'verifyLogin2FA']);

    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:api', 'check.status'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::post('/update-account', [AuthController::class, 'updateAccount']);
    Route::get('/available-avatars', [AuthController::class, 'getAvailableAvatars']);
    Route::get('/login-notifications', [AuthController::class, 'loginNotifications']);

    // Two Factor and notification preferences
    Route::post('/user/2fa/enable', [TwoFactorController::class, 'enable2FA']);
    Route::post('/user/2fa/complete-setup', [TwoFactorController::class, 'complete2FASetup']);
    Route::post('/user/2fa/cancel-setup', [TwoFactorController::class, 'cancel2FASetup']);
    Route::post('/user/2fa/disable', [TwoFactorController::class, 'disable2FA']);
    Route::post('/user/notification-preferences', [AuthController::class, 'notificationPreferences']);
});

