<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\CurrentOwner;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use CurrentOwner, ApiQuery;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

}
