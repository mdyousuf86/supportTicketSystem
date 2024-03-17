<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\User;
use App\Models\Ticket;
use App\Lib\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Support\Str;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use App\Models\TicketFeedback;
use App\Models\TicketPriority;
use App\Models\TicketAttachment;
use App\Models\TicketDepartment;
use App\Models\AdminNotification;

class SupportTicketController extends Controller
{
    protected $files;
    protected $allowedExtension = ['jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx'];
    public function supportTicket()
    {
        $user      = auth()->user();
        $pageTitle = "Support Tickets";
        $supports  = Ticket::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.support_ticket.index', compact('supports', 'pageTitle'));
    }

    public function supportTicketDepartment()
    {
        $pageTitle   = "Support Department";
        $departments = TicketDepartment::active()->orderBy('sort_order')->where('is_hidden', 0);
        if (!auth()->check()) {
            $departments = $departments->where('pipe_only', 0);
        }
        $departments = $departments->get();
        return view($this->activeTemplate . 'user.support_ticket.department', compact('pageTitle', 'departments'));
    }
    public function openSupportTicket($departmentId)
    {
        $user       = auth()->user();
        $pageTitle  = "Open Ticket";
        $department = TicketDepartment::active()->where('id', $departmentId)->firstOrFail();
        $priorities = TicketPriority::active()->orderBy('sort_order')->get();
        return view($this->activeTemplate . 'user.support_ticket.create', compact('department', 'pageTitle', 'priorities'));
    }

    public function storeSupportTicket(Request $request)
    {
        $department = TicketDepartment::active()->where('id', $request->department_id)->firstOrFail();

        if ($department->clients_only && !auth()->check()) {
            if (User::where('email', $request->email)->doesntExist()) {
                $notify[] = ['error', 'This ticket is only open to register user.'];
                return back()->withNotify($notify);
            } 
        }

        if ($department->pipe_only && !auth()->check()) {
            $notify[] = ['error', 'Please log in to open the ticket.'];
            return back()->withNotify($notify);
        }
        if ($department->is_hidden) {
            $notify[] = ['error', 'This department ticket can not be opened'];
            return back()->withNotify($notify);
        }
        $user = auth()->user();
        if (!auth()->check()) {
            $user           = new stdClass;
            $user->fullname = $request->name;
            $user->email    = $request->email;
        }
        $ticket = (new SupportTicket())->create($user, $request);

        if ($department->auto_respond == 0) {
            if (!auth()->check()) {
                $user->username = $ticket->user_name;
            }
            notify($user, 'TICKET_OPENED', [
                'username'        => $user->username,
                'user_email'      => $user->email,
                'ticket_number'   => $ticket->ticket_number,
                'ticket_password' => $ticket->ticket_password,
                'ticket_link'     => route('support.ticket.view', $ticket->ticket_number)
            ], ['email']);
        }
        $notify[] = ['success', 'Ticket opened successfully!'];
        return to_route('support.ticket.view', $ticket->ticket_number)->withNotify($notify);
    }

    public function viewTicket($ticket)
    {
        $user      = auth()->user();
        $userId    = $user ? $user->id : 0;
        $pageTitle = "Support Ticket Replies";
        $myTicket  = Ticket::where('ticket_number', $ticket)->where('user_id', $userId)->orderBy('id', 'desc')->firstOrFail();
        $messages  = TicketReply::where('ticket_id', $myTicket->id)->where('is_private', 0)->with('attachments')->orderBy('id', 'desc')->get();
        return view($this->activeTemplate . 'user.support_ticket.view', compact('myTicket', 'messages', 'pageTitle', 'user'));
    }


    public function replyTicket(Request $request, $id)
    {
        $ticket = Ticket::where('id', $id)->firstOrFail();
        if (auth()->check()) {
            $user = auth()->user();
        } else {
            $user           = new stdClass;
            $user->fullname = $ticket->user_name;
            $user->email    = $ticket->user_email;
        }
        (new SupportTicket())->ticketReply($user, $request, $ticket);

        $notify[] = ['success', 'Support ticket replied successfully!'];
        return back()->withNotify($notify);
    }


    protected function storeSupportAttachments($ticketReplyId, $ticketId)
    {
        $path = getFilePath('ticket');
        foreach ($this->files as  $file) {
            try {
                $attachment                      = new TicketAttachment();
                $attachment->ticket_id           = $ticketId;
                $attachment->ticket_reply_id     = $ticketReplyId;
                $attachment->attachment          = fileUploader($file, $path);
                $attachment->attachment_password = Str::random(8);
                $attachment->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'File could not upload'];
                return $notify;
            }
        }
        return 200;
    }


    public function closeTicket($id)
    {
        $ticket                   = Ticket::where('id', $id)->firstOrFail();
        $status                   = TicketStatus::active()->where('id', 4)->first();
        $ticket->ticket_status_id = $status->id;
        $ticket->status           = $status->title;
        $ticket->status_color     = $status->color;
        $ticket->save();
        $notify[] = ['success', 'Support ticket closed successfully!'];
        return back()->withNotify($notify);
    }

    public function ticketDownload($attachmentId)
    {
        $attachment = TicketAttachment::with('supportMessage')->findOrFail(decrypt($attachmentId));
        $title      = @$attachment->supportMessage ? slug(@$attachment->supportMessage->ticket->subject) : 'NA';
        $file       = $attachment->attachment;
        $path       = getFilePath('ticket');
        $full_path  = $path . '/' . $file;
        $ext        = pathinfo($file, PATHINFO_EXTENSION);
        $mimetype   = mime_content_type($full_path);
        header('Content-Disposition: attachment; filename="' . ($title ?? 'NA') . '.' . $ext . '";');
        header("Content-Type: " . $mimetype);
        return readfile($full_path);
    }

    public function ticketAttachmentDelete($attachmentId)
    {
        $attachment = TicketAttachment::with('supportMessage')->findOrFail(decrypt($attachmentId));
        $path       = getFilePath('ticket') . '/' . $attachment->attachment;
        fileManager()->removeFile($path);
        $attachment->delete();
        $notify[] = ['success', 'Attachment deleted successfully!'];
        return back()->withNotify($notify);
    }

    public function feedbackTicket($ticketNumber)
    {
        $pageTitle = "Ticket Feedback";
        $ticket    = Ticket::where('ticket_number', $ticketNumber)->first();
        return view($this->activeTemplate . 'user.support_ticket.feedback', compact('pageTitle', 'ticket'));
    }

    public function feedbackSave(Request $request, $ticketId)
    {
        $request->validate([
            'comment' => 'required',
            'rating'  => 'required',
        ]);

        $ticketFeedback             = new TicketFeedback();
        $ticketFeedback->ticket_id  = $ticketId;
        $ticketFeedback->comment    = $request->comment;
        $ticketFeedback->rating     = $request->rating;
        $ticketFeedback->ip_address = getRealIP();
        $ticketFeedback->save();

        $user                         = auth()->user();
        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $user ? $user->id : 0;
        $adminNotification->title     = 'Added new ticket feedback';
        $adminNotification->click_url = urlPath('admin.feedback.index', $ticketId);
        $adminNotification->save();

        $notify[] = ['success', 'Ticket feedback added successfully'];
        return redirect()->back()->withNotify($notify);
    }
}
