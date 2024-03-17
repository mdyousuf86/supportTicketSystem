<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    protected $casts = [
        'extra_fields' => "object",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function department()
    {
        return $this->belongsTo(TicketDepartment::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'first_reply_admin_id');
    }
}
