<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\HotelSetting;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;

class VendorRequestController extends Controller
{

    public function vendorRequests()
    {
        $pageTitle = 'Vendor Requests';
        $owners = Owner::owner()->ownerRequest()->searchable(['firstname', 'lastname', 'email', 'mobile'])->with('hotelSetting', 'hotelSetting.location', 'hotelSetting.city', 'hotelSetting.country')->orderByDesc('id')->paginate(getPaginate());
        return view('admin.vendor_request.list', compact('pageTitle', 'owners'));
    }

    public function requestDetail($id){
        $owner     = Owner::ownerRequest()->with('hotelSetting', 'hotelSetting.country', 'hotelSetting.city', 'hotelSetting.location')->findOrFail($id);
        $pageTitle = 'Vendor Request';

        return view('admin.vendor_request.detail', compact('pageTitle', 'owner'));
    }

    public function rejectRequest($id)
    {
        $owner = Owner::ownerRequest()->findOrFail($id);
        HotelSetting::where('owner_id', $owner->id)->delete();

        notify($owner, 'OWNER_REQUEST_REJECTED', [
            'username' => $owner->fullname
        ], createLog: false);

        $owner->delete();

        $notify[] = ['success', 'Request rejected successfully'];
        return to_route('admin.vendor.request')->withNotify($notify);
    }

    public function approveRequest($id)
    {
        $owner = Owner::ownerRequest()->findOrFail($id);
        $password = generateStrongPassword();

        $owner->password = Hash::make($password);
        $owner->status = Status::USER_ACTIVE;
        $owner->save();

        notify($owner, 'OWNER_REQUEST_APPROVED', [
            'email'  => $owner->email,
            'password' => $password,
            'login_url' => route('owner.login')
        ]);

        $notify[] = ['success', 'Request approved successfully'];
        return to_route('admin.vendor.request')->withNotify($notify);
    }
}
