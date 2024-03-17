<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class PredefinedReply extends Model
{
    use GlobalStatus;

    public function predefinedReplyCategory()
    {
        return $this->belongsTo(PredefinedReplyCategory::class);
    }
}
