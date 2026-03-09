<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BedType;

class BedTypeController extends Controller
{
    public function index()
    {
        $pageTitle   = "Bed List";
        $bedTypes = BedType::latest()->paginate(getPaginate());
        return view('admin.bed_type', compact('pageTitle', 'bedTypes'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'        => 'required|string|unique:bed_types,name,' . $id
        ]);

        $exist = BedType::where('id', '!=', $id)->where('name', $request->name)->exists();
        if ($exist) {
            $notify[] = ['error', 'Name already exist'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $bedType      = BedType::findOrFail($id);
            $notification = 'Bed type updated successfully';
        } else {
            $bedType      = new BedType();
            $notification = 'Bed type added successfully';
        }

        $bedType->name = $request->name;
        $bedType->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return BedType::changeStatus($id);
    }
}
