<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\OwnerNotification;
use App\Models\Room;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\PaymentLog;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Deposit;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $pageTitle                          = 'Dashboard';
        $todaysBookedRoomIds                = BookedRoom::active()->currentOwner('booking')->where('booked_for', todaysDate())->pluck('room_id')->toArray();
        $widget['today_booked']             = count($todaysBookedRoomIds);

        $widget['today_available']          = Room::currentOwner()->active()->whereNotIn('id', $todaysBookedRoomIds)->count();


        $widget['total']                    = Booking::currentOwner()->count();
        $widget['active']                   = Booking::currentOwner()->active()->count();
        $widget['pending_checkin']          = Booking::currentOwner()->active()->KeyNotGiven()->whereDate('check_in', '<=', now())->count();
        $widget['delayed_checkout']         = Booking::currentOwner()->delayedCheckout()->count();
        $upcomingCheckinDays = (int) hotelSetting('upcoming_checkin_days');
$upcomingCheckoutDays = (int) hotelSetting('upcoming_checkout_days');

$widget['upcoming_checkin'] = Booking::currentOwner()->active()
    ->whereDate('check_in', '>', now())
    ->whereDate('check_in', '<=', now()->addDays($upcomingCheckinDays))
    ->count();

$widget['upcoming_checkout'] = Booking::currentOwner()->active()
    ->whereDate('check_out', '>', now())
    ->whereDate('check_out', '<=', now()->addDays($upcomingCheckoutDays))
    ->count();
        return view('owner.dashboard', compact('pageTitle', 'widget'));
    }

    public function bookingReport(Request $request)
    {

        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }
        $bookings = BookedRoom::currentOwner('booking')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->whereIn('status', [Status::ROOM_ACTIVE, Status::ROOM_CHECKOUT])
            ->selectRaw("SUM( CASE WHEN status IN(1,9) THEN fare END) as amount")
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'bookingAmounts' => getAmount($bookings->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }

        $data = collect($data);
        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Booking Amount',
                'data' => $data->pluck('bookingAmounts')
            ]
        ];

        return response()->json($report);
    }

    public function paymentReport(Request $request)
    {
        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $plusTransactions = PaymentLog::where('type', 'BOOKING_PAYMENT_RECEIVED')
            ->currentOwner()
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $minusTransactions = PaymentLog::where('type', 'BOOKING_PAYMENT_RETURNED')
            ->currentOwner()
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();


        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'credits' => getAmount($plusTransactions->where('created_on', $date)->first()?->amount ?? 0),
                'debits' => getAmount($minusTransactions->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }

        $data = collect($data);
        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Received Amount',
                'data' => $data->pluck('credits')
            ],
            [
                'name' => 'Returned Amount',
                'data' => $data->pluck('debits')
            ]
        ];

        return response()->json($report);
    }

    private function getAllDates($startDate, $endDate)
    {
        $dates = [];
        $currentDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-F-Y');
            $currentDate->modify('+1 day');
        }

        return $dates;
    }

    private function  getAllMonths($startDate, $endDate)
    {
        if ($endDate > now()) {
            $endDate = now()->format('Y-m-d');
        }

        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $months = [];

        while ($startDate <= $endDate) {
            $months[] = $startDate->format('F-Y');
            $startDate->modify('+1 month');
        }

        return $months;
    }

    public function show2faForm()
    {
        $ga = new GoogleAuthenticator();
        $owner = authOwner();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($owner->email . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Setting';
        return view('owner.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $owner = authOwner();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);

        $response = verifyG2fa($owner, $request->code, $request->key);
        if ($response) {
            $owner->tsc = $request->key;
            $owner->ts = 1;
            $owner->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $owner = authOwner();
        $response = verifyG2fa($owner, $request->code);
        if ($response) {
            $owner->tsc = null;
            $owner->ts = 0;
            $owner->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $owner = authOwner();
        return view('owner.profile', compact('pageTitle', 'owner'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'firstname' => 'required|max:40',
            'lastname' => 'required|max:40',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);
        $owner = authOwner();

        if ($request->hasFile('image')) {
            try {
                $old = $owner->image ?: null;
                $owner->image = fileUploader($request->image, getFilePath('ownerProfile'), getFileSize('ownerProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $owner->firstname  = $request->firstname;
        $owner->lastname  = $request->lastname;
        $owner->email = $request->email;
        $owner->save();

        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('owner.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $owner = authOwner();
        return view('owner.password', compact('pageTitle', 'owner'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = authOwner();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('owner.password')->withNotify($notify);
    }

    public function notifications()
    {
        $owner = authOwner();
        $notifications = OwnerNotification::where('owner_id', $owner->id)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $hasUnread = $notifications->where('is_read', Status::NO)->count() > 0;
        $pageTitle = 'Notifications';
        return view('owner.notifications', compact('pageTitle', 'notifications', 'hasUnread'));
    }

    public function notificationRead($id)
{
    $notification = OwnerNotification::findOrFail($id);

    $notification->is_read = Status::YES;
    $notification->save();

    $url = $notification->click_url;

    if (!$url || $url == '#') {
        return redirect()->route('owner.dashboard');
    }

    // Handle booking request notification
    if (str_contains($url, 'booking/request/detail')) {

        $parts = explode('/', $url);
        $requestId = end($parts);

        $bookingRequest = \App\Models\BookingRequest::find($requestId);

        // Request still exists AND still pending
        if ($bookingRequest && $bookingRequest->status == Status::BOOKING_REQUEST_INITIAL) {
            return redirect($url);
        }

        // Otherwise redirect to booking details
        $booking = \App\Models\Booking::where('user_id', $notification->user_id)
            ->latest()
            ->first();

        if ($booking) {
            return redirect()->route('owner.booking.details', $booking->id);
        }

        return redirect()->route('owner.booking.active');
    }

    return redirect($url);
}

    public function readAll()
    {
        OwnerNotification::where('is_read', Status::NO)->update([
            'is_read' => Status::YES
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function singleNotificationDelete(Request $request, $id)
    {
        $owner = authOwner();
        $notification = OwnerNotification::where('owner_id', $owner->id)->findOrFail($id);
        $notification->delete();
        $notify[] = ['success', 'Notification deleted successfully'];
        return back()->withNotify($notify);
    }

    public function allNotificationDelete(Request $request)
    {
        $owner = authOwner();
        OwnerNotification::where('owner_id', $owner->id)->delete();
        $notify[] = ['success', 'All notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function paymentHistory()
    {
        $pageTitle = 'Subscription History';
        $deposits  = Deposit::where('status', '!=', Status::PAYMENT_INITIATE)->where('pay_for_month', '>', 0)->where('owner_id', getOwnerParentId())->searchable(['trx'])->dateFilter()->with('gateway')->latest()->paginate(getPaginate());

        return view('owner.payment_history', compact('pageTitle', 'deposits'));
    }

    public function updateAutoPaymentStatus(Request $request)
    {
        $owner = authOwner();
        $owner->auto_payment = $request->auto_payment ? 1 : 0;
        $owner->save();

        $notify[] = ['success', 'Auto payment status updated successfully'];
        return back()->withNotify($notify);
    }
}
