<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\AuctionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashbordController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;



/*
        |--------------------------------------------------------------------------
        | Route  Auth
        |--------------------------------------------------------------------------
        */

Route::prefix('cms/')->middleware('guest:admin', 'status')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('/{guard}/login', 'showLoginView')->name('auth.login');
        Route::post('/login', 'login');
        //**Route  Auth Forgot Password */
        Route::get('/{guard}/forgot-password', 'showForgotpassword')->name('auth.forgot');
        Route::post('/forgot-password', 'sendRestLink');
        //**Route  Auth Reset Password */
        Route::get('admin/reset-password/{token}', 'shoewResetPassword')->name('password.reset');
        Route::post('reset-password', 'resetPassword');
    });
});



/*
        |--------------------------------------------------------------------------
        | Route-Normal
        |--------------------------------------------------------------------------
        */
Route::prefix('cms/admin')->middleware('auth:admin', 'status', 'updatePassword')->group(function () {

    Route::controller(DashbordController::class)->group(function () {
        Route::get('/', 'showDashbord')->name('dashpard');
        Route::get('/test', 'closeExpiredAuctions');
    });

    Route::controller(AdminController::class)->group(function () {
        Route::post('blockedAdmin/{id}', 'adminBlocked')->name('admin.blocked');
    });

    Route::controller(ContactUsController::class)->group(function () {
        Route::get('contactUs', 'index')->name('contactUs.index');
    });

    Route::controller(AccountController::class)->group(function () {
        Route::get('accounts/tajir', 'indexTajir')->name('accounts.TajirIndex');
        Route::get('accounts/mazarie', 'indexMazarie')->name('accounts.MazarieIndex');
        Route::post('blockedAccounts/{id}', 'accountsBlocked')->name('accounts.blocked');
        Route::delete('accounts/{account}', 'destroy');
    });


    Route::controller(NotificationController::class)->group(function () {
        Route::get('notifications/create', 'create')->name('notifications.create');
        Route::post('send/notifications', 'sendNotification');
        Route::get('/users', 'getAllUsers');
        Route::get('notifications', 'index')->name('notifications.index');
    });


    Route::controller(AuctionController::class)->group(function () {
        Route::get('auctions', 'indexWeb')->name('auctions.index');
        Route::get('auctions/{auction}', 'indexWebDetails')->name('auctionsDetails.index');
    });
});



/*
        |--------------------------------------------------------------------------
        | Route  Resource
        |--------------------------------------------------------------------------
        */
Route::prefix('cms/admin')->middleware('auth:admin', 'status', 'updatePassword')->group(function () {
    Route::resource('countries', CountryController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('products', ProductController::class);
    Route::resource('conditions', ConditionController::class);
});



/*
        |--------------------------------------------------------------------------
        | Route Auth Logout && Update Password
        |--------------------------------------------------------------------------
        */
Route::prefix('cms/admin/')->controller(AuthController::class)->middleware(['auth:admin'])->group(function () {
    Route::get('edit-password', 'editPassword')->name('edit-password');
    Route::post('update-password', 'updatePassword')->name('auth.update-password');
    Route::get('logout', 'logout')->name('logout');
});
