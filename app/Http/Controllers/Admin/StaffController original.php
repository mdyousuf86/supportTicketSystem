<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\TicketDepartment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index()
    {
        $pageTitle         = "Staff";
        $staffs            = Admin::where('is_staff', Status::YES)->with('departments:id')->paginate(getPaginate());
        $ticketDepartments = TicketDepartment::active()->orderBy('sort_order')->get();
        return view('admin.staff.index', compact('pageTitle', 'staffs', 'ticketDepartments'));
    }

    public function save(Request $request, $id = 0)
    {
        $passwordIsRequired = $id ? 'nullable' : 'required';
        $request->validate([
            'name'     => 'required',
            'username' => 'required|unique:admins,username,' . $id,
            'email'    => 'required|email',
            'password' => "$passwordIsRequired|string|min:6",
        ]);

        if ($id) {
            $staff   = Admin::findOrFail($id);
            $massage = 'Staff information updated successfully';
        } else {
            $staff   = new Admin();
            $massage = 'Staff added successfully';
        }

        $staff->name     = $request->name;
        $staff->username = $request->username;
        $staff->email    = $request->email;
        if (!is_null($request->password)) {
            $staff->password = Hash::make($request->password);
        }
        $staff->is_staff = Status::YES;
        $staff->save();

        $staff->departments()->sync($request->department_id);

        $notify[] = ['success', $massage];
        return redirect()->back()->withNotify($notify);
    }

    public function status($id)
    {
        return Admin::changeStatus($id);
    }


    ///role

    public function roleIndex()
    {
        $pageTitle         = "All Roles";
        return view('admin.staff.role_index', compact('pageTitle'));
    }
}
