<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_reply_id', 'id');
    }
}
