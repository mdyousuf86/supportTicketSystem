<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketReply;
use App\Models\TicketStatus;
use App\Models\User;
use App\Models\UserLogin;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function dashboard()
    {
        $pageTitle = 'Dashboard';

        // User Info
        $widget['total_users']             = User::count();
        $widget['verified_users']          = User::active()->count();
        $widget['email_unverified_users']  = User::emailUnverified()->count();
        $widget['mobile_unverified_users'] = User::mobileUnverified()->count();

        // user Browsing, Country, Operating Log
        $userLoginData = UserLogin::where('created_at', '>=', Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);

        return view('admin.dashboard', compact('pageTitle', 'widget', 'chart'));
    }

    public function ticketStatistics(Request $request)
    {
        $pageTitle = 'Ticket Statistics';
        return view('admin.ticket_statistics', compact('pageTitle'));
    }

    public function ticketStatisticsWidget(Request $request)
    {
        $timeRange = $request->input('time_range');
        if ($timeRange == 'day') {
            $filterData = Carbon::now()->subDay();
        } elseif ($timeRange == 'week') {
            $filterData = Carbon::now()->subWeek();
        } elseif ($timeRange == 'month') {
            $filterData = Carbon::now()->subMonth();
        } elseif ($timeRange == 'year') {
            $filterData = Carbon::now()->subYear();
        } elseif ($timeRange == 'all') {
        }

        // new ticket 
        $newTickets = Ticket::where('ticket_status_id', Status::TICKET_OPEN);
        if ($timeRange != 'all') {
            $newTickets->whereDate('created_at', '>=', $filterData);
        }
        $widget['new_tickets'] = $newTickets->count();

        //clients replies
        $clientsReplies = Ticket::where('ticket_status_id', Status::TICKET_CUSTOMER_REPLY);
        if ($timeRange != 'all') {
            $clientsReplies->whereDate('created_at', '>=', $filterData);
        }
        $widget['clients_replies'] = $clientsReplies->count();

        //staff reply
        $staffReply = Ticket::whereNotIn('ticket_status_id', [Status::TICKET_OPEN, Status::TICKET_CUSTOMER_REPLY]);
        if ($timeRange != 'all') {
            $staffReply->whereDate('created_at', '>=', $filterData);
        }
        $widget['staff_replies'] = $staffReply->count();

        //ticket without reply
        $isAwaiting = TicketStatus::where('is_awaiting', Status::YES)->pluck('id');

        $ticketWithoutReply = Ticket::whereIn('ticket_status_id', $isAwaiting);
        if ($timeRange != 'all') {
            $ticketWithoutReply->whereDate('created_at', '>=', $filterData);
        }
        $widget['ticket_without_reply'] = $ticketWithoutReply->count();


        $supportTickets = Ticket::query();
        if ($timeRange != 'all') {
            $supportTickets->whereDate('created_at', '>=', $filterData);
        }



        $averageResponseTime = $supportTickets->where('first_reply_delay', '!=', 0)->avg('first_reply_delay');
        $widget['first_response_times'] = number_format($averageResponseTime / 3600, 2);

       
        //total ticket 
        $widget['total_tickets'] = Ticket::where('created_at', '>=', $filterData)->count();

        //total Staff
        $widget['total_staffs'] = Admin::where('is_staff', Status::YES)->count();

        //totalDepartment 
        $widget['total_departments'] = TicketDepartment::where('status', Status::YES)->count();

        return response()->json([
            'widget' => $widget,
        ]);
    }


    public function firstResponseChart(Request $request)
    {
        $timeRange = $request->input('time_range');
        if ($timeRange == 'day') {
            $filterData = Carbon::now()->subDay();
        } elseif ($timeRange == 'week') {
            $filterData = Carbon::now()->subWeek();
        } elseif ($timeRange == 'month') {
            $filterData = Carbon::now()->subMonth();
        } elseif ($timeRange == 'year') {
            $filterData = Carbon::now()->subYear();
        } elseif ($timeRange == 'all') {
        }

        $supportTickets = Ticket::query();
        if ($timeRange != 'all') {
            $supportTickets->whereDate('created_at', '>=', $filterData);
        }
        $tickets = $supportTickets->where('first_reply_delay', '!=', 0)->get();

        $firstResponseDayAvgHours = [
            '0-1 Hours'   => 0,
            '1-4 Hours'   => 0,
            '4-8 Hours'   => 0,
            '8-16 Hours'  => 0,
            '16-24 Hours' => 0,
            '24+ Hours'   => 0,
        ];
        $firstResponseDayAvgHours['0-1 Hours'] = $tickets->where('first_reply_delay', '<', 3600)->count();
        $firstResponseDayAvgHours['1-4 Hours'] = $tickets->where('first_reply_delay', '>', 3600)->where('first_reply_delay', '<', 3600 * 4)->count();
        $firstResponseDayAvgHours['4-8 Hours'] = $tickets->where('first_reply_delay', '>', 3600 * 4)->where('first_reply_delay', '<', 3600 * 8)->count();
        $firstResponseDayAvgHours['8-16 Hours'] = $tickets->where('first_reply_delay', '>', 3600 * 8)->where('first_reply_delay', '<', 3600 * 16)->count();
        $firstResponseDayAvgHours['16-24 Hours'] = $tickets->where('first_reply_delay', '>', 3600 * 16)->where('first_reply_delay', '<', 3600 * 24)->count();
        $firstResponseDayAvgHours['24+ Hours'] = $tickets->where('first_reply_delay', '>', 3600 * 24)->count();
        return response()->json([
            'firstResponseDayAvgHours' => $firstResponseDayAvgHours,
        ]);
    }


    public function ticketSubmittedByHours(Request $request)
    {
        $timeRange = $request->input('time_range');
        if ($timeRange == 'day') {
            $filterData = Carbon::now()->subDay();
        } elseif ($timeRange == 'week') {
            $filterData = Carbon::now()->subWeek();
        } elseif ($timeRange == 'month') {
            $filterData = Carbon::now()->subMonth();
        } elseif ($timeRange == 'year') {
            $filterData = Carbon::now()->subYear();
        } elseif ($timeRange == 'all') {
        }

        $supportTickets = Ticket::query();
        if ($timeRange != 'all') {
            $supportTickets->whereDate('created_at', '>=', $filterData);
        }
        $tickets      = $supportTickets->get();
        $totalTickets = $tickets->count();

        $ticketSubmittedByHours = [
            '00' => 0,
            '02' => 0,
            '04' => 0,
            '06' => 0,
            '08' => 0,
            '10' => 0,
            '12' => 0,
            '14' => 0,
            '16' => 0,
            '18' => 0,
            '20' => 0,
            '22' => 0,
            '24' => 0,
        ];

        foreach ($tickets as $ticket) {
            $createdHour = intval($ticket->created_at->format('H'));

            if ($createdHour == 0) {
                $ticketSubmittedByHours['00'] += 1;
            }
            if ($createdHour > 0 && $createdHour <= 2) {
                $ticketSubmittedByHours['02'] += 1;
            }

            if ($createdHour > 2 && $createdHour <= 4) {
                $ticketSubmittedByHours['04'] += 1;
            }
            if ($createdHour > 4 && $createdHour <= 6) {
                $ticketSubmittedByHours['06'] += 1;
            }
            if ($createdHour > 6 && $createdHour <= 8) {
                $ticketSubmittedByHours['08'] += 1;
            }
            if ($createdHour > 8 && $createdHour <= 10) {
                $ticketSubmittedByHours['10'] += 1;
            }
            if ($createdHour > 10 && $createdHour <= 12) {
                $ticketSubmittedByHours['12'] += 1;
            }
            if ($createdHour > 12 && $createdHour <= 14) {
                $ticketSubmittedByHours['14'] += 1;
            }
            if ($createdHour > 14 && $createdHour <= 16) {
                $ticketSubmittedByHours['16'] += 1;
            }
            if ($createdHour > 16 && $createdHour <= 18) {
                $ticketSubmittedByHours['18'] += 1;
            }
            if ($createdHour > 18  && $createdHour <= 20) {
                $ticketSubmittedByHours['20'] += 1;
            }
            if ($createdHour > 20 && $createdHour <= 22) {
                $ticketSubmittedByHours['22'] += 1;
            }
            if ($createdHour > 22 && $createdHour <= 24) {
                $ticketSubmittedByHours['24'] += 1;
            }
        }

        $hourPercentage = [];
        foreach ($ticketSubmittedByHours as $key => $value) {
            if ($totalTickets != 0) {
                $ticketSubmittedHourPercentage = ($value / $totalTickets) * 100;
                $hourPercentage[$key]          = $ticketSubmittedHourPercentage;
            } else {
                $hourPercentage[$key] = 0;
            }
        }

        $ticketSubmittedHours             = array_map('strval', array_keys($ticketSubmittedByHours));
        $ticketSubmittedByHoursPercentage = array_values($hourPercentage);
        $ticketSubmittedByHoursPercentage = array_map('round', $ticketSubmittedByHoursPercentage);

        return response()->json([
            'ticketSubmittedByHours' => $ticketSubmittedHours,
            'ticketSubmittedByHoursPercentage' => $ticketSubmittedByHoursPercentage,
        ]);
    }

    public function departmentWiseTickets(Request $request)
    {
        $timeRange = $request->input('time_range');
        if ($timeRange == 'day') {
            $filterData = Carbon::now()->subDay();
        } elseif ($timeRange == 'week') {
            $filterData = Carbon::now()->subWeek();
        } elseif ($timeRange == 'month') {
            $filterData = Carbon::now()->subMonth();
        } elseif ($timeRange == 'year') {
            $filterData = Carbon::now()->subYear();
        } elseif ($timeRange == 'all') {
            // $filterData = Ticket::get();
        }


        $supportTickets = Ticket::query();

        if ($timeRange != 'all') {
            $supportTickets->whereDate('created_at', '>=', $filterData);
        }
        $tickets = $supportTickets->get();

        $totalTickets = $tickets->count();

        $departmentTicketPercentage = [];

        $ticketsByDepartment = $tickets->groupBy('department.name');

        foreach ($ticketsByDepartment as $departmentName => $departmentTickets) {
            $ticketCount = $departmentTickets->count();
            $percentage = ($totalTickets != 0) ? round(($ticketCount / $totalTickets) * 100, 2) : 0;
            $departmentTicketPercentage[$departmentName] = $percentage;
        }

        return response()->json([
            'departmentName' => array_keys($departmentTicketPercentage),
            'departmentTicketPercentage' => array_values($departmentTicketPercentage),
        ]);
    }


    public function firstReplyByStaff(Request $request)
    {


        $timeRange = $request->input('time_range');
        if ($timeRange == 'day') {
            $filterData = Carbon::now()->subDay();
        } elseif ($timeRange == 'week') {
            $filterData = Carbon::now()->subWeek();
        } elseif ($timeRange == 'month') {
            $filterData = Carbon::now()->subMonth();
        } elseif ($timeRange == 'year') {
            $filterData = Carbon::now()->subYear();
        } elseif ($timeRange == 'all') {
            // $filterData = Ticket::get();
        }

        $supportTickets = Ticket::query();

        if ($timeRange != 'all') {
            $supportTickets->whereDate('created_at', '>=', $filterData);
        }

        $tickets = $supportTickets->where('first_reply_delay', '!=', 0)->where('first_reply_admin_id', '!=', 0)->get();


        $avgFirstReplyDelayByAdmin = $tickets->groupBy('first_reply_admin_id')
            ->map(function ($adminTickets, $adminId) {
                $adminName = Admin::find($adminId)->name;
                $avgDelayInSeconds = $adminTickets->avg('first_reply_delay');
                $avgDelayInHours = $avgDelayInSeconds / 3600;
                $roundedAvgDelayInHours = round($avgDelayInHours, 2);
                return [
                    'admin_name' => $adminName,
                    'avg_first_reply_delay' => $roundedAvgDelayInHours
                ];
            })
            ->pluck('avg_first_reply_delay', 'admin_name');

        $firstReplyStaffNames = $avgFirstReplyDelayByAdmin->keys();
        $staffFirstReplyAvgTime = $avgFirstReplyDelayByAdmin->values();

        return response()->json([
            'firstReplyStaffName' => $firstReplyStaffNames,
            'staffFirstReplyAvgTime' => $staffFirstReplyAvgTime,
        ]);
    }


    public function newTickets()
    {
        $pageTitle = 'New Tickets';
        $items = Ticket::where('ticket_status_id', Status::TICKET_OPEN)->orderBy('id', 'desc')->with('user', 'replies')->get();
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function clientsReplies()
    {
        $pageTitle = 'Clients Replies';
        $items =  Ticket::where('ticket_status_id', Status::TICKET_CUSTOMER_REPLY)->orderBy('id', 'desc')->with('user', 'replies')->get();
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }

    public function staffReplies()
    {
        $pageTitle = 'Staff Replies';
        $items =  Ticket::whereNotIn('ticket_status_id', [Status::TICKET_OPEN, Status::TICKET_CUSTOMER_REPLY])->orderBy('id', 'desc')->with('user', 'replies')->get();
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }
    public function withoutReplies()
    {
        $pageTitle = 'Without Replies';

        $isAwaiting = TicketStatus::where('is_awaiting', Status::YES)->pluck('id');
        $items = Ticket::with('user')->whereIn('ticket_status_id', $isAwaiting)->get();
        return view('admin.support.tickets', compact('items', 'pageTitle'));
    }


    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ]);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('admin.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('admin.password')->withNotify($notify);
    }

    public function notifications()
    {
        $notifications = AdminNotification::orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        $pageTitle = 'Notifications';
        return view('admin.notifications', compact('pageTitle', 'notifications'));
    }


    public function notificationRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function requestReport()
    {
        $pageTitle = 'Your Listed Report & Request';
        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASECODE');
        $url = "https://license.viserlab.com/issue/get?" . http_build_query($arr);
        $response = CurlRequest::curlContent($url);
        $response = json_decode($response);
        if ($response->status == 'error') {
            return to_route('admin.dashboard')->withErrors($response->message);
        }
        $reports = $response->message[0];
        return view('admin.reports', compact('reports', 'pageTitle'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bug,feature',
            'message' => 'required',
        ]);
        $url = 'https://license.viserlab.com/issue/add';

        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASECODE');
        $arr['req_type'] = $request->type;
        $arr['message'] = $request->message;
        $response = CurlRequest::curlPostContent($url, $arr);
        $response = json_decode($response);
        if ($response->status == 'error') {
            return back()->withErrors($response->message);
        }
        $notify[] = ['success', $response->message];
        return back()->withNotify($notify);
    }

    public function readAll()
    {
        AdminNotification::where('is_read', Status::NO)->update([
            'is_read' => Status::YES
        ]);
        $notify[] = ['success', 'Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        $mimetype = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }
}
