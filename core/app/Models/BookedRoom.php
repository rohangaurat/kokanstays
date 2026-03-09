<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\CurrentOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BookedRoom extends Model
{
    use CurrentOwner;

    protected $guarded = ['id'];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function roomType(){
        return $this->belongsTo(RoomType::class);
    }

    public function extraServices()
    {
        return $this->hasMany(UsedExtraService::class);
    }

    //scope
    public function scopeActive($query)
    {
        return $query->where('status', Status::ROOM_ACTIVE);
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', Status::ROOM_CHECKOUT);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', Status::ROOM_CANCELED);
    }


    public function statusBadge(): Attribute
    {
        $className = 'badge badge--';
        if ($this->status == Status::ROOM_ACTIVE) {
            $className .= 'success';
            $text = 'Booked';
        } elseif ($this->status == Status::ROOM_CANCELED) {
            $className .= 'danger';
            $text = 'Canceled';
        } elseif ($this->status == Status::ROOM_EXPIRED) {
            $className .= 'danger';
            $text = 'Expired';
        } elseif ($this->status == Status::ROOM_CHECKOUT) {
            $className .= 'dark';
            $text = 'Checked Out';
        } else {
            $className .= 'warning';
            $text = 'Booking Request';
        }

        return new Attribute(
            get: fn () => "<span class='$className'>" . trans($text) . "</span>",
        );
    }
}
