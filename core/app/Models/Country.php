<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Country extends Model {
    use GlobalStatus;

    public function cities() {
        return $this->hasMany(City::class);
    }

    public function scopeWhereHasCities($query) {
        return $query->whereHas('cities', function ($cities) {
            $cities->whereHas('locations');
        });
    }
}
