<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function all()
    {
        $pageTitle = 'All Locations';
        $countries = Country::active()
            ->with(['cities' => function ($cities) {
                $cities->where('status', Status::ENABLE);
            }])->orderBy('name')->get();
        $locations = Location::searchable(['name', 'city:name', 'city.country:name'])->latest()->with('city.country')->paginate(getPaginate());
        return view('admin.locations', compact('pageTitle', 'countries', 'locations'));
    }

    public function add(Request $request, $id = 0)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required'
        ]);

        if ($id) {
            $location = Location::findOrFail($id);
            $message = 'Location updated successfully';
        } else {
            $location = new Location;
            $message = 'Location added successfully';
        }

        $location->city_id = $request->city_id;
        $location->name = $request->name;
        $location->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function updateStatus(Request $request, $id)
    {
        return Location::changeStatus($id);
    }
}
