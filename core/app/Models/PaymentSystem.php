<?php

namespace App\Models;

use App\Traits\CurrentOwner;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class PaymentSystem extends Model
{
    use GlobalStatus, CurrentOwner;
}
