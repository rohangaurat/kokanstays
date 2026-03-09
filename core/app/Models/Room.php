<?php

namespace App\Models;

use App\Traits\CurrentOwner;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Room extends Model {
    use GlobalStatus, CurrentOwner;

    protected $fillable = ['id'];

    public function roomType() {
        return $this->belongsTo(RoomType::class);
    }

    public function booked() {
        return $this->hasMany(BookedRoom::class, 'room_id');
    }

    public function discountAmount() {
        $discount = $this->roomType->fare * $this->roomType->discount_percentage / 100;
        return $discount;
    }

    public function scopeAvailableRoomCount($query, $checkIn = null, $checkOut = null) {
        if (!$checkIn && !$checkOut) {
            $checkIn  = now();
            $checkOut = now();
        }
        return $query->active()->whereDoesntHave('booked.booking', function ($booking) use ($checkIn, $checkOut) {
            $booking->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)
                    ->where('check_out', '>', $checkIn);
            });
        })->count();
    }

    public function scopeIsAvailableRoom($query, $checkIn = null, $checkOut = null) {
        if (!$checkIn && !$checkOut) {
            $checkIn  = now();
            $checkOut = now();
        }
        return $query->active()->whereDoesntHave('booked.booking', function ($booking) use ($checkIn, $checkOut) {
            $booking->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in', '<', $checkOut)->where('check_out', '>', $checkIn);
            });
        });
    }
}
