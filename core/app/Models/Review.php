<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function scopeReviews($query)
    {
        return $query->where('parent_id', 0);
    }

    public function scopeWhereHasAuthOwner($query, $ownerId)
    {
        return $query->whereHas('owner', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        });
    }
}
