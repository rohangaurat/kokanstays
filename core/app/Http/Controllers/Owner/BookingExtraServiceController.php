<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\BookedRoom;
use App\Models\Booking;
use App\Models\ExtraService;
use App\Models\UsedExtraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingExtraServiceController extends Controller {
    public function list() {
        $pageTitle     = 'Utilized Premium Services';
        $extraServices = ExtraService::currentOwner()->active()->get();
        $services      = UsedExtraService::currentOwner()->searchable(['extraService:name', 'room:room_number'])->with('extraService', 'room', 'actionBy')->paginate(getPaginate());
        return view('owner.booking.service_details', compact('pageTitle', 'services'));
    }

    public function addNew() {
        $pageTitle     = 'Add Premium Service';
        $extraServices = ExtraService::currentOwner()->active()->get();
        return view('owner.extra_service.add', compact('pageTitle', 'extraServices'));
    }

    public function addService(Request $request) {
        $validator = Validator::make($request->all(), [
            'room_number'  => 'required',
            'service_date' => 'required|date_format:Y-m-d|before:tomorrow',
            'services'     => 'required|array',
            'services.*'   => 'required|exists:extra_services,id',
            'qty'          => 'required|array',
            'qty.*'        => 'integer|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $serviceRoom = BookedRoom::where(function ($query) {
            $query->whereHas('booking', function ($booking) {
                $booking->currentOwner();
            });
        })->where('room_number', $request->room_number)->whereDate('booked_for', $request->service_date)->where('status', Status::ROOM_ACTIVE)->first();

        if (!$serviceRoom) {
            return response()->json(['error' => [$request->room_number . ' no. room isn\'t booked for ' . showDateTime($request->service_date, 'd M, Y')]]);
        }

        $booking = Booking::currentOwner()->find($serviceRoom->booking_id);
        if (!$booking) {
            return response()->json(['error' => ['Booking not found']]);
        }

        $totalAmount = 0;
        $data        = [];

        foreach ($request->services as $key => $service) {
            $serviceDetails = ExtraService::currentOwner()->active()->find($service);
            if (!$serviceDetails) {
                return response()->json(['error' => ['Service not found']]);
            }

            $data[$key]['owner_id']         = getOwnerParentId();
            $data[$key]['action_by']        = authOwner()->id;
            $data[$key]['booking_id']       = $booking->id;
            $data[$key]['extra_service_id'] = $service;
            $data[$key]['room_id']          = $serviceRoom->room_id;
            $data[$key]['booked_room_id']   = $serviceRoom->id;
            $data[$key]['qty']              = $request->qty[$key];
            $data[$key]['unit_price']       = $serviceDetails->cost;
            $data[$key]['total_amount']     = $request->qty[$key] * $serviceDetails->cost;
            $data[$key]['service_date']     = $request->service_date;
            $data[$key]['created_at']       = now();
            $data[$key]['updated_at']       = now();

            $totalAmount += $request->qty[$key] * $serviceDetails->cost;
        }

        UsedExtraService::insert($data);
        $booking->service_cost += $totalAmount;
        $booking->save();
        $booking->createActionHistory('added_premium_service');
        return response()->json(['success' => 'Premium service added successfully']);
    }

    public function delete($id) {
        $service = UsedExtraService::currentOwner()->findOrFail($id);
        $booking = $service->booking;

        $booking->service_cost -= $service->total_amount;
        $booking->save();

        $booking->createActionHistory('deleted_premium_service');

        $service->delete();

        $notify[] = ['success', 'Premium service deleted successfully'];
        return back()->withNotify($notify);
    }
}
