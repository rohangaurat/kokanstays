<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Facilities';
        $facilities = Facility::latest()->paginate(getPaginate());

        return view('admin.facilities', compact('pageTitle', 'facilities'));
    }

    public function add(Request $request, $id = 0)
    {
        if ($id) {
            $imageValidation = 'nullable';
        } else {
            $imageValidation = 'required';
        }

        $request->validate([
            'name'     => 'required|string|unique:facilities,name,' . $id,
            'image'     => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $facility          = Facility::findOrFail($id);
            $notification       = 'Facility updated successfully';
        } else {
            $facility          = new Facility();
            $notification       = 'Facility added successfully';
        }

        $facility->name   = $request->name;

        if ($request->hasFile('image')) {
            try {
                $facility->image = fileUploader($request->image, getFilePath('facility'), getFileSize('facility'), @$facility->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }

        $facility->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Facility::changeStatus($id);
    }
}
