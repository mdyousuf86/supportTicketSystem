<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class TicketFeedback extends Model
{
    use Searchable;
    public function tickets()
    {
        return $this->belongsTo(Ticket::class,'ticket_id');
    }

}
