<?php

namespace App\Models;

use App\Traits\CurrentOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class HotelSetting extends Model
{
    use CurrentOwner;

    protected $casts = [
        'keywords'     => 'array',
        'facilities'   => 'object',
        'complements'  => 'object',
        'amenities'    => 'object',
        'instructions' => 'object',
        'child_policy' => 'object',
        'pet_policy'   => 'object',
        'other_policy' => 'object'
    ];

    protected $appends = ['image_url'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'hotel_setting_amenities', 'hotel_setting_id', 'amenities_id')->withTimestamps();
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'hotel_setting_facilities', 'hotel_setting_id', 'facility_id')->withTimestamps();
    }

    public function imageUrl(): Attribute
    {
        return new Attribute(
            get: function () {
                return getImage(getFilePath('hotelImage') . '/' . $this->image);
            }
        );
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function () {
            session()->forget('hotelSetting');
        });
    }
}
