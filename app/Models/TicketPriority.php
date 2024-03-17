<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\SortOrder;
use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    use GlobalStatus, SortOrder;
}
