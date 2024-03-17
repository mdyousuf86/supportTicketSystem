<?php

namespace App\Http\Controllers\Admin;

use stdClass;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Ticket;
use App\Constants\Status;
use App\Models\TicketLog;
use App\Lib\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Support\Str;
use App\Models\TicketStatus;
use Illuminate\Http\Request;
use App\Models\TicketPriority;
use App\Models\PredefinedReply;
use App\Models\TicketAttachment;
use App\Models\TicketDepartment;
use App\Http\Controllers\Controller;
use App\Models\PredefinedReplyCategory;
use League\CommonMark\CommonMarkConverter;

class SupportTicketController extends Controller
{
    protected $files;
    protected $allowedExtension = ['jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx'];

    public function tickets($id = null)
    {
        $staff       = auth()->guard('admin')->user();
        $departments = $staff->departments->pluck('id');
        $pageTitle   = 'Support Tickets';
        $items       = Ticket::orderBy('id', 'desc')->with('user', 'replies')->whereIn('department_id', $departments);

        if ($id) {
            $items->where('ticket_status_id', $id);
        }

        $items = $items->paginate(getPaginate());

        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function supportTicketDepartment()
    {
        $pageTitle   = "Support Department";
        $departments = TicketDepartment::active()->orderBy('sort_order')->get();
        return view('admin.support.departments', compact('pageTitle', 'departments'));
    }

    public function openSupportTicket($departmentId)
    {
        $pageTitle  = "Open Ticket";
        $department = TicketDepartment::active()->where('id', $departmentId)->firstOrFail();
        $priorities = TicketPriority::active()->orderBy('sort_order')->get();
        return view('admin.support.open', compact('department', 'pageTitle', 'priorities'));
    }
    public function storeSupportTicket(Request $request)
    {
        $request->validate([
            'user' => 'required|exists:users,username',
        ]);

        $department = TicketDepartment::where('id', $request->department_id)->first();
        $department = TicketDepartment::active()->where('id', $request->department_id)->firstOrFail();

        $user = User::where('username', $request->user)->firstOrFail();
        $request->merge(['ticket_reply' => 1]);
        $ticket = (new SupportTicket())->create($user, $request);

        $department = TicketDepartment::active()->where('id', $request->department_id)->firstOrFail();

        $admin                = auth()->guard('admin')->user();
        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;
        $ticketLog->action    = 'New support ticket opened (by ' . $admin->username . ')';
        $ticketLog->save();

        if ($department->auto_respond == 0) {
            notify($user, 'TICKET_OPENED_BY_ADMIN', [
                'username'        => $user->username,
                'user_email'      => $user->email,
                'ticket_number'   => $ticket->ticket_number,
                'ticket_password' => $ticket->ticket_password,
                'ticket_link'     => route('support.ticket.view', $ticket->ticket_number),
                'staff_name'      => $admin->name
            ],);
        }
        $notify[] = ['success', 'Ticket opened successfully!'];
        return to_route('admin.ticket.view', $ticket->ticket_number)->withNotify($notify);
    }

    public function userCheck(Request $request)
    {
        $username = $request->username;
        $user = User::where('username', $username)->where('status', Status::USER_ACTIVE)->first();
        if ($user) {
            return response()->json(['exists' => true]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    public function ticketReply($ticketNumber)
    {
        $pageTitle   = "Reply Ticket";
        $ticketReply = $this->ticketReplyAndNote($ticketNumber);
        extract($ticketReply);

        return view('admin.support.add_reply', compact('ticket', 'messages', 'departments', 'priorities', 'requestor', 'ticketDepartment', 'statuses', 'pageTitle'));
    }
    public function addNote($id)
    {
        $pageTitle  = "Ticket Note";
        $ticketNote = $this->ticketReplyAndNote($id);
        extract($ticketNote);

        return view('admin.support.add_note', compact('ticket', 'messages', 'departments', 'ticketDepartment', 'requestor', 'priorities', 'statuses', 'pageTitle'));
    }

    private function ticketReplyAndNote($ticketNumber)
    {
        $ticket = Ticket::with('user')->where('ticket_number', $ticketNumber)->firstOrFail();
        return [
            'ticket'           => $ticket,
            'requestor'        => User::where('id', $ticket->user_id)->first(),
            'departments'      => TicketDepartment::active()->orderBy('sort_order')->get(),
            'ticketDepartment' => TicketDepartment::active()->where('id', $ticket->department_id)->with('staffs')->first(),
            'priorities'       => TicketPriority::active()->orderBy('sort_order')->get(),
            'statuses'         => TicketStatus::active()->orderBy('sort_order')->get(),
            'messages'         => TicketReply::with('ticket', 'admin', 'attachments')->where('ticket_id', $ticket->id)->orderBy('id', 'desc')->get(),
        ];
    }
    public function customFields($id)
    {
        $pageTitle          = 'Reply Ticket';
        $ticketCustomFields = $this->ticketReplyAndNote($id);
        extract($ticketCustomFields);
        return view('admin.support.custom_field', compact('ticket', 'messages', 'requestor', 'ticketDepartment',  'departments', 'priorities', 'pageTitle'));
    }
    public function otherTickets($id)
    {
        $pageTitle       = 'Other Ticket';
        $otherTicketData = $this->ticketReplyAndNote($id);
        extract($otherTicketData);
        $otherTickets = Ticket::with('user')->where('user_id', $ticket->user_id)->where('ticket_number', '!=', $ticket->ticket_number)->paginate(getPaginate());
        return view('admin.support.other_ticket', compact('ticket', 'otherTickets', 'requestor', 'departments', 'ticketDepartment', 'priorities', 'pageTitle'));
    }
    public function log($id)
    {
        $pageTitle  = 'Ticket Log';
        $ticketLogs = $this->ticketReplyAndNote($id);
        extract($ticketLogs);
        $TicketLogs = TicketLog::where('ticket_id', $ticket->id)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.support.log', compact('ticket', 'requestor', 'departments', 'ticketDepartment', 'priorities', 'TicketLogs', 'pageTitle', 'ticketDepartment'));
    }

    public function replyTicket(Request $request, $ticketNumber)
    {
        $converter   = new CommonMarkConverter();
        $htmlContent = $converter->convert($request->message);
        $admin       = auth()->guard('admin')->user();
        $ticket      = Ticket::where('ticket_number', $ticketNumber)->firstOrFail();
        $this->validation($request);

        $status     = TicketStatus::active()->where('id', $request->ticket_status_id)->first();
        $priority   = TicketPriority::active()->where('id', $request->priority_id)->first();
        $department = TicketDepartment::active()->where('id', $ticket->department_id)->first();
        if (!is_null($request->department_id)) {
            $ticket->department_id = $request->department_id;
        }

        if (!is_null($request->assigned_admin_id)) {
            $ticket->assigned_admin_id = $request->assigned_admin_id;
        }

        if (!is_null($request->ticket_priority_id)) {
            $ticket->ticket_priority_id = $priority->id;
            $ticket->priority           = $priority->title;
            $ticket->priority_color     = $priority->color;
        }

        if (!is_null($request->ticket_status_id)) {
            $ticket->ticket_status_id = $status->id;
            $ticket->status           = $status->title;
            $ticket->status_color     = $status->color;
        }
        if (!$ticket->first_reply_delay) {
            $ticket->first_reply_delay = Carbon::parse($ticket->created_at)->diffInSeconds();
        }
        if (!$ticket->first_reply_admin_id) {
            $ticket->first_reply_admin_id = $admin->id;
        }


        $ticket->last_reply = Carbon::now();
        $ticket->save();
        $message             = new TicketReply();
        $message->ticket_id  = $ticket->id;
        $message->user_id    = $ticket->user_id;
        $message->user_name  = $ticket->fullname;
        $message->user_email = $ticket->email;
        $message->admin_id   = $admin->id;
        $message->admin_name = $admin->username;
        $message->message    = $htmlContent->getContent();
        $message->ip_address = getRealIP();
        $message->is_private = $request->is_private ? Status::ENABLE : Status::DISABLE;
        $message->save();

        if ($request->hasFile('attachments')) {
            $uploadAttachments = $this->storeSupportAttachments($message->id, $ticket->id);
            if ($uploadAttachments != 200) return back()->withNotify($uploadAttachments);;
        }

        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;

        if ($request->is_private == 'on') {
            $ticketLog->action = 'Ticket note added (by ' . $admin->username . ')';
            $notify[]          = ['success', 'Note added successfully!'];
        } else {
            $ticketLog->action = 'Ticket replied (by ' . $admin->username . ')';
            $notify[]          = ['success', 'Ticket replied successfully!'];
        }
        $ticketLog->save();

        $user = User::find($ticket->user_id);
        if ($department->auto_respond == 0 && !$request->is_private == 'on') {
            if ($ticket->user_id == 0) {
                $user           = new stdClass();
                $user->username = $ticket->user_name;
                $user->fullname = $ticket->user_name;
                $user->email    = $ticket->user_email;
            }
            notify($user, 'TICKET_REPLIED', [
                'username'        => $user->username,
                'ticket_password' => $ticket->ticket_password,
                'ticket_subject'  => $ticket->subject,
                'reply'           => $message->message,
                'link'            => route('support.ticket.view', $ticket->ticket_number),
                'staff_name'      => $admin->name
            ], ['email']);
        }
        if ($request->return == 'return_to_ticket_list') {
            return to_route('admin.ticket.index')->withNotify($notify);
        } else {
            return back()->withNotify($notify);
        }
    }

    public function changeDepartment(Request $request)
    {
        $ticket = Ticket::where('id', $request->ticket_id)->firstOrFail();
        $ticket->department_id      = $request->department_id;
        $ticket->save();

        $admin                = auth()->guard('admin')->user();
        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;
        $ticketLog->action    = 'Ticket department changed (by ' . $admin->username . ')';
        $ticketLog->save();
    }
    public function changePriority(Request $request)
    {
        $ticket                     = Ticket::where('id', $request->ticket_id)->firstOrFail();
        $ticketPriority             = TicketPriority::where('id', $request->ticket_priority_id)->first();
        $ticket->ticket_priority_id = $ticketPriority->id;
        $ticket->priority           = $ticketPriority->title;
        $ticket->priority_color     = $ticketPriority->color;
        $ticket->save();

        $admin                = auth()->guard('admin')->user();
        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;
        $ticketLog->action    = 'Ticket priority changed (by ' . $admin->username . ')';
        $ticketLog->save();
    }
    public function ticketAssigned(Request $request)
    {

        $ticket                    = Ticket::where('id', $request->ticket_id)->firstOrFail();
        $ticket->assigned_admin_id = $request->staff_id;
        $ticket->save();

        $admin                = auth()->guard('admin')->user();
        $staff                = Admin::where('id', $request->staff_id)->first();
        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;
        $ticketLog->action    = 'Assigned to staff member ' . $staff->username . ' (by ' . $admin->username . ')';
        $ticketLog->save();
    }

    public function predefinedMessage(Request $request)
    {
        $categoryId      = $request->category_id;
        $prevFolder      = PredefinedReplyCategory::find($categoryId);
        $replyCategories = PredefinedReplyCategory::where('parent_id', $request->category_id ?? 0)->whereNull('deleted_at')->get();
        $replies         = PredefinedReply::where('category_id', $request->category_id ?? 0)->get();
        return view('admin.replies.get_replies', compact('replyCategories', 'replies', 'prevFolder'));
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

    protected function validation($request)
    {
        $fileSizeLimit = gs('file_size_limit');
        $maxSize = strval($fileSizeLimit);
        $this->maxSize = $maxSize;
        $this->files   = $request->file('attachments');
        $request->validate([
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) {
                    foreach ($this->files as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > $this->maxSize) {
                            return $fail("Maximum $this->maxSize MB file size allowed!");
                        }
                        if (!in_array($ext, gs('attachment_file_type'))) {
                            return $fail("Only " . implode(', ', gs('attachment_file_type')) . " files are allowed");
                        }
                    }
                    if (count($this->files) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'message' => 'required',
        ]);
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

    public function closeTicket($ticketNumber)
    {
        $ticket                   = Ticket::where('ticket_number', $ticketNumber)->firstOrFail();
        $status                   = TicketStatus::active()->where('id', Status::TICKET_CLOSE)->first();
        $ticket->ticket_status_id = $status->id;
        $ticket->status           = $status->title;
        $ticket->status_color     = $status->color;
        $ticket->save();
        $notify[] = ['success', 'Support ticket closed successfully!'];

        $admin                = auth()->guard('admin')->user();
        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $ticket->id;
        $ticketLog->action    = 'Ticket closed (by ' . $admin->username . ')';
        $ticketLog->save();
        return back()->withNotify($notify);
    }
    public function ticketAttachmentDelete($attachmentId)
    {
        $attachment = TicketAttachment::with('supportMessage')->findOrFail(decrypt($attachmentId));
        $path       = getFilePath('ticket') . '/' . $attachment->attachment;
        fileManager()->removeFile($path);
        $attachment->delete();

        $admin                = auth()->guard('admin')->user();
        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $attachment->ticket_id;
        $ticketLog->action    = 'Ticket attachment deleted (by ' . $admin->username . ')';
        $ticketLog->save();

        $notify[] = ['success', 'Attachment deleted successfully!'];
        return back()->withNotify($notify);
    }

    public function ticketDelete($id)
    {
        $message = TicketReply::findOrFail($id);
        $path    = getFilePath('ticket');
        if ($message->attachments()->count() > 0) {
            foreach ($message->attachments as $attachment) {
                fileManager()->removeFile($path . '/' . $attachment->attachment);
                $attachment->delete();
            }
        }
        $message->delete();

        $admin                = auth()->guard('admin')->user();
        $ticketLog            = new TicketLog();
        $ticketLog->ticket_id = $message->ticket_id;
        $ticketLog->action    = 'Ticket deleted (by ' . $admin->username . ')';
        $ticketLog->save();

        $notify[] = ['success', "Support ticket deleted successfully"];
        return back()->withNotify($notify);
    }

    public function ticketNumberFilter(Request $request)
    {
        $pageTitle = 'Support Tickets';
        $items     = Ticket::where('ticket_number', $request->ticket_number)->get();
        $request->validate([
            'ticket_number' => 'required',
        ]);
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }
}
