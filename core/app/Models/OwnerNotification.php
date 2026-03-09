<?php

namespace App\Models;

use App\Traits\CurrentOwner;
use Illuminate\Database\Eloquent\Model;

class OwnerNotification extends Model
{
    use CurrentOwner;
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
