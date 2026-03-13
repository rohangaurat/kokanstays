<?php

namespace App\Models;

use App\Traits\CurrentOwner;
use Illuminate\Database\Eloquent\Model;

class OwnerNotification extends Model
{
    use CurrentOwner;

    protected $fillable = [
        'owner_id',
        'user_id',
        'title',
        'click_url',
        'is_read'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}