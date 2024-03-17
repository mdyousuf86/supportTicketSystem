<?php

namespace App\Models;


use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use GlobalStatus, Searchable;
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function departments()
    {
        return $this->belongsToMany(TicketDepartment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }


    public function role(){
        return $this->belongsTo(Role::class);
    }
    
}
