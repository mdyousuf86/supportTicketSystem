<?php

namespace App\Lib;

use Carbon\Carbon;
use App\Models\Ticket;
use App\Lib\FormProcessor;
use App\Models\SpamFilter;
use App\Models\TicketReply;
use Illuminate\Support\Str;
use App\Models\TicketStatus;
use App\Models\TicketPriority;
use App\Models\TicketAttachment;
use App\Models\TicketDepartment;
use Illuminate\Validation\ValidationException;

class SupportTicket
{
    public function create($user, $request)
    {
        $this->validation($request);

        $this->spamFilter($user, $request);

        $ticket     = new Ticket();
        $priority   = TicketPriority::active()->where('id', $request->priority)->first();
        $status     = TicketStatus::active()->where('id', 1)->first();
        $department = TicketDepartment::active()->where('id', $request->department_id)->firstOrFail();

        $ticket->ticket_number      = rand(100000, 999999);
        $ticket->ticket_password    = Str::random(8);
        $ticket->department_id      = $department->id;
        $ticket->user_id            = $user->id ?? 0;
        $ticket->user_name          = $user->fullname;
        $ticket->user_email         = $user->email;
        $ticket->subject            = $request->subject;
        $ticket->ticket_priority_id = $priority->id;
        $ticket->priority           = $priority->title;
        $ticket->priority_color     = $priority->color;
        $ticket->ticket_status_id   = $status->id;
        $ticket->status             = $status->title;
        $ticket->status_color       = $status->color;
        $ticket->last_reply         = Carbon::now();

        $formData       = $department->form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData             = $formProcessor->processFormData($request, $formData);
        $ticket->extra_fields = $userData;
        $ticket->save();

        $this->ticketReply($user, $request, $ticket, fromCreate: true);

        return $ticket;
    }

    public function ticketReply($user, $request, $ticket, $fromCreate = false)
    {
        if (!$fromCreate) {
            $request->merge(['ticket_reply' => 1]);
            $this->validation($request);
            $this->spamFilter($user, $request);

            $status   = TicketStatus::active()->where('id', 3)->first();
            if ($status) {
                $ticket->ticket_status_id = $status->id;
                $ticket->status = $status->title;
                $ticket->status_color = $status->color;
                $ticket->last_reply = Carbon::now();
                $ticket->save();
            }
        }
        $ticketReply = new TicketReply();
        $ticketReply->ticket_id  = $ticket->id;
        $ticketReply->user_id    = $user->id ?? 0;
        $ticketReply->user_name  = $user->fullname;
        $ticketReply->user_email = $user->email;
        $ticketReply->message    = $request->message;
        $ticketReply->ip_address = getRealIP();
        $ticketReply->save();
        $this->storeSupportAttachments($request, $ticketReply->id, $ticket->id);
    }

    private function storeSupportAttachments($request, $ticketReplyId, $ticketId)
    {
        if ($request->hasFile('attachments')) {
            $path = getFilePath('ticket');
            foreach ($request->file('attachments') as  $file) {
                try {
                    $attachment = new TicketAttachment();
                    $attachment->ticket_id = $ticketId;
                    $attachment->ticket_reply_id = $ticketReplyId;
                    $attachment->attachment = fileUploader($file, $path);
                    $attachment->attachment_password = Str::random(8);
                    $attachment->save();
                } catch (\Exception $exp) {
                    throw ValidationException::withMessages(['error' => 'File could not upload']);
                }
            }
        }
    }

    private function validation($request)
    {
        $fileSizeLimit = gs('file_size_limit');
        $maxSize = strval($fileSizeLimit);
        
        $validationRules = [
            'attachments' => [
                'max:4096',
                function ($attribute, $value, $fail) use ($maxSize, $request) {
                    foreach ($request->file('attachments') as $file) {
                        $ext = strtolower($file->getClientOriginalExtension());
                        if (($file->getSize() / 1000000) > $maxSize) {
                            return $fail("Maximum $maxSize MB file size allowed!");
                        }

                        if (!in_array($ext, gs('attachment_file_type'))) {
                            return $fail("Only " . implode(', ', gs('attachment_file_type')) . " files are allowed");
                        }
                    }
                    if (count($request->file('attachments')) > 5) {
                        return $fail("Maximum 5 files can be uploaded");
                    }
                },
            ],
            'subject'    => 'required_without:ticket_reply|max:255',
            'department_id' => 'required_without:ticket_reply',
            'priority'   => 'required_without:ticket_reply',
            'message'    => 'required',
        ];

        if (!auth()->check()) {
            $validationRules['name'] = 'required_without:ticket_reply';
            $validationRules['email'] = 'required_without|email:ticket_reply';
        }
        $request->validate($validationRules);
    }

    private function spamFilter($user, $request)
    {
        $ipExists = SpamFilter::where('filter_type', 'ip')->where('content', getRealIP())->exists();
        if ($ipExists) {
            throw ValidationException::withMessages(['error' => 'This ip is blocked to open support ticket']);
        }
        $emailExists = SpamFilter::where('filter_type', 'email')->where('content', $user->email)->exists();
        if ($emailExists) {
            throw ValidationException::withMessages(['error' => 'Your email is blocked to open support ticket']);
        }

        $spamWords = SpamFilter::where('filter_type', 'phrase')->pluck('content')->toArray();
        foreach ($spamWords as $spamWord) {
            if (preg_match("/\b$spamWord\b/i", $request->message)) {
                throw ValidationException::withMessages(['error' => "You can't use '$spamWord' to your message"]);
            }
        }
    }
}
