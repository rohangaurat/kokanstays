<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\CurrentOwner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BookingRequest extends Model
{
    use CurrentOwner;

    protected $fillable = ['id'];

    protected $casts = [
        'room_type_details' => 'object',
        'contact_info' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function bookingRequestDetails()
    {
        return $this->hasMany(BookingRequestDetail::class);
    }

    //scope
    public function scopeInitial($query)
    {
        return $query->where('status', Status::BOOKING_REQUEST_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::BOOKING_REQUEST_APPROVED);
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', Status::BOOKING_REQUEST_CANCELED);
    }

    public function statusBadge(): Attribute
    {
        $className = 'badge badge--';
        if ($this->status == Status::BOOKING_REQUEST_PENDING) {
            $className .= 'warning';
            $text = 'Pending';
        } elseif ($this->status == Status::BOOKING_REQUEST_APPROVED) {
            $className .= 'success';
            $text = 'Approved';
        } elseif ($this->status == Status::BOOKING_REQUEST_CANCELED) {
            $className .= 'danger';
            $text = 'Canceled';
        }

        if(Carbon::parse($this->check_in) < today()){
            $className = 'dark';
            $text = 'Expired';
        }

        return new Attribute(
            get: fn () => "<span class='badge badge--$className'>" . trans($text) . "</span>",
        );
    }

    public function customStatusBadge(): Attribute
    {
        $className = 'bg--';
        if ($this->status == Status::BOOKING_REQUEST_PENDING) {
            $className .= 'warning';
            $text = 'Pending';
        } elseif ($this->status == Status::BOOKING_REQUEST_APPROVED) {
            $className .= 'success';
            $text = 'Approved';
        } elseif ($this->status == Status::BOOKING_REQUEST_CANCELED) {
            $className .= 'danger';
            $text = 'Canceled';
        }

        if(Carbon::parse($this->check_in) < today()){
            $className = 'bg--dark';
            $text = 'Expired';
        }

        return new Attribute(
            get: fn () => "<span class='booking-card__badge $className'>" . trans($text) . "</span>",
        );
    }

    function bookFor()
    {
        return  diffInDays($this->check_in, $this->check_out);
    }

    function totalRoom()
    {
        return $this->bookingRequestDetails->sum('number_of_rooms');
    }

    public function taxPercentage()
    {
        $firstItem = $this->bookingRequestDetails->first();
        $tax = $firstItem->tax_charge * 100 / ($firstItem->total_amount - $firstItem->tax_charge);
        return getAmount($tax);
    }

    public function taxCharge()
    {
        return $this->bookingRequestDetails->sum('tax_charge');
    }

    public function discountAmount()
    {
        return $this->bookingRequestDetails->sum('discount');
    }

    public function bookingFare()
    {
        return $this->total_amount - $this->taxCharge() + $this->discountAmount();
    }

    public function totalGuest(): Attribute
    {
        return new Attribute(
            get: fn () => $this->total_adult + $this->total_child
        );
    }
}
