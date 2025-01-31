<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\AuctionController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\MazarieController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ShowDataController;
use App\Http\Controllers\Api\TajirController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
|  Routes Auth Login && Register
|--------------------------------------------------------------------------
|
*/

Route::middleware(['guest:accountApi'])->prefix('v1')->group(function () {
    Route::controller(ApiAuthController::class)->group(function () {
        Route::post('/{guard}/login', 'login');
        Route::post('/{guard}/check-code', 'checkCode');
        Route::post('/{guard}/resend-code', 'reSendVerifiyCode');
    });
    //Register
    Route::controller(AccountController::class)->group(function () {
        Route::get('general/cities', 'showCities');
        Route::post('/{guard}/registerUser', 'userRegister');
    });
    //Conditions
    Route::controller(ShowDataController::class)->group(function () {
        Route::get('general/conditions', 'showConditions');
    });
    //Forgot-Password
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::post('/{guard}/send-code', 'sendEmailCode');
        Route::post('/{guard}/checkCodeForget', 'checkCodeForge');
        Route::post('/{guard}/reset', 'resetPassword');
    });
});


/*
|--------------------------------------------------------------------------
|  Routes Normale
|--------------------------------------------------------------------------
|
*/

Route::prefix('v1/')->middleware(['auth:accountApi'])->group(function () {
    Route::controller(ShowDataController::class)->group(function () {
        Route::get('general/products', 'showProducts');
        Route::post('general/products/create', 'createProduct');
        Route::Post('general/contact_us/create', 'seendContactUs');
        Route::get('general/getNotifications', 'getNotifications');
    });


    Route::controller(AuctionController::class)->group(function () {
        Route::get('general/auctions', 'index');
        Route::post('general/auctions/create', 'store');
        Route::put('general/{auctions}', 'update');
        Route::delete('general/{auctions}', 'destroy');
    });



    Route::controller(OfferController::class)->group(function () {
        Route::post('general/offers/{auctionId}', 'store');
    });

    Route::controller(MazarieController::class)->group(function () {
        Route::get('general/home-screen', 'homeScreen');
        Route::get('general/my-sales', 'mySales');
        Route::get('general/auction-details/{auction}', 'auctionDetails');
        Route::get('general/invoices/{auction}', 'invoices');
        Route::get('general/my-wallet', 'myWallet');
    });


    Route::controller(TajirController::class)->group(function () {
        Route::get('general/tajir/home-screen/{product}', 'homeScreen');
        Route::get('general/tajir/my-purchases', 'myPurchases');
        Route::get('general/tajir/auction-details/{auction}', 'auctionDetails');
        Route::get('general/tajir/my-wallet', 'myWallet');
        Route::Post('general/tajir/myWallet/create', 'addWallet');
        Route::get('general/tajir/invoices/{auction}', 'invoices');
        Route::post('general/tajir/closeAuctions/{auctionId}', 'closeMobileAuctions');
    });
});



/*
|--------------------------------------------------------------------------
| Route Auth Logout && Update Password
|--------------------------------------------------------------------------
*/
Route::prefix('v1/')->controller(ApiAuthController::class)->middleware(['auth:accountApi'])->group(function () {
    Route::get('logout', 'logout');
});
