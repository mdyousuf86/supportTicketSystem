<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    public function supportMessage()
    {
        return $this->belongsTo(TicketReply::class, 'ticket_id');
    }
}
