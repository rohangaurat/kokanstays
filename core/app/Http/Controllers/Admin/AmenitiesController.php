<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class AmenitiesController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Amenities';
        $amenities = Amenity::orderBy('title')->Paginate(getPaginate());
        return view('admin.amenities', compact('pageTitle', 'amenities'));
    }

    public function save(Request $request, $id = 0)
    {
        if ($id) {
            $imageValidation = 'nullable';
        } else {
            $imageValidation = 'required';
        }

        $request->validate([
            'title'     => 'required|string|unique:amenities,title,' . $id,
            'image'     => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $amenity          = Amenity::findOrFail($id);
            $notification       = 'Amenity updated successfully';
        } else {
            $amenity          = new Amenity();
            $notification       = 'Amenity added successfully';
        }
        $amenity->title   = $request->title;

        if ($request->hasFile('image')) {
            try {
                $amenity->image = fileUploader($request->image, getFilePath('amenity'), getFileSize('amenity'), @$amenity->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }

        $amenity->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Amenity::changeStatus($id);
    }
}
