<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\SortOrder;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use GlobalStatus, SortOrder;

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
