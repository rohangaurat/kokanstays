<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\CurrentOwner;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model {
    use GlobalStatus, CurrentOwner;

    protected $casts = [
        'keywords' => 'array',
        'beds'     => 'array',
    ];
    protected $appends = ['image', 'discounted_fare', 'discount'];

    public function image(): Attribute {
        return new Attribute(
            get: function () {
                $image = $this->images->first();
                return getImage(getFilePath('roomTypeImage') . '/' . @$image->image);
            }
        );
    }

    public function discount(): Attribute {
        return new Attribute(
            get: function () {
                return $this->fare * $this->discount_percentage / 100;
            }
        );
    }

    public function discountedFare(): Attribute {
        return new Attribute(
            get: function () {
                $discount = 0;
                if ($this->discount_percentage) {
                    $discount = $this->fare * $this->discount_percentage / 100;
                }
                return $this->fare - $discount;
            }
        );
    }

    public function amenities() {
        return $this->belongsToMany(Amenity::class, 'room_type_amenities', 'room_type_id', 'amenities_id')->withTimestamps();
    }

    public function facilities() {
        return $this->belongsToMany(Facility::class, 'room_type_facilities', 'room_type_id', 'facility_id')->withTimestamps();
    }

    public function rooms() {
        return $this->hasMany(Room::class);
    }

    public function activeRooms() {
        return $this->hasMany(Room::class)->active();
    }

    public function images() {
        return $this->hasMany(RoomTypeImage::class);
    }

    public function bookedRooms() {
        return $this->hasMany(BookedRoom::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function owner() {
        return $this->belongsTo(Owner::class);
    }

    public function scopeActiveBookedRooms() {
        return $this->bookedRooms()->active();
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', Status::ROOM_TYPE_FEATURED);
    }

    public function featureBadge(): Attribute {
        return new Attribute(
            function () {
                $html = '';

                if ($this->is_featured == Status::ROOM_TYPE_FEATURED) {
                    $html = '<span class="badge badge--primary">' . trans('Featured') . '</span>';
                } else {
                    $html = '<span class="badge badge--dark">' . trans('Unfeatured') . '</span>';
                }

                return $html;
            }
        );
    }

    public function dealShowingBadge(): Attribute {
        return new Attribute(
            function () {
                $html = '';

                if ($this->deal_shown_status == Status::ENABLE) {
                    $html = '<span class="badge badge--success">' . trans('Shown') . '</span>';
                } else {
                    $html = '<span class="badge badge--dark">' . trans('Hidden') . '</span>';
                }

                return $html;
            }
        );
    }

    public function bedCount() {
        return array_count_values($this->beds);
    }
}
