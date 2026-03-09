<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use GlobalStatus;

    protected $appends = ['image_url'];

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'room_type_amenities', 'amenities_id');
    }

    public function hotelSettings()
    {
        return $this->belongsToMany(HotelSetting::class, 'hotel_setting_amenities', 'amenities_id');
    }

    public function imageUrl(): Attribute
    {
        return new Attribute(
            get: function () {
                return getImage(getFilePath('amenity') . '/' . @$this->image, getFileSize('amenity'));
            }
        );
    }
}
