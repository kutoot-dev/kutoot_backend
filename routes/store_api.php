<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Seller\Auth\SellerAuthController;
use App\Http\Controllers\API\Seller\DashboardController;
use App\Http\Controllers\API\Seller\StoreProfileController;
use App\Http\Controllers\API\Seller\StoreCategoriesController;
use App\Http\Controllers\API\Seller\VisitorsController;
use App\Http\Controllers\API\Seller\SettingsController;
use App\Http\Controllers\API\Seller\MasterAdminSettingsController;

/*
|--------------------------------------------------------------------------
| Seller Panel APIs (as per provided spec)
|--------------------------------------------------------------------------
| Base path: /api/seller/...
| Guard: auth:store-api (JWT)
*/

Route::prefix('seller')->group(function () {
    // Auth
    Route::post('auth/login', [SellerAuthController::class, 'login']);

    Route::middleware('auth:store-api')->group(function () {
        Route::post('auth/logout', [SellerAuthController::class, 'logout']);
        Route::get('me', [SellerAuthController::class, 'me']);

        // Dashboard
        Route::get('dashboard/summary', [DashboardController::class, 'summary']);
        Route::get('dashboard/revenue-trend', [DashboardController::class, 'revenueTrend']);
        Route::get('dashboard/visitors-trend', [DashboardController::class, 'visitorsTrend']);

        // Store profile
        Route::get('store/profile', [StoreProfileController::class, 'show']);
        Route::put('store/profile', [StoreProfileController::class, 'update']);
        Route::post('store/images/upload', [StoreProfileController::class, 'uploadImages']);
        Route::delete('store/images/delete', [StoreProfileController::class, 'deleteImage']);
        Route::get('store/categories', [StoreCategoriesController::class, 'index']);

        // Visitors
        Route::get('visitors', [VisitorsController::class, 'index']);

        // Settings
        Route::put('settings/change-password', [SettingsController::class, 'changePassword']);
        Route::get('settings/bank', [SettingsController::class, 'getBank']);
        Route::put('settings/bank', [SettingsController::class, 'updateBank']);
        Route::get('settings/notifications', [SettingsController::class, 'getNotifications']);
        Route::put('settings/notifications', [SettingsController::class, 'updateNotifications']);

        // Master admin (read-only)
        Route::get('master-admin/settings', [MasterAdminSettingsController::class, 'show']);
    });
});


