<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function all()
    {
        $pageTitle = 'All Countries';
        $countries = Country::searchable(['name'])->latest()->withCount('cities as total_city')->paginate(getPaginate());
        return view('admin.countries', compact('pageTitle', 'countries'));
    }

    public function add(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|unique:countries,name,' . $id,
            'code' =>  ['required', 'regex:/^[A-Z]{2,3}$/', 'unique:countries,code,' . $id],
            'dial_code' => ['required', 'regex:/^(\+\d+|\d+)$/', 'unique:countries,dial_code,' . $id]
        ]);

        if ($id) {
            $country = Country::findOrFail($id);
            $message = 'Country updated successfully';
        } else {
            $country = new Country;
            $message = 'Country added successfully';
        }

        $country->name = $request->name;
        $country->code = $request->code;
        $country->dial_code = $request->dial_code;
        $country->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function updateStatus($id)
    {
        return Country::changeStatus($id);
    }
}
