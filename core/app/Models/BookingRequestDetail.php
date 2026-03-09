<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRequestDetail extends Model
{
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookingRequest()
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function totalFare()
    {
        return $this->total_amount + $this->discount - $this->tax_charge;
    }

    public function taxCharge()
    {
        return $this->tax_charge * $this->unit_fare / $this->totalFare();
    }

    public function unitDiscount()
    {
        return $this->discount * $this->unit_fare / $this->totalFare();
    }
}
