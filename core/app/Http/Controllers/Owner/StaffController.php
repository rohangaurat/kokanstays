<?php

namespace App\Http\Controllers\Owner;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller {
    public function index() {
        $pageTitle = 'All Staff';
        $owner     = authOwner();
        $allStaff  = Owner::where('parent_id', $owner->id)->with('role')->paginate(getPaginate());
        $roles     = Role::currentOwner()->orderBy('name')->get();
        return view('owner.staff.index', compact('pageTitle', 'allStaff', 'roles'));
    }

    public function status($id) {
        $parent        = authOwner();
        $owner         = Owner::where('parent_id', $parent->id)->findOrFail($id);
        $owner->status = $owner->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        $owner->save();

        $notify[] = ['success', 'Status updated successfully'];
        return back()->withNotify($notify);
    }

    public function save(Request $request, $id = 0) {
        $this->validation($request, $id);
        if ($id) {
            $staff   = Owner::findOrFail($id);
            $message = "Staff updated successfully";
        } else {
            $staff   = new Owner();
            $message = "New staff added successfully";
        }

        $staff->parent_id = getOwnerParentId();
        $staff->firstname = $request->firstname;
        $staff->lastname  = $request->lastname;
        $staff->email     = $request->email;
        $staff->role_id   = $request->role_id;
        $staff->password  = $request->password ? Hash::make($request->password) : $staff->password;
        $staff->save();
        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    private function validation($request, $id) {
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'required|unique:owners,email,' . $id,
            'role_id'   => 'required|integer|gt:0',
            'password'  => !$id ? 'required|min:6' : 'nullable',
        ]);
    }

    public function login($id) {
        Auth::guard('owner')->loginUsingId($id);
        return to_route('owner.dashboard');
    }
}
