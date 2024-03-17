<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketFeedback;
use Illuminate\Http\Request;

class TicketFeedbackController extends Controller
{
    public function index($ticketId = null)
    {
        $pageTitle       = "Ticket Feedback";
        $ticketFeedbacks = TicketFeedback::searchable(['tickets:ticket_number'])->with('tickets')->orderBy('id', 'desc');

        if ($ticketId) {
            $ticketFeedbacks->where('ticket_id', $ticketId);
        }

        $ticketFeedbacks = $ticketFeedbacks->paginate(getPaginate());

        return view('admin.feedback.index', compact('pageTitle', 'ticketFeedbacks'));
    }
}
