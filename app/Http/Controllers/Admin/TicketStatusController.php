<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TicketStatusController extends Controller
{
    public function index()
    {
        $pageTitle      = "Ticket Status";
        $ticketStatuses = TicketStatus::orderBy('sort_order')->paginate(getPaginate());
        return view('admin.status.index', compact('pageTitle', 'ticketStatuses'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'title' => 'required',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);

        if ($id) {
            $ticketStatus = TicketStatus::findOrFail($id);
            $massage      = 'Ticket Status updated successfully';
        } else {
            $ticketStatus = new TicketStatus();
            $massage      = 'Ticket status added successfully';
        }
        $ticketStatus->title       = $request->title;
        $ticketStatus->color       = $request->color;
        $ticketStatus->is_active   = $request->is_active ? Status::ENABLE : Status::DISABLE;
        $ticketStatus->is_awaiting = $request->is_awaiting ? Status::ENABLE : Status::DISABLE;
        $ticketStatus->auto_close  = $request->auto_close ? Status::ENABLE : Status::DISABLE;
        $ticketStatus->save();
        $notify[] = ['success', $massage];
        return redirect()->back()->withNotify($notify);
    }

    public function status($id)
    {
        return TicketStatus::changeStatus($id);
    }

    public function sortStatus()
    {
        TicketStatus::sortOrder();
    }
}
