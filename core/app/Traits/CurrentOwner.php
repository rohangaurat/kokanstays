<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait CurrentOwner
{
    public function scopeCurrentOwner($query, $relation = null)
    {
        if ($relation) {
            return $query->whereHas($relation, function ($q) {
                $q->currentOwner();
            });
        }

        if (Schema::hasColumn($query->from, 'owner_id')) {
            return $query->where('owner_id', getOwnerParentId());
        }

        return $query;
    }
}