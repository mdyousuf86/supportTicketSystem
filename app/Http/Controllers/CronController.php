<?php

namespace App\Http\Controllers;

use stdClass;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Ticket;
use App\Models\CronJob;
use App\Lib\CurlRequest;
use App\Constants\Status;
use App\Models\CronJobLog;
use App\Models\TicketStatus;
use App\Models\TicketDepartment;
use App\Http\Controllers\Controller;

class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            // $crons->where('next_run', '<', now())->where('is_running', 1);
        }

        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function autoTicketClose()
    {
        $statusId = TicketStatus::where('auto_close', 1)->pluck('id')->toArray();
        $status   = TicketStatus::where('id', Status::TICKET_CLOSE)->first();
        $tickets  = Ticket::whereIn('ticket_status_id', $statusId)->where('last_reply', '<', now()->subDays(3))->get();
        foreach ($tickets as $ticket) {
            $ticket->ticket_status_id = Status::TICKET_CLOSE;
            $ticket->status           = $status->title;
            $ticket->status_color     = $status->color;
            $ticket->save();

            $user = User::where('email', $ticket->user_email)->first();

            if (!$user) {
                $user           = new User();
                $user->username = $ticket->user_name;
                $user->fullname = $ticket->user_name;
                $user->email    = $ticket->user_email;
            }

            notify($user, 'AUTO_CLOSED', [
                'username'        => $user->username,
                'ticket_password' => $ticket->ticket_password,
                'ticket_subject'  => $ticket->subject,
                'link'            => route('support.ticket.view', $ticket->ticket_number),
            ], ['email']);

            $department = TicketDepartment::active()->where('id', $ticket->department_id)->where('feedback_request', Status::YES)->first();
            if ($department) {
                notify($user, 'FEEDBACK_REQUEST', [
                    'username'        => $user->username,
                    'ticket_password' => $ticket->ticket_password,
                    'ticket_subject'  => $ticket->subject,
                    'ticket_link'     => route('support.ticket.view', $ticket->ticket_number),
                    'feedback_link'   => route('support.ticket.feedback', $ticket->ticket_number),
                ], ['email']);
            }
        }
    }
}
