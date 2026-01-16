<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WEB\Store\Auth\StoreLoginController;
use App\Http\Controllers\WEB\Store\DashboardController as StoreDashboardController;
use App\Http\Controllers\WEB\Store\StoreProfileController as StoreStoreProfileController;
use App\Http\Controllers\WEB\Store\VisitorsController as StoreVisitorsController;
use App\Http\Controllers\WEB\Store\UserLedgerController as StoreUserLedgerController;
use App\Http\Controllers\WEB\Store\SettingsController as StoreSettingsController;

/*
|--------------------------------------------------------------------------
| Store Panel (Blade) Routes
|--------------------------------------------------------------------------
| Prefix: /store/...
| Guard: auth:store
*/
Route::middleware(['demo', 'XSS', 'maintainance'])->prefix('store')->name('store.')->group(function () {
    // Auth (no login required)
    Route::middleware('guest:store')->group(function () {
        Route::get('login', [StoreLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [StoreLoginController::class, 'login'])->name('login.submit');
    });

    Route::get('logout', [StoreLoginController::class, 'logout'])->name('logout');

    // Panel (login required)
    Route::middleware('auth:store')->group(function () {
        Route::get('dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');

        Route::get('store-profile', [StoreStoreProfileController::class, 'edit'])->name('store-profile');
        Route::post('store-profile', [StoreStoreProfileController::class, 'update'])->name('store-profile.update');
        Route::delete('store-image/{id}', [StoreStoreProfileController::class, 'deleteImage'])->name('store-image.delete');

        Route::get('visitors', [StoreVisitorsController::class, 'index'])->name('visitors');
        Route::get('visitors/data', [StoreVisitorsController::class, 'data'])->name('visitors.data');
        Route::get('visitors/search-users', [StoreVisitorsController::class, 'searchUsers'])->name('visitors.search-users');
        Route::get('visitors/calculate-by-user', [StoreVisitorsController::class, 'calculateByUser'])->name('visitors.calculate-by-user');
        Route::post('visitors/add-transaction-with-user', [StoreVisitorsController::class, 'addTransactionWithUser'])->name('visitors.add-transaction-with-user');
        Route::get('visitors/{visitorId}/calculate-transaction', [StoreVisitorsController::class, 'calculateTransaction'])->name('visitors.calculate-transaction');
        Route::post('visitors/{visitorId}/create-transaction', [StoreVisitorsController::class, 'createTransaction'])->name('visitors.create-transaction');

            Route::get('users/{user}/ledger', [StoreUserLedgerController::class, 'show'])->name('users.ledger');
            Route::get('users/{user}/ledger/data', [StoreUserLedgerController::class, 'data'])->name('users.ledger.data');

        Route::get('settings/change-password', [StoreSettingsController::class, 'changePasswordForm'])->name('settings.change-password');
        Route::post('settings/change-password', [StoreSettingsController::class, 'changePassword'])->name('settings.change-password.submit');

        Route::get('settings/bank', [StoreSettingsController::class, 'bankForm'])->name('settings.bank');
        Route::post('settings/bank', [StoreSettingsController::class, 'updateBank'])->name('settings.bank.update');

        Route::get('settings/notifications', [StoreSettingsController::class, 'notificationsForm'])->name('settings.notifications');
        Route::post('settings/notifications', [StoreSettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
    });
});


