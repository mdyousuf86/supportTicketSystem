<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketPriority;
use Illuminate\Http\Request;

class TicketPriorityController extends Controller
{

    public function index()
    {
        $pageTitle        = "Ticket Priority";
        $ticketPriorities = TicketPriority::orderBy('sort_order')->paginate(getPaginate());
        return view('admin.ticket_priority.index', compact('pageTitle', 'ticketPriorities'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'title' => 'required',
            'color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
        ]);
        if ($id) {
            $ticketPriority = TicketPriority::findOrFail($id);
            $massage        = 'Ticket priority updated successfully';
        } else {
            $ticketPriority = new TicketPriority();
            $massage        = 'Ticket priority added successfully';
        }
        $ticketPriority->title = $request->title;
        $ticketPriority->color = $request->color;
        $ticketPriority->save();
        $notify[] = ['success', $massage];
        return redirect()->back()->withNotify($notify);
    }

    public function status($id)
    {
        return TicketPriority::changeStatus($id);
    }

    public function sortPriority()
    {
        TicketPriority::sortOrder();
    }
}
