<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\City;
use App\Models\Country;
use App\Models\CoverPhoto;
use App\Models\Facility;
use App\Models\HotelSetting;
use App\Models\Location;
use App\Models\NotificationLog;
use App\Models\NotificationTemplate;
use App\Models\Owner;
use App\Models\RoomType;
use App\Models\Transaction;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ManageOwnersController extends Controller
{
    public function allOwners()
    {
        $pageTitle = 'All Hotels';
        $owners = $this->ownersData();
        return view('admin.owners.list', compact('pageTitle', 'owners'));
    }

    public function activeOwners()
    {
        $pageTitle = 'Active Hotels';
        $owners = $this->ownersData('active');
        return view('admin.owners.list', compact('pageTitle', 'owners'));
    }

    public function bannedOwners()
    {
        $pageTitle = 'Banned Hotels';
        $owners = $this->ownersData('banned');
        return view('admin.owners.list', compact('pageTitle', 'owners'));
    }

    public function detail($id)
    {
        $owner     = Owner::owner()->whereNotIn('status', [2, 5])->with('hotelSetting', 'hotelSetting.country', 'hotelSetting.city', 'hotelSetting.location')->findOrFail($id);
        $pageTitle = 'Vendor\'s Detail';

        $widget['total_room_type'] = RoomType::where('owner_id', $owner->id)->count();
        $widget['total_booking']   = Booking::active()->where('owner_id', $owner->id)->count();
        $widget['total_staff']     = Owner::where('parent_id', $owner->id)->count();
        $totalTransaction = Transaction::where('owner_id', $owner->id)->count();

        $countries = Country::active()->orderBy('name')->get();
        return view('admin.owners.detail', compact('pageTitle', 'owner', 'widget', 'countries'));
    }

    public function update(Request $request, $id)
    {
        $owner          = Owner::owner()->findOrFail($id);

        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|email|unique:owners,email,' . $owner->id,
            'country'   => 'required|exists:countries,code'
        ]);

        $country  = Country::where('code', $request->country)->first();

        $owner->dial_code    = $country->dial_code;
        $owner->mobile        = $country->dial_code . $request->mobile;
        $owner->country_code  = $country->code;
        $owner->firstname     = $request->firstname;
        $owner->lastname      = $request->lastname;
        $owner->email         = $request->email;
        $owner->address      = [
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'zip'           => $request->zip,
            'country'       => @$country,
        ];

        $owner->ts = $request->ts ? Status::ENABLE : Status::DISABLE;
        $owner->save();

        $notify[] = ['success', 'Vendor detail updated successfully'];
        return back()->withNotify($notify);
    }

    public function hotelSetting($id)
    {
        $owner     = Owner::owner()->with('hotelSetting')->findOrFail($id);
        $pageTitle = 'Hotel Setting of ' . $owner->fullname;
        $setting   = $owner->hotelSetting;
        $coverPhotos = CoverPhoto::where('owner_id', $owner->id)->get();
        $amenities = Amenity::active()->get();
        $facilities = Facility::active()->get();

        $countries = Country::active()->orderBy('name')
            ->whereHas('cities', function ($city) {
                $city->whereHas('locations');
            })
            ->with(['cities' => function ($cities) {
                $cities->whereHas('locations')->with('locations');
            }])->get();

        $images      = [];
        foreach ($coverPhotos as $key => $image) {
            $img['id']  = $image->id;
            $img['src'] = getImage(getFilePath('coverPhoto') . '/' . $image->cover_photo);
            $images[]   = $img;
        }

        abort_if(request()->step && !in_array(request()->step, [1, 2, 3, 4]), 404);
        $step = request()->step ??  1;

        return view('admin.owners.form', compact('pageTitle', 'setting', 'countries', 'images', 'step', 'amenities', 'facilities'));
    }

    public function updateHotelSetting(Request $request, $id)
    {
        $request->validate([
            'step' => 'required|in:1,2,3,4',
        ]);
        $method = [1 =>  'stepOne', 2 =>  'stepTwo', 3 => 'stepThree', 4 => 'stepFour'];
        $method = $method[$request->step];
        return $this->$method($request, $id);
    }

    private function stepOne($request, $id)
    {
        $this->hotelSettingValidation($request, $id);

        $hotelSetting                         = HotelSetting::findOrFail($id);
        $hotelSetting->name                   = $request->name;
        $hotelSetting->star_rating            = $request->star_rating;
        $hotelSetting->country_id             = $request->country_id;
        $hotelSetting->city_id                = $request->city_id;
        $hotelSetting->location_id            = $request->location_id;
        $hotelSetting->hotel_address          = $request->hotel_address;
        $hotelSetting->latitude               = $request->latitude;
        $hotelSetting->longitude              = $request->longitude;
        $hotelSetting->tax_name               = $request->tax_name;
        $hotelSetting->tax_percentage         = $request->tax_percentage;
        $hotelSetting->checkin_time           = $request->checkin_time;
        $hotelSetting->checkout_time          = $request->checkout_time;
        $hotelSetting->upcoming_checkin_days  = $request->upcoming_checkin_days;
        $hotelSetting->upcoming_checkout_days = $request->upcoming_checkout_days;
        $hotelSetting->description            = $request->description;
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('admin.owners.hotel.setting', $hotelSetting->owner_id) . '?step=2';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function stepTwo($request, $id)
    {
        $this->hotelSettingValidation($request, $id, 2);
        $hotelSetting = HotelSetting::findOrFail($id);

        if ($request->hasFile('image')) {
            try {
                $hotelSetting->image = fileUploader($request->image, getFilePath('hotelImage'), getFileSize('hotelImage'), @$hotelSetting->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('cover_image')) {
            try {
                $hotelSetting->cover_image = fileUploader($request->cover_image, getFilePath('hotelCoverImage'), getFileSize('hotelCoverImage'), @$hotelSetting->cover_image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }

        $this->uploadCoverPhoto($request, $hotelSetting->owner_id);
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('admin.owners.hotel.setting', $hotelSetting->owner_id) . '?step=3';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function uploadCoverPhoto($request, $ownerId)
    {
        $path = getFilePath('coverPhoto');
        $owner = Owner::where('id', $ownerId)->with('coverPhotos')->first();
        $previousImages = $owner->coverPhotos->pluck('id')->toArray();
        $imageToRemove  = array_values(array_diff($previousImages, $request->old ?? []));

        foreach ($imageToRemove as $item) {
            $coverPhoto   = CoverPhoto::where('owner_id', $owner->id)->find($item);
            @unlink($path . '/' . $coverPhoto->cover_photo);
            $coverPhoto->delete();
        }

        if ($request->hasFile('cover_photos')) {
            $coverPhotos = [];

            foreach ($request->file('cover_photos') as $file) {
                try {
                    $coverPhoto              = new CoverPhoto();
                    $coverPhoto->cover_photo = fileUploader($file, $path);
                    $coverPhotos[]           = $coverPhoto;
                } catch (\Exception $e) {
                    throw ValidationException::withMessages(['error' =>  'Couldn\'t upload the cover photo']);
                }
            }
            $owner->coverPhotos()->saveMany($coverPhotos);
        }
    }

    private function stepThree($request, $id)
    {
        $this->hotelSettingValidation($request, $id, 3);

        $hotelSetting = HotelSetting::findOrFail($id);
        $hotelSetting->complements = $request->complements;

        $hotelSetting->amenities()->sync($request->amenities);
        $hotelSetting->facilities()->sync($request->facilities);
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('admin.owners.hotel.setting', $hotelSetting->owner_id) . '?step=4';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function stepFour($request, $id)
    {
        $this->hotelSettingValidation($request, $id, 4);

        $hotelSetting                        = HotelSetting::findOrFail($id);
        $hotelSetting->cancellation_policy   = $request->cancellation_policy;
        $hotelSetting->instructions          = $request->instructions;
        $hotelSetting->early_check_in_policy = $request->early_check_in_policy;
        $hotelSetting->child_policy          = $request->child_policy;
        $hotelSetting->pet_policy            = $request->pet_policy;
        $hotelSetting->other_policy          = $request->other_policy;
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('admin.owners.hotel.setting', $hotelSetting->owner_id) . '?step=1';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function hotelSettingValidation($request, $id, $step = 1)
    {
        if ($step == 1) {
            $countryIds  = Country::active()->pluck('id')->toArray();
            $cityIds     = City::where('country_id', $request->country_id)->pluck('id')->toArray();
            $locationIds = Location::where('city_id', $request->city_id)->pluck('id')->toArray();
            $request->validate([
                'name'                   => 'required|max:255|unique:hotel_settings,name,' . $id,
                'star_rating'            => 'required|integer|min:1|max:' . gs('max_star_rating'),
                'country_id'             => 'required|integer|in:' . implode(',', $countryIds),
                'city_id'                => 'required|in:' . implode(',', $cityIds),
                'location_id'            => 'required|in:' . implode(',', $locationIds),
                'hotel_address'          => 'required|string',
                'latitude'               => 'required|numeric|between:-90,90',
                'longitude'              => 'required|numeric|between:-180,180',
                'tax_name'               => 'required',
                'tax_percentage'         => 'required|numeric|gte:0',
                'checkin_time'           => 'required|date_format:H:i',
                'checkout_time'          => 'required|date_format:H:i',
                'upcoming_checkin_days'  => 'required|numeric|min:1',
                'upcoming_checkout_days' => 'required|numeric|min:1',
                'description'            => 'required|string',
            ]);
        } elseif ($step == 2) {
            $request->validate([
                'image'          => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
                'cover_image'    => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
                'cover_photos'   => 'nullable|array',
                'cover_photos.*' => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            ]);
        } elseif ($step == 3) {
            $request->validate([
                'facilities'    => 'nullable|array',
                'facilities.*'  => 'integer|exists:facilities,id',
                'complements'   => 'nullable|array',
                'complements.*' => 'string',
                'amenities'     => 'nullable|array',
                'amenities.*'   => 'integer|exists:amenities,id',
            ]);
        } elseif ($step == 4) {
            $request->validate([
                'cancellation_policy'   => 'required',
                'instructions'          => 'nullable|array',
                'instructions.*'        => 'string',
                'early_check_in_policy' => 'nullable|string|max:255',
                'child_policy'          => 'nullable|array',
                'child_policy.*'        => 'string',
                'pet_policy'            => 'nullable|array',
                'pet_policy.*'          => 'string',
                'other_policy'          => 'nullable|array',
                'other_policy.*'        => 'string',
            ]);
        }
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'act' => 'required|in:add,sub',
            'remark' => 'required|string|max:255',
        ]);

        $owner = Owner::owner()->findOrFail($id);
        $amount = $request->amount;
        $trx = getTrx();

        $transaction = new Transaction();

        if ($request->act == 'add') {
            $owner->balance += $amount;

            $transaction->trx_type = '+';
            $transaction->remark = 'balance_add';

            $notifyTemplate = 'BAL_ADD';

            $notify[] = ['success', gs('cur_sym') . $amount . ' added successfully'];
        } else {
            if ($amount > $owner->balance) {
                $notify[] = ['error', $owner->fullname . ' doesn\'t have sufficient balance.'];
                return back()->withNotify($notify);
            }

            $owner->balance -= $amount;

            $transaction->trx_type = '-';
            $transaction->remark = 'balance_subtract';

            $notifyTemplate = 'BAL_SUB';
            $notify[] = ['success', gs('cur_sym') . $amount . ' subtracted successfully'];
        }

        $owner->save();

        $transaction->owner_id = $owner->id;
        $transaction->amount = $amount;
        $transaction->post_balance = $owner->balance;
        $transaction->charge = 0;
        $transaction->trx =  $trx;
        $transaction->details = $request->remark;
        $transaction->save();

notify($owner, $notifyTemplate, [
    'trx' => $trx,
    'amount' => showAmount($amount, currencyFormat: false),
    'remark' => $request->remark,
    'post_balance' => showAmount($owner->balance, currencyFormat: false)
]);

// Dashboard notification
if ($request->act == 'add') {
    ownerNotify(
        $owner->id,
        'Admin added ' . gs('cur_sym') . $amount . ' to your wallet',
        route('owner.report.transaction')
    );
} else {
    ownerNotify(
        $owner->id,
        'Admin deducted ' . gs('cur_sym') . $amount . ' from your wallet',
        route('owner.report.transaction')
    );
}

        return back()->withNotify($notify);
    }

    public function status(Request $request, $id)
    {
        $owner = Owner::owner()->findOrFail($id);
        if ($owner->status == Status::USER_ACTIVE) {
            $request->validate([
                'reason' => 'required|string|max:255'
            ]);

            $owner->status = Status::USER_BAN;
            $owner->ban_reason = $request->reason;
            $notify[] = ['success', 'Vendor banned successfully'];
        } else {
            $owner->status = Status::USER_ACTIVE;
            $owner->ban_reason = null;
            $notify[] = ['success', 'Vendor unbanned successfully'];
        }

        $owner->save();
        return back()->withNotify($notify);
    }

    public function notificationLog($id)
    {
        $owner = Owner::findOrFail($id);
        $pageTitle = 'Notifications Sent to ' . $owner->fullname;
        $logs = NotificationLog::where('owner_id', $id)->with('owner')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs', 'owner'));
    }

    public function showNotificationSingleForm($id)
    {
        $owner = Owner::owner()->findOrFail($id);
        $general = gs();
        if (!$general->en && !$general->sn) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.owners.detail', $owner->id)->withNotify($notify);
        }
        $pageTitle = 'Send Notification to ' . $owner->fullname;
        return view('admin.owners.notification_single', compact('pageTitle', 'owner'));
    }

    public function sendNotificationSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'subject' => 'required|string',
        ]);

        $owner = Owner::owner()->findOrFail($id);
        notify($owner, 'DEFAULT', [
            'subject' => $request->subject,
            'message' => $request->message,
        ]);
        $notify[] = ['success', 'Notification sent successfully'];
        return back()->withNotify($notify);
    }


    public function showNotificationAllForm()
    {
        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $notifyToOwner = Owner::notifyToOwner();
        $owners = Owner::owner()->active()->count();
        $pageTitle    = 'Notification to Verified Vendors';

        if (session()->has('SEND_NOTIFICATION') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION');
        }

        return view('admin.owners.notification_all', compact('pageTitle', 'owners', 'notifyToOwner'));
    }

    public function sendNotificationAll(Request $request)
    {
        $request->validate([
            'via'                          => 'required|in:email,sms,push',
            'message'                      => 'required',
            'subject'                      => 'required_if:via,email,push',
            'start'                        => 'required|integer|gte:1',
            'batch'                        => 'required|integer|gte:1',
            'being_sent_to'                => 'required',
            'cooling_time'                 => 'required|integer|gte:1',
            'number_of_top_deposited_owner' => 'required_if:being_sent_to,topDepositedOwners|integer|gte:0',
            'number_of_days'               => 'required_if:being_sent_to,notLoginOwners|integer|gte:0',
            'image'                        => ["nullable", 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ], [
            'number_of_days.required_if'               => "Number of days field is required",
            'number_of_top_deposited_owner.required_if' => "Number of top deposited vendor field is required",
        ]);

        if (!gs('en') && !gs('sn') && !gs('pn')) {
            $notify[] = ['warning', 'Notification options are disabled currently'];
            return to_route('admin.dashboard')->withNotify($notify);
        }

        $template = NotificationTemplate::where('act', 'DEFAULT')->where($request->via . '_status', Status::ENABLE)->exists();
        if (!$template) {
            $notify[] = ['warning', 'Default notification template is not enabled'];
            return back()->withNotify($notify);
        }

        if ($request->being_sent_to == 'selectedOwners') {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['owner' => session()->get('SEND_NOTIFICATION')['owner']]);
            } else {
                if (!$request->owner || !is_array($request->owner) || empty($request->owner)) {
                    $notify[] = ['error', "Ensure that the vendor field is populated when sending an email to the designated vendor group"];
                    return back()->withNotify($notify);
                }
            }
        }

        $scope          = $request->being_sent_to;
        $ownerQuery      = Owner::owner()->oldest()->active()->$scope();

        if (session()->has("SEND_NOTIFICATION")) {
            $totalOwnerCount = session('SEND_NOTIFICATION')['total_owner'];
        } else {
            $totalOwnerCount = (clone $ownerQuery)->count() - ($request->start - 1);
        }

        if ($totalOwnerCount <= 0) {
            $notify[] = ['error', "Notification recipients were not found among the selected vendor base."];
            return back()->withNotify($notify);
        }

        $imageUrl = null;

        if ($request->via == 'push' && $request->hasFile('image')) {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['image' => session()->get('SEND_NOTIFICATION')['image']]);
            }
            if ($request->hasFile("image")) {
                $imageUrl = fileUploader($request->image, getFilePath('push'));
            }
        }

        $owners = (clone $ownerQuery)->skip($request->start - 1)->limit($request->batch)->get();

        foreach ($owners as $owner) {
            notify($owner, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ], [$request->via], pushImage: $imageUrl);
        }

        return $this->sessionForNotification($totalOwnerCount, $request);
    }

    private function sessionForNotification($totalOwnerCount, $request)
    {
        if (session()->has('SEND_NOTIFICATION')) {
            $sessionData                = session("SEND_NOTIFICATION");
            $sessionData['total_sent'] += $sessionData['batch'];
        } else {
            $sessionData               = $request->except('_token');
            $sessionData['total_sent'] = $request->batch;
            $sessionData['total_owner'] = $totalOwnerCount;
        }

        $sessionData['start'] = $sessionData['total_sent'] + 1;

        if ($sessionData['total_sent'] >= $totalOwnerCount) {
            session()->forget("SEND_NOTIFICATION");
            $message = ucfirst($request->via) . " notifications were sent successfully";
            $url     = route("admin.owners.notification.all");
        } else {
            session()->put('SEND_NOTIFICATION', $sessionData);
            $message = $sessionData['total_sent'] . " " . $sessionData['via'] . "  notifications were sent successfully";
            $url     = route("admin.owners.notification.all") . "?email_sent=yes";
        }
        $notify[] = ['success', $message];
        return redirect($url)->withNotify($notify);
    }

    public function countBySegment($methodName)
    {
        return Owner::owner()->active()->$methodName()->count();
    }

    public function list()
    {
        $query = Owner::owner()->active();

        if (request()->search) {
            $query->where(function ($q) {
                $q->where('email', 'like', '%' . request()->search . '%')->orWhere('email', 'like', '%' . request()->search . '%');
            });
        }

        $owners = $query->orderBy('id', 'desc')->paginate(getPaginate());

        return response()->json([
            'success'  => true,
            'owners'   => $owners,
            'more'     => $owners->hasMorePages()
        ]);
    }

    private function ownersData($scope = null)
    {
        $owners = Owner::owner();

        if ($scope) {
            $owners = $owners->$scope();
        } else {
            $owners = $owners->whereNotIn('status', [2, 5]);
        }

        if (request()->search) {
            $search = request()->search;
            $owners = $owners->where('email', 'like', "%$search%")->orWhere(function ($query) use ($search) {
                $query->whereRaw("CONCAT(firstname, ' ',lastname) LIKE?", ["%$search%"]);
            })->orWhere(function ($query) use ($search) {
                $query->whereHas('hotelSetting', function ($hotelSetting) use ($search) {
                    $hotelSetting->where('name', 'like', "%$search%");
                });
            });
        }
        return $owners->latest()->with('hotelSetting', 'hotelSetting.country', 'hotelSetting.city', 'hotelSetting.location')->paginate(getPaginate());
    }

    public function updateFeatureStatus($id)
    {
        $owner = Owner::findOrFail($id);
        $owner->is_featured = $owner->is_featured == Status::YES ? Status::NO : Status::YES;
        $owner->save();

        $notify[] = ['success', 'Feature status updated successfully'];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        Auth::guard('owner')->loginUsingId($id);
        return to_route('owner.dashboard');
    }
}
