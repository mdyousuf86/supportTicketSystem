<?php

namespace App\Models;

use App\Traits\SortOrder;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class TicketDepartment extends Model
{
    use GlobalStatus, SortOrder;
    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function staffs()
    {
        return $this->belongsToMany(Admin::class);
    }
}
