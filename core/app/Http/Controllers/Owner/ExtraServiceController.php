<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ExtraService;
use Illuminate\Http\Request;

class ExtraServiceController extends Controller
{
    public function index()
    {
        $pageTitle     = 'Premium Services';
        $extraServices = ExtraService::currentOwner()->latest()->paginate(getPaginate());
        return view('owner.hotel.extra_services', compact('pageTitle', 'extraServices'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cost' => 'required|integer|gt:0'
        ]);

        $exist = ExtraService::currentOwner()->where('id', '!=', $id)->where('name', $request->name)->exists();
        if ($exist) {
            $notify[] = ['error', 'Name already exist'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $extraService         = ExtraService::currentOwner()->findOrFail($id);
            $notification          = 'Service updated successfully';
        } else {
            $extraService = new ExtraService();
            $extraService->owner_id = getOwnerParentId();
            $notification  = 'Service added successfully';
        }

        $extraService->name = $request->name;
        $extraService->cost = $request->cost;

        $extraService->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $extraService = ExtraService::currentOwner()->findOrFail($id);
        $extraService->status = $extraService->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $extraService->save();

        $notify[] = ['success', 'Status updated successfully'];
        return back()->withNotify($notify);
    }
}
