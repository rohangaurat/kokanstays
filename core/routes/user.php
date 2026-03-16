<?php
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });

    Route::controller('SocialiteController')->group(function () {
        Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
        Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {
        Route::namespace('User')->group(function(){
            Route::controller(UserController::class)->group(function(){
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //Report
                Route::any('payment/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions','transactions')->name('transactions');

                Route::post('add-device-token','addDeviceToken')->name('add.device.token');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function(){
                Route::post('profile-image-update', 'updateProfileImage')->name('profile.image.update');
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting-submit', 'submitProfile')->name('profile.setting.submit');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password-submit', 'submitPassword')->name('change.password.submit');
            });

            Route::controller('BookingController')->name('booking.')->prefix('booking')->group(function(){
                Route::get('history', 'bookingHistory')->name('history');
                Route::get('request-history', 'bookingRequestHistory')->name('request.history');
                Route::get('request-details/{id}', 'bookingRequestDetails')->name('request.details');
                Route::post('request-submit', 'bookingRequestSubmit')->name('request.submit');
                Route::get('details/{id}', 'bookingDetails')->name('details');
                Route::get('invoice/{id}', 'bookingInvoice')->name('invoice');
                Route::post('delete/{id}', 'bookingDelete')->name('delete');
            });

            Route::controller('ReviewController')->name('review.')->prefix('review')->group(function(){
                Route::post('submit/{id}', 'reviewSubmit')->name('submit');
                Route::post('reply/{id}', 'reviewReply')->name('reply');
            });
        });
        // Payment
        Route::controller('Gateway\PaymentController')->prefix('payment')->name('deposit.')->group(function () {
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            Route::any('/{id?}', 'deposit')->name('index');
        });

        Route::post('add-device-token', [UserController::class, 'addDeviceToken'])->name('add.device.token');
    });
});
