<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\ApiQuery;
use App\Traits\CurrentOwner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Booking extends Model
{
    use CurrentOwner, ApiQuery;

    protected $casts = [
        'contact_info' => 'object',
        'checked_out_at' => 'datetime'
    ];

    protected $appends = ['total_amount', 'due_amount', 'refundable_amount', 'tax_percent'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function bookingRequest()
    {
        return $this->hasOne(BookingRequest::class);
    }

    public function approvedBy()
    {
        return $this->hasOne(BookingActionHistory::class)->where('remark', 'approve_booking_request');
    }

    public function bookedBy()
    {
        return $this->hasOne(BookingActionHistory::class)->where('remark', 'book_room');
    }

    public function checkedOutBy()
    {
        return $this->hasOne(BookingActionHistory::class)->where('remark', 'checked_out');
    }

    public function canceledBy()
    {
        return $this->hasOne(BookingActionHistory::class)->where('remark', 'cancel_booking');
    }

    public function bookedRooms()
    {
        return $this->hasMany(BookedRoom::class, 'booking_id');
    }

    public function activeBookedRooms()
    {
        return $this->hasMany(BookedRoom::class, 'booking_id')->where('status', Status::ROOM_ACTIVE);
    }

    public function usedExtraService()
    {
        return $this->hasMany(UsedExtraService::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::BOOKING_ACTIVE);
    }

    public function scopeCheckedOut($query)
    {
        return $query->where('status', Status::BOOKING_CHECKOUT);
    }

    public function scopeDelayedCheckout($query)
    {
        $query->active()->where(function ($booking) {
            $booking->where(function ($booking) {
                $booking->whereDate('check_out', '<', now());
            })->orWhere(function ($booking) {
                $booking->whereDate('check_out', '=', now())
                    ->where(function ($booking) {
                        if (date('H:i:s') > @hotelSetting()->checkout_time) {
                            return $booking;
                        } else {
                            return $booking->where('id', '0');
                        }
                    });
            });
        });
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', Status::BOOKING_CANCELED);
    }

    public function scopeTodayCheckIn($query)
    {
        return $query->whereDate('check_in', now());
    }

    public function scopeTodayCheckout($query)
    {
        return $query->whereDate('check_out', now());
    }

    public function scopeRefundable($query)
{
    return $query->canceled()->whereRaw('(paid_amount - (booking_fare + tax_charge + service_cost + extra_charge + cancellation_fee - extra_charge_subtracted - total_discount)) > 0');
}

    public function scopeKeyGiven($query)
    {
        return $query->where('key_status', Status::KEY_GIVEN);
    }

    public function scopeKeyNotGiven($query)
    {
        return $query->where('key_status', Status::KEY_NOT_GIVEN);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            function () {
                if (now() >= $this->check_in && $this->status == Status::BOOKING_ACTIVE) {
                    $class = "badge--success";
                    $text = 'Running';
                } elseif (now() < $this->check_in && $this->status == Status::BOOKING_ACTIVE) {
                    $class = "badge--warning";
                    $text = 'Upcoming';
                } elseif ($this->status == Status::BOOKING_CANCELED) {
                    $class = "badge--danger";
                    $text = 'Canceled';
                } else {
                    $class = "badge--dark";
                    $text = 'Checked Out';
                }

                $html = "<small class='badge $class'>" . trans($text) . "</small>";
                return $html;
            }
        );
    }

    public function statusCustomBadge(): Attribute
    {
        return new Attribute(
            function () {
                if (now() >= $this->check_in && $this->status == Status::BOOKING_ACTIVE) {
                    $class = "bg--success";
                    $text = 'Running';
                } elseif (now() < $this->check_in && $this->status == Status::BOOKING_ACTIVE) {
                    $class = "bg--warning";
                    $text = 'Upcoming';
                } elseif ($this->status == Status::BOOKING_CANCELED) {
                    $class = "bg--danger";
                    $text = 'Canceled';
                } else {
                    $class = "bg--dark";
                    $text = 'Checked Out';
                }
                return "<span class='booking-card__badge $class'>" . trans($text) . "</span>";
            }
        );
    }

    public function totalAmount(): Attribute
    {
        return new Attribute(
            function () {
                return getAmount($this->booking_fare + $this->tax_charge + $this->service_cost + $this->extra_charge + $this->cancellation_fee - $this->extra_charge_subtracted - $this->total_discount);
            }
        );
    }

    public function dueAmount(): Attribute
{
    return new Attribute(
        function () {

            $due = $this->total_amount - $this->paid_amount;

            // prevent negative receivable
            if ($due < 0) {
                return 0;
            }

            return getAmount($due);
        }
    );
}

public function refundableAmount(): Attribute
{
    return new Attribute(
        function () {

            $refund = $this->paid_amount - $this->total_amount;

            if ($refund > 0) {
                return getAmount($refund);
            }

            return 0;
        }
    );
}

    public function taxPercent(): Attribute
    {
        return new Attribute(
            function () {
                if ($this->tax_charge == 0) {
                    return 0;
                }
                return $this->tax_charge * 100 / ($this->booking_fare - $this->total_discount);
            }
        );
    }

    public function isDelayed()
    {
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        if ($this->status == Status::BOOKING_ACTIVE && ($this->check_out < $currentDate || ($this->check_out == $currentDate && $currentTime > hotelSetting()->checkout_time))) {
            return 1;
        } else {
            return 0;
        }
    }

    public function extraCharge()
    {
        return getAmount($this->extra_charge - $this->extra_charge_subtracted);
    }

    public function taxPercentage()
    {
        if ($this->tax_charge == 0) {
            return 0;
        }
        return $this->tax_charge * 100 / ($this->booking_fare - $this->total_discount);
    }

    public function createActionHistory($remark, $details = null)
    {
        $bookingActionHistory              = new BookingActionHistory();
        $bookingActionHistory->booking_id  = $this->id;
        $bookingActionHistory->remark      = $remark;
        $bookingActionHistory->details     = $details;
        $bookingActionHistory->owner_id    = getOwnerParentId();
        $bookingActionHistory->action_by   = authOwner()->id;
        $bookingActionHistory->save();
    }

    public function createPaymentLog($amount, $type, $paymentSystem = null, $isUser = false, $ownerId = 0)
    {
        $paymentLog              = new PaymentLog();
        $paymentLog->booking_id  = $this->id;
        $paymentLog->amount      = $amount;
        $paymentLog->type        = $type;
        $paymentLog->payment_system = $paymentSystem ?? 'Cash Payment';
        $paymentLog->owner_id    = $ownerId ?? getOwnerParentId();
        $paymentLog->action_by   = $isUser ? 0 : authOwner()->id;
        $paymentLog->save();
    }

    public function stayingDays()
    {
        return Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out), false);
    }
}
