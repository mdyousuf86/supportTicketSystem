<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Lib\FormProcessor;
use Illuminate\Http\Request;
use App\Models\TicketDepartment;
use App\Http\Controllers\Controller;
use App\Models\Form;

class TicketDepartmentController extends Controller
{
    public function index()
    {
        $pageTitle         = 'Ticket Department';
        $ticketDepartments = TicketDepartment::orderBy('sort_order')->paginate(getPaginate());
        return view('admin.department.list', compact('pageTitle', 'ticketDepartments'));
    }
    public function details($departmentId = 0)
    {
        $pageTitle = 'Ticket Department';
        $fileName  = "details";
        $ticketDepartment = TicketDepartment::find($departmentId);
        return view('admin.department.save', compact('pageTitle', 'fileName', 'ticketDepartment'));
    }

    public function detailsSave(Request $request, $id = 0)
    {
        $request->validate([
            'name'           => 'required',
            'email'          => 'required|email',
            'description'    => 'required',
            'host'           => 'required_if:auto_import,on',
            'port'           => 'required_if:auto_import,on',
            'email_username' => 'required_if:auto_import,on',
            'email_password' => 'required_if:auto_import,on'
        ]);

        if ($id) {
            $department = TicketDepartment::findOrFail($id);
        } else {
            $department = new TicketDepartment();
        }
        $department->name             = $request->name;
        $department->email            = $request->email;
        $department->description      = $request->description;
        $department->pipe_only        = $request->pipe_only ? Status::ENABLE : Status::DISABLE;
        $department->clients_only     = $request->clients_only ? Status::ENABLE : Status::DISABLE;
        $department->auto_respond     = $request->auto_respond ? Status::ENABLE : Status::DISABLE;
        $department->is_hidden        = $request->is_hidden ? Status::ENABLE : Status::DISABLE;
        $department->feedback_request = $request->feedback_request ? Status::ENABLE : Status::DISABLE;
        $department->auto_import      = $request->auto_import ? Status::ENABLE : Status::DISABLE;
        $department->host             = $request->auto_import ? $request->host : '';
        $department->port             = $request->auto_import ? $request->port : '';
        $department->email_username   = $request->auto_import ? $request->email_username : '';
        $department->email_password   = $request->auto_import ? $request->email_password : '';
        $department->save();
        if ($department->completed_step > 1) {
            $notify[] = ['success', 'Department details updated successfully'];
            return to_route('admin.department.details', $department->id)->withNotify($notify);
        } else {
            return to_route('admin.department.custom.field', $department->id);
        }
    }
    public function customField($departmentId = 0)
    {
        $pageTitle = 'Ticket Department';
        $fileName  = "custom_field";
        if (!$departmentId) {
            $notify[] = ['error', "Please fill the basic information"];
            return to_route('admin.department.details')->withNotify($notify);
        }
        $ticketDepartment = TicketDepartment::findOrFail($departmentId);
        $form             = $ticketDepartment->form;
        return view('admin.department.save', compact('pageTitle', 'fileName', 'ticketDepartment', 'form'));
    }

    public function customFieldSave(Request $request, $id = 0)
    {
        $department    = TicketDepartment::findOrFail($id);
        $formProcessor = new FormProcessor();
        $formExists    = Form::where('id', $department->form_id)->where('act', 'department')->exists();
        $generate      = $formProcessor->generate('department', $formExists, 'id', $department->form_id);

        $generatorValidation = $formProcessor->generatorValidation();
        $validation          = array_merge($generatorValidation['rules']);
        $request->validate($validation, $generatorValidation['messages']);
        $department->form_id        = @$generate->id ?? 0;
        if ($department->completed_step == 1) {
            $department->completed_step   = 2;
        }
        $department->save();
        $notify[] = ['success', 'Department saved successfully'];
        return to_route('admin.department.index')->withNotify($notify);
    }

    public function status($id)
    {
        return TicketDepartment::changeStatus($id);
    }
    public function sortDepartment()
    {
        TicketDepartment::sortOrder();
    }
}
