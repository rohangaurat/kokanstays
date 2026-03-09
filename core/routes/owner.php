<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->middleware('owner.guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/', 'showLoginForm')->name('login');
        Route::post('login', 'login')->name('login.submit');
        Route::get('logout', 'logout')->withoutMiddleware('owner.guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('registration-request/send', 'storeRegistrationRequest')->name('registration.request.send');
        Route::post('send-form-data/{id}', 'storeFormData')->name('send.form.data');
        Route::post('check-vendor', 'checkOwner')->name('check.user');
    });

    // Owner Password Reset
    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('reset');
        Route::post('reset', 'sendResetCodeEmail');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'reset')->name('password.change');
    });
});

Route::middleware('owner')->group(function () {
    Route::controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-g2fa', 'g2faVerification')->name('go2fa.verify');
    });

    Route::middleware('check.owner.status', 'owner.permission')->group(function () {
        Route::controller('OwnerController')->group(function () {
            Route::get('dashboard', 'dashboard')->name('dashboard');

            Route::get('chart/booking-report', 'bookingReport')->name('chart.booking');
            Route::get('chart/payment-report', 'paymentReport')->name('chart.payment');

            Route::get('profile', 'profile')->name('profile');
            Route::get('payment/history', 'paymentHistory')->name('payment.history');
            Route::post('profile', 'profileUpdate')->name('profile.update');

            Route::get('password', 'password')->name('password');
            Route::post('password', 'passwordUpdate')->name('password.update');

            Route::post('update/auto-payment-status', 'updateAutoPaymentStatus')->name('update.auto.payment.status');
        });

        Route::middleware('owner.validity')->group(function () {
            Route::controller('OwnerController')->group(function () {
                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //Notification
                Route::get('notifications', 'notifications')->name('notifications');
                Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
                Route::get('notifications/read-all', 'readAll')->name('notifications.readAll');
                Route::post('notifications/delete-single/{id}', 'singleNotificationDelete')->name('notifications.delete.single');
                Route::post('notifications/delete-all', 'allNotificationDelete')->name('notifications.delete.all');

                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
            });

            Route::controller('StaffController')->prefix('staff')->name('staff.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('save/{id?}', 'save')->name('save');
                Route::post('switch-status/{id}', 'status')->name('status');
                Route::get('login/{id}', 'login')->name('login');
            });

            Route::controller('RolesController')->prefix('roles')->name('roles.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('add', 'add')->name('add');
                Route::get('edit/{id}', 'edit')->name('edit');
                Route::post('save/{id?}', 'save')->name('save');
            });

            Route::name('hotel.')->prefix('hotel')->group(function () {
                //Hotel setting
                Route::controller('HotelSettingController')->name('setting.')->prefix('setting')->group(function () {
                    Route::get('', 'index')->name('index');
                    Route::post('update/{id}', 'update')->name('update');

                    //payment system
                    Route::get('payment-systems', 'paymentSystems')->name('payment.systems');
                    Route::post('payment-system/add', 'addPaymentSystem')->name('payment.system.add');
                    Route::post('payment-system/update/{id}', 'addPaymentSystem')->name('payment.system.update');
                    Route::post('payment-system/status/update/{id}', 'updatePaymentSystemStatus')->name('payment.system.status.update');
                });

                //Room Type
                Route::controller('RoomTypeController')->name('room.type.')->prefix('room-type')->group(function () {
                    Route::get('', 'index')->name('all');
                    Route::get('create', 'create')->name('create');
                    Route::get('edit/{id}', 'edit')->name('edit');
                    Route::post('save/{id?}', 'save')->name('save');
                    Route::post('status/{id}', 'status')->name('status');
                });

                //Room
                Route::controller('RoomController')->name('room.')->prefix('room')->group(function () {
                    Route::get('', 'index')->name('all');
                    Route::post('add', 'addRoom')->name('add');
                    Route::post('update/{id}', 'addRoom')->name('update');
                    Route::post('update/status/{id}', 'status')->name('status');
                });

                //Extra Services
                Route::controller('ExtraServiceController')->name('extra_services.')->prefix('premium-service')->group(function () {
                    Route::get('', 'index')->name('all');
                    Route::post('save/{id?}', 'save')->name('save');
                    Route::post('status/{id}', 'status')->name('status');
                });
            });

            Route::controller('BookRoomController')->group(function () {
                // Route::get('update-booking/{id}', 'updateBooking')->name('update.booking');
                Route::get('book-room/{id?}', 'room')->name('book.room');
                Route::post('room-book', 'book')->name('room.book');
                Route::get('room/search', 'searchRoom')->name('room.search');
                Route::post('room/session-data/update', 'updateRoomSessionData')->name('room.session.data.update');
                Route::get('available_room', 'getRooms')->name('room.available');
            });

            Route::name('booking.')->prefix('booking')->group(function () {
                Route::controller('BookingController')->group(function () {
                    Route::get('all-bookings', 'allBookingList')->name('all');
                    Route::get('approved', 'activeBookings')->name('active');
                    Route::get('canceled-bookings', 'canceledBookingList')->name('canceled.list');
                    Route::get('checked-out-booking', 'checkedOutBookingList')->name('checked.out.list');
                    Route::get('todays/booked-room', 'todaysBooked')->name('todays.booked');
                    Route::get('todays/check-in', 'todayCheckInBooking')->name('todays.checkin');
                    Route::get('todays/checkout', 'todayCheckoutBooking')->name('todays.checkout');
                    Route::get('refundable', 'refundableBooking')->name('refundable');
                    Route::get('checkout/delayed', 'delayedCheckout')->name('checkout.delayed');
                    Route::get('details/{id}', 'bookingDetails')->name('details');
                    Route::get('booked-rooms/{id}', 'bookedRooms')->name('booked.rooms');
                });

                Route::controller('ManageBookingController')->group(function () {
                    Route::post('key/handover/{id}', 'handoverKey')->name('key.handover');
                    Route::post('booking-merge/{id}', 'mergeBooking')->name('merge');

                    Route::get('bill-payment/{id}', 'paymentView')->name('payment');
                    Route::post('bill-payment/{id}', 'payment')->name('payment');

                    Route::post('add-charge/{id}', 'addExtraCharge')->name('extra.charge.add');
                    Route::post('subtract-charge/{id}', 'subtractExtraCharge')->name('extra.charge.subtract');

                    Route::get('booking-checkout/{id}', 'checkOutPreview')->name('checkout');
                    Route::post('booking-checkout/{id}', 'checkOut')->name('checkout');

                    Route::get('premium-service/details/{id}', 'extraServiceDetail')->name('service.details');
                    Route::get('booking-invoice/{id}', 'generateInvoice')->name('invoice');
                });

                Route::controller('CancelBookingController')->group(function () {
                    Route::get('cancel/{id}', 'cancelBooking')->name('cancel');
                    Route::post('cancel-full/{id}', 'cancelFullBooking')->name('cancel.full');
                    Route::post('booked-room/cancel/{id}', 'cancelSingleBookedRoom')->name('booked.room.cancel');
                    Route::post('cancel-booking/{id}', 'cancelBookingByDate')->name('booked.day.cancel');
                });
            });

            Route::controller('BookingController')->prefix('booking')->group(function () {
                Route::get('upcoming/check-in', 'upcomingCheckIn')->name('upcoming.booking.checkin');
                Route::get('upcoming/checkout', 'upcomingCheckout')->name('upcoming.booking.checkout');
                Route::get('pending/check-in', 'pendingCheckIn')->name('pending.booking.checkin');
                Route::get('delayed/checkout', 'delayedCheckouts')->name('delayed.booking.checkout');
            });

            Route::controller('BookingExtraServiceController')->prefix('premium-service')->name('extra.service.')->group(function () {
                Route::get('all', 'list')->name('list');
                Route::get('add-new', 'addNew')->name('add');
                Route::post('add', 'addService')->name('save');
                Route::post('delete/{id}', 'delete')->name('delete');
            });

            Route::controller('ManageBookingRequestController')->prefix('booking')->name('request.booking.')->group(function () {
                Route::get('requests', 'index')->name('all');
                Route::get('request/detail/{id}', 'approve')->name('approve');
                Route::post('request/cancel/{id}', 'cancel')->name('cancel');
                Route::post('assign-room', 'assignRoom')->name('assign.room');
            });

            // Report
            Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
                Route::get('transaction', 'transaction')->name('transaction');
                Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
                Route::get('payments/received/history', 'paymentsReceived')->name('payments.received');
                Route::get('payment/returned/history', 'paymentReturned')->name('payments.returned');
                Route::get('bookings', 'bookings')->name('bookings');
                Route::get('booking-actions', 'bookingSituationHistory')->name('action.history');
            });

            Route::controller('PermissionController')->prefix('permissions')->name('permissions.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('update-permissions', 'updatePermissions')->name('update');
            });

            // Withdraw
            Route::controller('WithdrawController')->prefix('withdraw')->name('withdraw')->group(function () {
                Route::get('/', 'withdrawMoney');
                Route::post('/', 'withdrawStore')->name('.money');
                Route::get('preview', 'withdrawPreview')->name('.preview');
                Route::post('preview', 'withdrawSubmit')->name('.submit');
                Route::get('history', 'withdrawLog')->name('.history');
            });

            // reviews
            Route::controller('ReviewController')->prefix('review')->name('review.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::get('detail/{id}', 'reviewDetail')->name('details');
                Route::post('reply/{id}', 'reply')->name('reply');
            });
        });

        //Support ticket
        Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
            Route::get('/', 'supportTicket')->name('index');
            Route::get('/new', 'openSupportTicket')->name('open');
            Route::post('/create', 'storeSupportTicket')->name('store');
            Route::get('/view/{ticket}', 'viewTicket')->name('view');
            Route::post('/reply/{ticket}', 'replyTicket')->name('reply');
            Route::post('/close/{ticket}', 'closeTicket')->name('close');
            Route::get('/download/{ticket}', 'ticketDownload')->name('download');
        });

        //payment
        Route::controller('\App\Http\Controllers\Gateway\PaymentController')->prefix('payment')->name('deposit.')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
