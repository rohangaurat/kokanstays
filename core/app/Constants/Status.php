<?php

namespace App\Constants;

class Status
{
    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    const TICKET_OPEN = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY = 2;
    const TICKET_CLOSE = 3;

    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM = 3;

    const ROOM_TYPE_ACTIVE   = 1;
    const ROOM_TYPE_FEATURED = 1;

    const ROOM_ACTIVE    = 1;
    const ROOM_CANCELED = 3;
    const ROOM_EXPIRED = 5;
    const ROOM_CHECKOUT  = 9;

    const BOOKING_ACTIVE    = 1;
    const BOOKING_CANCELED = 3;
    const BOOKING_CHECKOUT  = 9;

    const BOOKING_REQUEST_PENDING  = 0;
    const BOOKING_REQUEST_APPROVED  = 1;
    const BOOKING_REQUEST_CANCELED = 3;

    const KEY_NOT_GIVEN = 0;
    const KEY_GIVEN = 1;

    const USER_REQUEST   = 2;

    const REVIEW_TYPE_USER  = 1;
    const REVIEW_TYPE_OWNER = 2;
}
