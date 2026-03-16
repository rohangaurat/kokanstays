<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\City;
use App\Models\Deposit;
use App\Models\DeviceToken;
use App\Models\NotificationLog;
use App\Models\Owner;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
public function home()
{
    $ads = Advertisement::whereDate('end_date', '>', now())
        ->where('status', 1)
        ->inRandomOrder()
        ->limit(5)
        ->get();


        $popularHotels = Owner::active()->notExpired()->whereHas('hotelSetting')
            ->withCount(['bookings as total_bookings' => function ($query) {
                $query->where('status', Status::BOOKING_ACTIVE)->whereDate('created_at', '>=', Carbon::now()->subDays(gs('popularity_count_from')));
            }])->having('total_bookings', '>', 0)->orderBy('total_bookings')->with('hotelSetting')->withMin(['roomTypes as minimum_fare' => function ($query) {
                $query->active();
            }], 'fare');

        $totalOwners = (clone $popularHotels)->count();
        $owners      = $popularHotels->limit(5)->get();


        $featuredOwners      = Owner::owner()->active()->notExpired()->featured()->whereHas('hotelSetting')->with('hotelSetting');
        $totalFeaturedOwners = (clone $featuredOwners)->count();
        $featuredOwners      = $featuredOwners->limit(5)->get();

        $popularCities = City::active()->popular()->withCount(['hotelSettings as total_hotel' => function ($hotelSetting) {
            $hotelSetting->whereHas('owner', function ($owner) {
                $owner->where('status', Status::USER_ACTIVE)->whereDate('owners.expire_at', '>=', now())->whereHas('roomTypes', function ($roomTypes) {
                    $roomTypes->where('status', Status::ROOM_TYPE_ACTIVE);
                });
            });
        }])->having('total_hotel', '>', 0)->with('country:id,name')->orderBy('total_hotel', 'DESC');

        $totalPopularCities = (clone $popularCities)->count();
        $popularCities      = $popularCities->limit(4)->get();

        $notify[] = 'Home Screen';
        return responseSuccess('home_screen', $notify, [
            'ads'                   => $ads,
            'owners'                => $owners,
            'total_owners'          => $totalOwners,
            'popular_cities'        => $popularCities,
            'total_popular_cities'  =>  $totalPopularCities,
            'featured_owners'       => $featuredOwners,
            'total_featured_owners' => $totalFeaturedOwners
        ]);
    }

    public function dashboard()
    {
        $notify[] = 'User dashboard';
        return responseSuccess('user_dashboard', $notify, [
            'user' => auth()->user()
        ]);
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == Status::YES) {
            $notify[] = 'You\'ve already completed your profile';
            return responseError('already_completed', $notify);
        }

        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));


        $validator = Validator::make($request->all(), [
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'username'     => 'required|unique:users|min:6',
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);


        if ($validator->fails()) return responseError('validation_error', $validator->errors());

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = 'No special character, space or capital letters in username';
            return responseError('validation_error', $notify);
        }

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        $user->username     = $request->username;

        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        $notify[] = 'Profile completed successfully';
        return responseSuccess('profile_completed', $notify, ['user' => $user]);
    }

    public function paymentHistory(Request $request)
    {
        $deposits = Deposit::where('user_id', auth()->id())->where('status', '!=', Status::PAYMENT_INITIATE);
        if ($request->search) {
            $deposits = $deposits->where('trx', $request->search);
        }
        $deposits = $deposits->with('booking')->orderBy('id', 'desc')->apiQuery();

        $notify[] = 'Payment History';
        return responseSuccess('payment_history', $notify, ['deposits' => $deposits]);
    }

    public function submitProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
        ], [
            'firstname.required' => 'The first name field is required',
            'lastname.required' => 'The last name field is required'
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;

        $user->save();

        $notify[] = 'Profile updated successfully';
        return responseSuccess('profile_updated', $notify);
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation]
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();

            $notify[] = 'Password changed successfully';
            return responseSuccess('password_changed', $notify);
        } else {
            $notify[] = 'The password doesn\'t match!';
            return responseError('validation_error', $notify);
        }
    }

    public function bookingHistory()
    {
        $bookings = Booking::where('user_id', auth()->id())
    ->with('bookedRooms', 'bookedRooms.room.roomType:id,name', 'owner.hotelSetting')
    ->latest()
    ->apiQuery();

// $bookings->getCollection()->transform(function ($booking) {

//     // Custom Fix (KokanStays): normalize due amount for mobile app
//     $due = $booking->total_amount - $booking->paid_amount;

//     // For mobile apps expecting positive due
//     if ($due < 0) {
//         $booking->due_amount = abs($due);
//         $booking->paid_amount = $booking->total_amount; 
//         $booking->is_refundable = true;
//     } else {
//         $booking->due_amount = $due;
//         $booking->is_refundable = false;
//     }

//     return $booking;
// });

        $notify[] = 'Booking history';
        return responseSuccess('booking_history', $notify, ['bookings' => $bookings]);
    }

    public function bookingDetail($id)
{
    $booking = Booking::where('user_id', auth()->id())->with([
        'usedExtraService.room',
        'usedExtraService.extraService',
        'payments',
        'guest',
        'owner:id,firstname',
        'owner.hotelSetting:id,owner_id,location_id,city_id,country_id,name,image,tax_name',
        'owner.hotelSetting.location',
        'owner.hotelSetting.city',
        'owner.hotelSetting.country'
    ])->where('id', $id)->first();

    if (!$booking) {
        $notify[] = 'Booking record not found';
        return ResponseError('booking_detail', $notify);
    }

    $bookedRooms = BookedRoom::where('booking_id', $booking->id)
        ->with('room', 'roomType:id,name')
        ->get()
        ->groupBy('booked_for');

    // Payment calculations
    $paymentReceived = $booking->payments
        ->where('type', 'BOOKING_PAYMENT_RECEIVED')
        ->sum('amount');

    $refunded = $booking->payments
        ->where('type', 'BOOKING_PAYMENT_RETURNED')
        ->sum('amount');

    $due = $booking->total_amount - $paymentReceived;

if ($due < 0) {

    $dueAmount = 0;
    $isRefundable = true;

    $booking->paid_amount = $paymentReceived;
    $booking->due_amount = 0;

} else {

    $dueAmount = $due;
    $isRefundable = false;

    $booking->paid_amount = $paymentReceived;
    $booking->due_amount = $due;
}

    $paymentInfo = [
    'subtotal'            => $booking->booking_fare - $booking->total_discount,
    'total_amount'        => $booking->total_amount,
    'canceled_fare'       => $booking->bookedRooms()->where('status', Status::ROOM_CANCELED)->sum('fare'),
    'canceled_tax_charge' => $booking->bookedRooms()->where('status', Status::ROOM_CANCELED)->sum('tax_charge'),

    'payment_received'    => $paymentReceived,

    'refunded'            => $refunded,
    'due_amount'          => $dueAmount,
    'is_refundable'       => $isRefundable
];

    $reviews = Review::reviews()
        ->where('owner_id', $booking->owner_id)
        ->with('replies', 'user')
        ->orderByDesc('id')
        ->get();

    $authUserReview = Review::reviews()
        ->where('booking_id', $id)
        ->first();

    $notify[] = 'Booking Detail';

    return responseSuccess('booking_detail', $notify, [
        'booking'            => $booking,
        'bookedRooms'        => $bookedRooms,
        'paymentInfo'        => $paymentInfo,
        'reviews'            => $reviews,
        'REVIEW_TYPE_USER'   => Status::REVIEW_TYPE_USER,
        'REVIEW_TYPE_OWNER'  => Status::REVIEW_TYPE_OWNER,
        'user_review'        => auth()->check() && !$authUserReview && $booking->status == Status::BOOKING_CHECKOUT
    ]);
}

    public function addDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return responseError('validation_error', $validator->errors());
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            $notify[] = 'Token already exists';
            return responseError('token_exists', $notify);
        }

        $deviceToken          = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token   = $request->token;
        $deviceToken->is_app  = Status::YES;
        $deviceToken->save();

        $notify[] = 'Token saved successfully';
        return responseSuccess('token_saved', $notify);
    }

    public function pushNotifications()
    {
        $notifications = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->orderBy('id', 'desc')->paginate(getPaginate());
        $notify[] = 'Push notifications';
        return responseSuccess('notifications', $notify, [
            'notifications' => $notifications,
        ]);
    }


    public function pushNotificationsRead($id)
    {
        $notification = NotificationLog::where('user_id', auth()->id())->where('sender', 'firebase')->find($id);
        if (!$notification) {
            $notify[] = 'Notification not found';
            return responseError('notification_not_found', $notify);
        }

        $notification->user_read = 1;
        $notification->save();

        $notify[] = 'Notification marked as read successfully';
        return responseSuccess('notification_read', $notify);
    }


    public function userInfo()
    {
        $notify[] = 'User information';
        return responseSuccess('user_info', $notify, ['user' => auth()->user()]);
    }

    public function deleteAccount()
    {
        $user = auth()->user();
        $user->username = 'deleted_' . $user->username;
        $user->email = 'deleted_' . $user->email;
        $user->provider_id = 'deleted_' . $user->provider_id;
        $user->save();

        $user->tokens()->delete();

        $notify[] = 'Account deleted successfully';
        return responseSuccess('account_deleted', $notify);
    }

    public function downloadAttachment($fileHash)
    {
        try {
            $filePath = decrypt($fileHash);
        } catch (\Exception $e) {
            $notify[] = 'Invalid file';
            return responseError('invalid_failed', $notify);
        }
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '-attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = 'File downloaded failed';
            return responseError('download_failed', $notify);
        }
        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET,');
            header('Access-Control-Allow-Headers: Content-Type');
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
}
