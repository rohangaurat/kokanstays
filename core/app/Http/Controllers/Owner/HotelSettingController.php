<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\CoverPhoto;
use App\Models\HotelSetting;
use App\Models\PaymentSystem;
use App\Models\Owner;
use App\Models\Amenity;
use App\Models\Facility;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HotelSettingController extends Controller
{
    public function index()
    {
        $pageTitle = 'Hotel Setting';
        $setting = HotelSetting::where('owner_id', getOwnerParentId())->with('city')->first();
        $coverPhotos = CoverPhoto::where('owner_id', getOwnerParentId())->get();
        $images      = [];
        foreach ($coverPhotos as $key => $image) {
            $img['id']  = $image->id;
            $img['src'] = getImage(getFilePath('coverPhoto') . '/' . $image->cover_photo);
            $images[]   = $img;
        }
        $amenities = Amenity::active()->get();
        $facilities = Facility::active()->get();

        abort_if(request()->step && !in_array(request()->step, [1, 2, 3, 4]), 404);
        $step = request()->step ??  1;

        if($setting->complete_step < $step){
            $redirectUrl = route('owner.hotel.setting.index') . '?step=1';
            return redirect($redirectUrl);
        }

        return view('owner.hotel.form', compact('pageTitle', 'setting', 'images', 'facilities', 'amenities', 'step'));
    }

    public function update(Request $request, $id)
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
        $this->validation($request);

        $hotelSetting = HotelSetting::currentOwner()->findOrFail($id);
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
        $hotelSetting->complete_step          = 2;
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('owner.hotel.setting.index') . '?step=2';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function stepTwo($request, $id)
    {
        $this->validation($request, 2);
        $hotelSetting = HotelSetting::currentOwner()->findOrFail($id);

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

        $this->uploadCoverPhoto($request);
        $hotelSetting->complete_step = 3;
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('owner.hotel.setting.index') . '?step=3';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function stepThree($request, $id)
    {
        $this->validation($request, 3);

        $hotelSetting = HotelSetting::currentOwner()->findOrFail($id);
        $hotelSetting->complements = $request->complements;

        $hotelSetting->amenities()->sync($request->amenities);
        $hotelSetting->facilities()->sync($request->facilities);

        $hotelSetting->complete_step = 4;
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('owner.hotel.setting.index') . '?step=4';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function stepFour($request, $id)
    {
        $this->validation($request, 4);

        $hotelSetting = HotelSetting::currentOwner()->findOrFail($id);
        $hotelSetting->cancellation_policy    = $request->cancellation_policy;
        $hotelSetting->instructions           = $request->instructions;
        $hotelSetting->early_check_in_policy  = $request->early_check_in_policy;
        $hotelSetting->child_policy           = $request->child_policy;
        $hotelSetting->pet_policy             = $request->pet_policy;
        $hotelSetting->other_policy           = $request->other_policy;
        $hotelSetting->save();

        $notify[] = ['success', 'Hotel setting updated successfully'];
        $redirectUrl = route('owner.hotel.setting.index') . '?step=1';
        return redirect($redirectUrl)->withNotify($notify);
    }

    private function validation($request, $step = 1)
    {
        if ($step == 1) {
            $request->validate([
                'hotel_address'          => 'required|string',
                'latitude'               => 'required|numeric|between:-90,90',
                'longitude'              => 'required|numeric|between:-180,180',
                'tax_name'               => 'required',
                'tax_percentage'         => 'required|numeric|gte:0',
                'checkin_time'           => 'required|date_format:H:i',
                'checkout_time'          => 'required|date_format:H:i',
                'upcoming_checkin_days'  => 'required|numeric|min:1',
                'upcoming_checkout_days' => 'required|numeric|min:1',
                'description'            => 'required|string'
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
                'cancellation_policy'    => 'required',
                'instructions'           => 'nullable|array',
                'instructions.*'         => 'string',
                'early_check_in_policy'  => 'nullable|string|max:255',
                'child_policy'           => 'nullable|array',
                'child_policy.*'         => 'string',
                'pet_policy'             => 'nullable|array',
                'pet_policy.*'           => 'string',
                'other_policy'           => 'nullable|array',
                'other_policy.*'         => 'string',
            ]);
        }
    }

    private function uploadCoverPhoto($request)
    {
        $path = getFilePath('coverPhoto');
        $owner = Owner::where('id', getOwnerParentId())->with('coverPhotos')->first();
        $previousImages = $owner->coverPhotos->pluck('id')->toArray();
        $imageToRemove  = array_values(array_diff($previousImages, $request->old ?? []));

        foreach ($imageToRemove as $item) {
            $coverPhoto   = CoverPhoto::currentOwner()->find($item);
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

    public function paymentSystems()
    {
        $pageTitle = 'Payment Systems';
        $paymentSystems = PaymentSystem::currentOwner()->get();
        return view('owner.hotel.payment_systems', compact('pageTitle', 'paymentSystems'));
    }

    public function addPaymentSystem(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|'
        ]);

        $exist = PaymentSystem::currentOwner()->where('name', $request->name)->where('id', '!=', $id)->exists();
        if ($exist) {
            $notify[] = ['error', 'The name already exists'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $paymentSystem = PaymentSystem::currentOwner()->findOrFail($id);
            $notification = 'Payment system updated successfully';
        } else {
            $paymentSystem = new PaymentSystem();
            $paymentSystem->owner_id = getOwnerParentId();
            $notification = 'Payment system added successfully';
        }

        $paymentSystem->name = $request->name;
        $paymentSystem->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function updatePaymentSystemStatus($id)
    {
        $paymentSystem = PaymentSystem::currentOwner()->findOrFail($id);

        $paymentSystem->status = $paymentSystem->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $paymentSystem->save();

        $notify[] = ['success', 'Status updated successfully'];
        return back()->withNotify($notify);
    }
}
