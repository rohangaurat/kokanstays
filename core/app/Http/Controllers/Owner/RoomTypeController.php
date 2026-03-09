<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\BedType;
use App\Models\Facility;
use App\Models\RoomTypeImage;

class RoomTypeController extends Controller
{
    public function index()
    {
        $pageTitle   = 'All Room Types';
        $typeList    = RoomType::currentOwner()->with('amenities')->withCount('rooms')->latest()->paginate(getPaginate());
        return view('owner.hotel.room_type.list', compact('pageTitle', 'typeList'));
    }

    public function create()
    {
        $pageTitle   = 'Add Room Type';
        $amenities   = Amenity::active()->get();

        $bedTypes    = BedType::orderBy('name')->get();
        $facilities  = Facility::active()->get();

        return view('owner.hotel.room_type.create', compact('pageTitle', 'amenities', 'bedTypes', 'facilities'));
    }

    public function edit($id)
    {
        $roomType    = RoomType::currentOwner()->with('amenities', 'facilities', 'images')->findOrFail($id);
        $pageTitle   = 'Update Room Type -' . $roomType->name;
        $amenities   = Amenity::active()->get();
        $bedTypes    = BedType::orderBy('name')->get();
        $facilities  = Facility::active()->get();
        $images      = [];

        foreach ($roomType->images as $key => $image) {
            $img['id']  = $image->id;
            $img['src'] = getImage(getFilePath('roomTypeImage') . '/' . $image->image);
            $images[]   = $img;
        }
        return view('owner.hotel.room_type.create', compact('pageTitle', 'roomType', 'amenities', 'bedTypes', 'images', 'facilities'));
    }

    public function save(Request $request, $id = 0)
    {
        $this->validation($request, $id);

        $exist = RoomType::currentOwner()->where('id', '!=', $id)->where('name', $request->name)->exists();
        if ($exist) {
            $notify[] = ['error', 'Name already exist'];
            return back()->withNotify($notify);
        }
        $bedArray         = array_values($request->bed ?? []);

        if ($id) {
            $roomType         = RoomType::currentOwner()->findOrFail($id);
            $notification     = 'Room type updated successfully';
        } else {
            $roomType         = new RoomType();
            $roomType->owner_id = getOwnerParentId();
            $notification     = 'Room type added successfully';
        }

        $roomType->name                = $request->name;
        $roomType->total_adult         = $request->total_adult;
        $roomType->total_child         = $request->total_child;
        $roomType->fare                = $request->fare;
        $roomType->discount_percentage = $request->discount_percentage ?? 0;
        $roomType->description         = $request->description;
        $roomType->beds                = $bedArray;
        $roomType->is_featured         = $request->is_featured ? 1 : 0;
        $roomType->cancellation_fee    = $request->cancellation_fee ?? 0;
        $roomType->cancellation_policy = $request->cancellation_policy;
        $roomType->save();

        $roomType->amenities()->sync($request->amenities);
        $roomType->facilities()->sync($request->facilities);
        $this->insertImages($request, $roomType);

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }


    protected function validation($request, $id)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'total_adult'          => 'required|integer|gte:0',
            'total_child'          => 'required|integer|gte:0',
            'fare'                 => 'required|numeric|gt:0',
            'discount_percentage'  => 'nullable|numeric|gte:0',
            'amenities'            => 'nullable|array',
            'amenities.*'          => 'integer|exists:amenities,id',
            'facilities'           => 'nullable|array',
            'facilities.*'         => 'integer|exists:facilities,id',
            'total_bed'            => 'required|gt:0',
            'bed'                  => 'required|array',
            'bed.*'                => 'exists:bed_types,name',
            'cancellation_policy'  => 'nullable|string',
            'cancellation_fee'     => 'nullable|numeric|gte:0|lt:fare'
        ]);
    }

    protected function insertImages($request, $roomType)
    {
        $path = getFilePath('roomTypeImage');
        $this->removeImages($request, $roomType, $path);

        if ($request->hasFile('images')) {
            // $size = getFileSize('roomTypeImage');
            $images = [];

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            foreach ($request->file('images') as $file) {
                try {
                    $name = fileUploader($file, $path);
                    $roomTypeImage        = new RoomTypeImage();
                    $roomTypeImage->image = $name;
                    $images[] = $roomTypeImage;
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload the logo'];
                    return back()->withNotify($notify);
                }
            }

            $roomType->images()->saveMany($images);
        }
    }

    protected function removeImages($request, $roomType, $path)
    {
        $previousImages = $roomType->images->pluck('id')->toArray();
        $imageToRemove  = array_values(array_diff($previousImages, $request->old ?? []));

        foreach ($imageToRemove as $item) {
            $roomImage   = RoomTypeImage::find($item);
            @unlink($path . '/' . $roomImage->image);
            $roomImage->delete();
        }
    }

    public function status($id)
    {
        $roomType = RoomType::currentOwner()->findOrFail($id);
        $roomType->status = $roomType->status == Status::ENABLE ? Status::DISABLE : STATUS::ENABLE;
        $roomType->save();

        $notify[] = ['success', 'Status updated successfully'];
        return back()->withNotify($notify);
    }
}
