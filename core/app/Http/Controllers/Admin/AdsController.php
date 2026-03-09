<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Owner;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function all()
    {
        $pageTitle = 'All Advertisements';
        $ads = Advertisement::latest()->with('owner.hotelSetting')->paginate(getPaginate());
        $owners = Owner::owner()->active()->notExpired()->with('hotelSetting:id,owner_id,name')->orderBy('firstname')->get();
        return view('admin.ads', compact('pageTitle', 'ads', 'owners'));
    }

    public function add(Request $request, $id = 0)
    {
        $ownerIds = Owner::owner()->active()->notExpired()->pluck('id')->toArray();

        $imageValidation = $id ? 'nullable' : 'required';
        $request->validate(
            [
                'owner_id' => 'nullable|required_if:redirect_to,owner_id|in:' . implode(',', $ownerIds),
                'url'      => 'nullable|required_if:redirect_to,url|url',
                'end_date' => 'required|date_format:Y-m-d|after:today',
                'image'    => [$imageValidation, new FileTypeValidate(['png', 'jpg', 'jpg', 'gif'])]
            ],
            [
                'owner_id.required_if' => 'The hotel field is required if redirect to hotel',
                'url.required_if' => 'The URL field is required if redirect to is URL'
            ]
        );

        if ($id) {
            $ads = Advertisement::findOrFail($id);
            $message = 'Ad updated successfully';
        } else {
            $ads = new Advertisement();
            $message = 'Ad added successfully';
        }

        $ownerId = $request->owner_id;
        $url = $request->url;
        if ($request->redirect_to == 'owner_id') {
            $url = null;
        } elseif ($request->redirect_to == 'url') {
            $ownerId = 0;
        } else {
            $ownerId = 0;
            $url = null;
        }

        $ads->owner_id = $ownerId;
        $ads->url      = $url;
        $ads->end_date = $request->end_date;

        if ($request->hasFile('image')) {
            try {
                $path = getFilePath('ads');
                if ($ads->image) {
                    $filePath = $path . '/' . $ads->image;
                    if (file_exists($filePath))  unlink($filePath);
                }
                $fileName = uniqid() . time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move($path, $fileName);
                $ads->image = $fileName;
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }
        $ads->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status(Request $request, $id)
    {
        return Advertisement::changeStatus($id);
    }
}
