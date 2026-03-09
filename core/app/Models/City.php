<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class City extends Model
{
    use ApiQuery, GlobalStatus;

    protected $appends = ['image_url'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function hotelSettings()
    {
        return $this->hasMany(HotelSetting::class);
    }

    //scope
    public function scopePopular($query)
    {
        $query->where('is_popular', Status::YES);
    }

    public function imageUrl(): Attribute
    {
        return new Attribute(
            get: function () {
                return getImage(getFilePath('city') . '/' . $this->image);
            }
        );
    }

    public function popularBadge(): Attribute
    {
        return new Attribute(
            function () {
                $html = '';
                if ($this->is_popular == Status::YES) {
                    $html = '<span class="badge badge--primary">' . trans('Yes') . '</span>';
                } else {
                    $html = '<span><span class="badge badge--dark">' . trans('No') . '</span></span>';
                }
                return $html;
            }
        );
    }
}
