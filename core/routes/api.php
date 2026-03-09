<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::namespace('Api')->name('api.')->group(function () {

    Route::controller('AppController')->group(function () {
        Route::get('general-setting', 'generalSetting');
        Route::get('get-countries', 'getCountries');
        Route::get('language/{key?}', 'getLanguage');
        Route::get('policies', 'policies');
        Route::get('policy/{slug}', 'policyContent');
        Route::get('faq', 'faq');
        Route::get('cookie', 'cookie');
        Route::post('cookie/accept', 'cookieAccept');

        Route::get('sections/{key?}', 'allSections');

        Route::get('popular-hotels', 'getPopularHotels');
        Route::get('popular-cities', 'popularCities');
        Route::get('search-cities', 'searchCities');
        Route::get('featured-hotels', 'featuredHotels');
    });

    Route::namespace('Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('login', 'login');
            Route::post('check-token', 'checkToken');
            Route::post('social-login', 'socialLogin');
        });

        Route::post('register', 'RegisterController@register');

        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/email', 'sendResetCodeEmail');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('user-data-submit', 'UserController@userDataSubmit');

        //authorization
        Route::middleware('registration.complete')->controller('AuthorizationController')->group(function () {
            Route::get('authorization', 'authorization');
            Route::get('resend-verify/{type}', 'sendVerifyCode');
            Route::post('verify-email', 'emailVerification');
            Route::post('verify-mobile', 'mobileVerification');
        });

        Route::middleware(['check.status'])->group(function () {

            Route::middleware('registration.complete')->group(function () {
                Route::controller('UserController')->group(function () {
                    Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
                    Route::get('home', 'home');
                    Route::get('dashboard', 'dashboard');
                    Route::get('user-info', 'userInfo');

                    Route::any('payment/history', 'paymentHistory');

                    Route::post('profile-setting', 'submitProfile');
                    Route::post('change-password', 'submitPassword');

                    Route::get('booking/history', 'bookingHistory');
                    Route::get('booking/detail/{id}', 'bookingDetail');

                    Route::post('add-device-token', 'addDeviceToken');
                    Route::get('push-notifications', 'pushNotifications');
                    Route::post('push-notifications/read/{id}', 'pushNotificationsRead');

                    Route::post('delete-account', 'deleteAccount');
                });

                //search and filter hotel
                Route::controller('HotelController')->prefix('hotel')->group(function () {
                    Route::get('search', 'search');
                    Route::get('filter-by-city/{id}', 'filterByCity');
                    Route::get('filter-parameters', 'getFilterParameters');
                    Route::get('detail/{id}', 'detail');
                });

                //Booking Request
                Route::controller('BookingRequestController')->prefix('booking-request')->group(function () {
                    Route::get('history', 'history');
                    Route::post('delete/{id}', 'delete');
                    Route::post('send', 'sendRequest');
                });

                // Review
                Route::controller('ReviewController')->prefix('review')->group(function () {
                    Route::post('submit/{id}', 'reviewSubmit');
                    Route::post('reply/{id}', 'reviewReply');
                });

                // Payment
                Route::controller('PaymentController')->prefix('payment')->group(function () {
                    Route::get('methods/{bookingId}', 'methods');
                    Route::post('insert', 'paymentInsert');
                });

                Route::controller('TicketController')->prefix('ticket')->group(function () {
                    Route::get('/', 'supportTicket');
                    Route::post('create', 'storeSupportTicket');
                    Route::get('view/{ticket}', 'viewTicket');
                    Route::post('reply/{id}', 'replyTicket');
                    Route::post('close/{id}', 'closeTicket');
                    Route::get('download/{attachment_id}', 'ticketDownload');
                });
            });
        });

        Route::get('logout', 'Auth\LoginController@logout');
    });
});
