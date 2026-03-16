KOKANSTAYS – CUSTOM LOG

### 2026-03-16
### Fix Multi-Guard Session Conflict in Subscription Payment

**File Modified**

core/app/Http/Controllers/Gateway/PaymentController.php

**Issue**

Vendor subscription payments sometimes triggered validation errors for booking payments when a user session existed in another browser tab.

Errors included:

* The amount field is required.
* The booking id field is required.

**Cause**

The `currentAuth()` helper prioritized the `user` guard when both `user` and `owner` sessions were active.

**Fix**

Force the request to use the `owner` guard when the subscription form is submitted.

```php
$currentAuth = currentAuth();

if ($request->has('pay_for_month')) {
    $currentAuth['type'] = 'owner';
}
```

**Result**

Vendor subscription payments now work correctly even when a user session exists in another tab.


### Change: Default Booking Date Range to 1 Night

**File Modified**

```
core/resources/views/templates/basic/partials/booking_filter.blade.php
```

**Previous Behavior**

* Homepage booking filter defaulted to **30-day stay**.
* Example: `16 Mar 2026 → 15 Apr 2026`.

**New Behavior**

* Default booking range set to **1 night stay**.
* Example: `16 Mar 2026 → 17 Mar 2026`.

**Reason**

* Improves user experience and aligns with common hotel booking platforms (Booking.com / Airbnb).
* Prevents unrealistic long default booking ranges.

**Code Change**

Before:

```javascript
if (!checkOutDateRaw) {
    checkOutDate.setDate(checkOutDate.getDate() + 30);
}
```

After:

```javascript
if (!checkOutDateRaw) {
    checkOutDate.setDate(checkOutDate.getDate() + 1);
}
```

**Impact**

* Homepage search now defaults to **1 night stay**.
* Users can still select longer stays manually via the date picker.

## 2026-03-16 – Booking Request System Improvements

### Fixed: Booking Request Deletion Issue
Expired and cancelled booking requests were returning a 404 error when users attempted to delete them.

**Cause**
Deletion logic was restricted to only `initial()` booking requests.

**Old code**
BookingRequest::initial()
->where('user_id', auth()->id())
->findOrFail($id);

**Fix**
Removed `initial()` scope so users can delete their own expired or cancelled booking requests.

**New code**
BookingRequest::where('user_id', auth()->id())
->findOrFail($id);

File modified:
core/app/Http/Controllers/User/BookingController.php


---

### Fixed: Validation Rule for Checkout Date

Incorrect validation rule used `after:check_in`.

**Old**
'checkout' => 'required|date_format:Y-m-d|after:check_in'

**New**
'checkout' => 'required|date_format:Y-m-d|after:checkin'


---

### Improvement: Prevent Invalid Room Quantity

Added validation to skip room types with zero quantity.

**Added code**
if ($roomCount <= 0) {
    continue;
}


---

### Improvement: Prevent Zero-Night Stay Calculation

Added protection to ensure minimum 1 night calculation.

**Added code**
$stayingDays = max(1, diffInDays($checkIn, $checkout));


---

### Improvement: Owner Existence Validation

Added safety check to prevent booking requests for unavailable or expired hotels.

**Added code**
if (!$owner) {
    $notify[] = ['error', 'Hotel not available'];
    return back()->withNotify($notify);
}


---

### Security Improvement: Restrict Booking Request Deletion

Users can now only delete their own booking requests.

**Implemented**
BookingRequest::where('user_id', auth()->id())
->findOrFail($id);

### Fix: Offline Hotel Payment Label Missing in User Payment History

**Issue**

On the user payment history page:

```
/user/payment/history
```

Offline payments recorded by hotel staff (`method_code = 0`) were showing **no payment method label**, only the transaction ID and amount.

Example:

```
#WUSNLEVJLMZ3
₹50
```

This created confusion because the payment was actually **Cash Payment at Hotel**.

---

**Cause**

The payment history view was displaying the gateway name using:

```php
$deposit->gateway->name
```

However, offline payments (`method_code = 0`) do not have an associated gateway, so the label appeared blank.

---

**Solution**

Updated the payment method display logic to handle offline/manual payments separately.

**File Modified**

```
core/resources/views/templates/basic/user/deposit_history.blade.php
```

**Before**

```php
@if ($deposit->method_code < 5000)
    {{ __($deposit->gateway->name ?? '') }}
@else
    @lang('Google Pay')
@endif
```

**After**

```php
@if ($deposit->method_code == 0)
    {{ $deposit->detail ?? __('Offline Payment at Hotel') }}
@elseif ($deposit->method_code < 5000)
    {{ __($deposit->gateway->name ?? '') }}
@else
    @lang('Google Pay')
@endif
```

---

**Result**

Offline payments now display correctly in user payment history.

Example:

```
Cash Payment
#WUSNLEVJLMZ3
₹50
```

If no detail is stored, the system shows:

```
Offline Payment at Hotel
```

---

**Impact**

* Improves clarity for guests viewing payment history.
* Supports manual hotel payments such as **Cash, Hotel UPI, or Reception payments**.
* Keeps gateway-based payments unchanged.

---


### Fix: Admin Vendor Detail Booking Count Mismatch

**Issue**

On the Admin panel vendor detail page:

```
/admin/hotels/detail/{id}
```

The **Total Bookings** widget was showing `0`, while the Vendor Dashboard correctly displayed the actual number of bookings.

Example:

| Panel               | Total Bookings |
| ------------------- | -------------- |
| Vendor Dashboard    | 3              |
| Admin Vendor Detail | 0              |

---

**Cause**

The Admin controller was counting only **active bookings** using:

```php
Booking::active()->where('owner_id', $owner->id)->count();
```

However, the Vendor Dashboard counts **all bookings**, regardless of status.

If bookings are completed, checked-out, cancelled, or pending, they are not included in `active()` scope, resulting in an incorrect count.

---

**Solution**

Updated the query to count **all bookings** instead of only active ones.

**File Modified**

```
core/app/Http/Controllers/Admin/ManageOwnersController.php
```

**Before**

```php
$widget['total_booking'] = Booking::active()->where('owner_id', $owner->id)->count();
```

**After**

```php
$widget['total_booking'] = Booking::where('owner_id', $owner->id)->count();
```

---

**Result**

Admin Vendor Detail page now correctly shows the same booking count as the Vendor Dashboard.

Example:

| Panel               | Total Bookings |
| ------------------- | -------------- |
| Vendor Dashboard    | 3              |
| Admin Vendor Detail | 3              |

---

**Impact**

* Fixes incorrect booking statistics in the Admin panel.
* Keeps Admin and Vendor dashboards consistent.
* No database structure changes required.

---


### Invoice Improvements

Files:
- invoice.blade.php
- invoice_calculation_summary.blade.php
- invoice.css

Changes:
- Professional invoice layout
- Payment tables (received / refunded)
- Display payment receiver (Hotel / Platform)

### Payment Logic Fix

File:
core/app/Http/Controllers/Gateway/PaymentController.php

Gateway payments now recorded with owner_id = 0 (Platform)
instead of owner_id = vendor.

### Billing Fix – Refund report visibility

Issue:
Refund entries in `payment_logs` were saved with incorrect `owner_id`, causing them not to appear in Vendor → Reports → Returned Payments.

Fix:
Updated `createPaymentLog()` in `core/app/Models/Booking.php` to always assign the correct owner using `getOwnerParentId()`.

Impact:
Refund transactions now appear correctly in vendor reports, regardless of whether processed by vendor or staff.


#### Fix: Cancelled bookings appearing in Today's Checkout

Updated `scopeTodayCheckout()` in `Booking` model to include only active bookings.

Before:
Returned all bookings with checkout date today including cancelled bookings.

After:
Filters only `BOOKING_ACTIVE` bookings.

File:
core/app/Models/Booking.php

## 2026-03-16

### Booking Payment & Refund System Improvements

Implemented fixes for booking payment, cancellation, and refund handling.

#### Fixes
- Corrected `due_amount` and `refundable_amount` calculation in `Booking` model.
- Fixed refund processing logic in `ManageBookingController`.
- Prevented negative receivable amounts.
- Allowed proper refund when `paid_amount > total_amount`.
- Updated bill payment screen to correctly show **Refund Amount** instead of receivable amount.
- Improved booking payment UI logic for refund vs receive payment.
- Updated vendor booking details view to correctly display payment and refund summary.
- Fixed API booking details response for mobile app to reflect correct totals after refund.

#### Result
Booking accounting now behaves correctly:

Example:

| Item | Amount |
|-----|------|
Room Fare | ₹2  
Canceled Fare | -₹2  
Total Amount | ₹0  
Payment Received | ₹2  
Refunded | ₹2  
Receivable | ₹0  

System now correctly logs:

- `BOOKING_PAYMENT_RECEIVED`
- `BOOKING_PAYMENT_RETURNED`

and balances booking payment totals.
----
### Fix: Owner notification error (Undefined constant BOOKING_REQUEST_INITIAL)

Date: 15 Mar 2026

File:
core/app/Http/Controllers/Owner/OwnerController.php

Issue:
Notification link `/vendor/notification/read/{id}` caused a fatal error:
Undefined constant Status::BOOKING_REQUEST_INITIAL

Reason:
The constant `BOOKING_REQUEST_INITIAL` does not exist in `App\Constants\Status`.

Solution:
Replaced it with `Status::BOOKING_REQUEST_PENDING`.

Old:
Status::BOOKING_REQUEST_INITIAL

New:
Status::BOOKING_REQUEST_PENDING

### Fix: Show "Admin" in Vendor Booking Actions when payment approved by admin

**Date:** 15 Mar 2026
**Project:** kokanstays.com (Multi-Hotel SaaS)

#### Problem

When a guest paid the remaining booking amount via **Bank Transfer to KokanStays**, the **admin approves the payment** from the admin panel.
In the vendor panel **Booking Actions** page:

`/vendor/report/booking-actions`

the **Action By** column appeared **blank**, because the database stores:

```
action_by = 0
```

which represents **Admin**, but the Blade template only attempted to display the related `Owner` model.

Example database rows:

| id | action_by | remark           |
| -- | --------- | ---------------- |
| 11 | 0         | payment_approved |
| 10 | 1         | key_handover     |

So when `action_by = 0`, no owner existed and nothing was displayed.

---

#### Solution

Handle `action_by = 0` directly in the Blade view and display **Admin**.

File modified:

```
core/resources/views/owner/reports/booking_actions.blade.php
```

Original code:

```php
<td>{{ __(@$log->actionBy->fullname) }}</td>
```

Updated code:

```php
{{-- Fix: action_by = 0 means Admin approved the action (e.g., bank transfer payment approval) --}}
<td>
    {{ $log->action_by == 0 ? 'Admin' : optional($log->actionBy)->fullname }}
</td>
```

---

#### Result

Vendor booking actions now correctly show:

| Booking      | Details          | Action By       |
| ------------ | ---------------- | --------------- |
| U4M84GOB44LC | Payment approved | Admin           |
| U4M84GOB44LC | Key handover     | Prathamesh More |

---

#### Notes

* `action_by = 0` is used internally to represent **Admin actions**.
* No database or model changes were required.
* Only the Blade view was adjusted to correctly display Admin.


### Payment Gateway UI Improvement
**File:** core/resources/views/templates/basic/user/payment/deposit.blade.php

**Changes:**
- Updated payment gateway display labels to improve UI consistency.
- Replaced gateway name text (e.g., bank name) with a cleaner **"Pay via"** label.
- Gateway logos (Razorpay, Paytm, Instamojo, Bank Transfer) remain visible to identify the payment method.
- Improves visual consistency of payment gateway cards.

**Reason:**
To create a cleaner and more uniform payment gateway selection interface for users during booking payments.

## 2026-03-15 - Google Maps location fix on hotel detail page

### Issue
On the hotel detail page, clicking the map marker showed the message **"Place info couldn't load"** instead of displaying location details.

### Cause
The Google Maps iframe was using a coordinate-only query:
https://www.google.com/maps?q=LAT,LNG&output=embed

This sometimes prevents Google from loading place information when the marker is clicked.

### Fix
Updated the map embed to use a proper query including the hotel name and coordinates.

### File Modified
core/resources/views/templates/basic/hotel_detail.blade.php

### Updated Code
```html
<iframe
    width="100%"
    height="450"
    style="border:0"
    loading="lazy"
    allowfullscreen
    src="https://www.google.com/maps?q={{ urlencode($hotel->hotelSetting->name) }},{{ $hotel->hotelSetting->latitude }},{{ $hotel->hotelSetting->longitude }}&z=16&output=embed">
</iframe>

## 2026-03-14 – Support Ticket Notification Improvements

### Wallet Label Fix (Admin Deposit Details)

File:
core/resources/views/admin/deposit/detail.blade.php

Issue:
Wallet payments were not displaying a payment method on the deposit details page because wallet transactions use `method_code = 0` and have no gateway record.

Fix:
Added condition to display "Wallet" when `method_code == 0`.

### Wallet Payment Label Fix (Admin Deposit List)

File:
core/resources/views/admin/deposit/log.blade.php

Issue:
Wallet payments were not displaying any payment method label because wallet transactions use `method_code = 0` and have no gateway record.

Fix:
Added condition to display "Wallet" when `method_code == 0`.

@if ($deposit->method_code == 0)
    <span class="text--primary">@lang('Wallet')</span>
@endif

### Files Modified

* `core/app/Traits/SupportTicketManager.php`

---

### 1. Added Admin Notification When Vendor Replies to Ticket

**Issue**

When vendors replied to an existing support ticket, admins did not receive a dashboard notification.
Notifications were only triggered when the ticket was first created.

**Fix**

Added `AdminNotification` creation when a vendor/user replies to a ticket.

```php
$adminNotification = new AdminNotification();
$adminNotification->owner_id  = $ticket->owner_id;
$adminNotification->user_id   = $ticket->user_id;
$adminNotification->title     = 'New reply in support ticket #' . $ticket->ticket;
$adminNotification->click_url = urlPath('admin.ticket.view', $ticket->id);
$adminNotification->save();
```

---

### 2. Added Vendor Dashboard Notification When Admin Replies

Added `OwnerNotification` when admin responds to vendor tickets so vendors receive bell notifications.

```php
OwnerNotification::create([
    'owner_id'  => $ticket->owner_id,
    'user_id'   => 0,
    'title'     => 'Admin replied to ticket #' . $ticket->ticket,
    'click_url' => route('owner.ticket.view', $ticket->ticket),
    'is_read'   => 0
]);
```

---

### 3. Fixed MassAssignmentException for AdminNotification

Using:

```php
AdminNotification::create([...])
```

caused:

```
MassAssignmentException: Add [owner_id] to fillable property
```

Resolved by switching to manual model creation instead of mass assignment.

```php
$adminNotification = new AdminNotification();
$adminNotification->owner_id = $ticket->owner_id;
$adminNotification->save();
```

---

### Result

Support ticket system now sends notifications correctly:

* Vendor creates ticket → Admin notified
* Admin replies → Vendor notified
* Vendor replies again → Admin notified
* Admin replies again → Vendor notified

This ensures a complete **two-way notification system for ticket conversations**.


## 2026-03-14 – Subscription Payment & Notification Fixes

### Files Modified

* `core/app/Http/Controllers/Gateway/PaymentController.php`
* `core/app/Http/Controllers/Admin/DepositController.php`

---

### 1. Fixed Carbon addMonth TypeError (PHP 8.3)

**Issue**
Admin approval of subscription payments caused:

```
Carbon\Carbon::rawAddUnit(): Argument #3 ($value) must be of type int|float, string given
```

because `pay_for_month` was stored as a string.

**Fix**

Cast `pay_for_month` to integer and safely handle null `expire_at`.

```php
$months = (int) $deposit->pay_for_month;

$baseDate = $owner->expire_at
    ? Carbon::parse($owner->expire_at)
    : now();

$nextExpireDate = $baseDate->addMonths($months)->subDay();
```

Applied in:

* `PaymentController::userDataUpdate()`
* `PaymentController::billPayByWalletBalance()`

---

### 2. Vendor Dashboard Notification for Subscription Approval

Default script only sent template/email notifications but did not create a record in `owner_notifications`, so the vendor dashboard bell icon did not update.

Added dashboard notification:

```php
$ownerNotification = new OwnerNotification();
$ownerNotification->owner_id  = $owner->id;
$ownerNotification->user_id   = 0;
$ownerNotification->title     = 'Subscription payment approved for ' . $deposit->pay_for_month . ' month(s)';
$ownerNotification->click_url = urlPath('owner.payment.history') . '?search=' . $deposit->trx;
$ownerNotification->save();
```

---

### 3. Vendor Dashboard Notification for Rejected Subscription

Added dashboard notification when admin rejects subscription payment.

```php
$ownerNotification = new OwnerNotification();
$ownerNotification->owner_id  = $owner->id;
$ownerNotification->user_id   = 0;
$ownerNotification->title     = 'Subscription payment rejected';
$ownerNotification->click_url = urlPath('owner.payment.history') . '?search=' . $deposit->trx;
$ownerNotification->save();
```

---

### 4. Fixed Typo in Rejection Logic

Corrected:

```
pay_form_month
```

to:

```
pay_for_month
```

to prevent notification errors.

---

### Result

After fixes:

* Admin can approve or reject subscription payments without errors.
* Vendor dashboard bell notification appears for:

  * subscription approval
  * subscription rejection
* Subscription expiry calculation works correctly on PHP 8.3.


1️⃣ Fix: Booking notification redirect logic

Files:

OwnerController.php

ManageBookingRequestController.php

git add core/app/Http/Controllers/Owner/OwnerController.php
git add core/app/Http/Controllers/Owner/ManageBookingRequestController.php

git commit -m "fix: correct booking notification redirect after request approval"

Commit meaning:

Pending → /vendor/booking/request/detail/{id}

Approved / Cancelled → /vendor/booking/details/{id}

Old notifications handled safely

2️⃣ Fix: Vendor subscription history display

File:

core/resources/views/owner/payment_history.blade.php
git add core/resources/views/owner/payment_history.blade.php

git commit -m "fix: display wallet subscription transactions and details in vendor payment history"

Commit meaning:

Wallet payments appear

Details popup works

Gateway label corrected

3️⃣ Improvement: Dashboard subscription expiry display

File:

core/resources/views/owner/dashboard.blade.php
git add core/resources/views/owner/dashboard.blade.php

git commit -m "improve: show vendor subscription expiry with remaining days in dashboard"

Commit meaning:
Instead of:

1057.4279322587 days

It now shows:

1057 days left
4️⃣ Fix: Gateway payment controller adjustment

File:

core/app/Http/Controllers/Gateway/PaymentController.php
git add core/app/Http/Controllers/Gateway/PaymentController.php

git commit -m "fix: adjust gateway payment controller for vendor subscription handling"

## 2026-03-14 — Notification redirect fix for booking requests

### Problem
When a booking request was approved, the system deleted the booking request record.  
Old notifications still pointed to:

/vendor/booking/request/detail/{id}

This resulted in **404 Page Not Found** after approval.

### Solution

Implemented intelligent redirect logic inside:

core/app/Http/Controllers/Owner/OwnerController.php

Function modified:

notificationRead($id)

Redirect behavior now:

| Scenario | Redirect |
|--------|---------|
| Request still pending | /vendor/booking/request/detail/{id} |
| Request already approved | /vendor/booking/details/{id} |
| Booking cancelled | /vendor/booking/details/{id} |
| Old notification | /vendor/booking/details/{id} |

### Additional Change

Modified booking approval logic in:

core/app/Http/Controllers/Owner/ManageBookingRequestController.php

Instead of deleting booking requests after approval, the system now updates the request status:

Status::BOOKING_REQUEST_APPROVED

This preserves request history and prevents notification links from breaking.

### Result

• No more 404 errors from old notifications  
• Notifications redirect to correct booking pages  
• Booking request history preserved  
• System behavior aligned with author demo

## 2026-03-13 – PHP 8.3 Carbon addDays() Fix (BookingController)

# Custom Changes – KokanStays

## 1. Vendor Notification System Fix

### Purpose

Ensure both **main vendor (owner)** and **staff accounts (manager, etc.)** receive dashboard notifications for important events like withdrawals and wallet transactions.

### Files Modified

* `core/app/Http/Controllers/Admin/WithdrawalController.php`
* `core/app/Http/Controllers/Admin/ManageOwnersController.php`
* `core/app/Http/Helpers/helpers.php`
* `core/resources/views/owner/partials/topnav.blade.php`
* `core/app/Models/OwnerNotification.php`
* `core/app/Providers/AppServiceProvider.php`

---

## 2. Helper Function Added

### File

`core/app/Http/Helpers/helpers.php`

### Function

`ownerNotify()`

### Description

Creates notifications for the **main owner and all staff accounts (parent-child owner relationship)**.

```php
function ownerNotify($ownerId, $title, $url = null)
{
    try {

        // Main owner notification
        \App\Models\OwnerNotification::create([
            'owner_id'  => $ownerId,
            'user_id'   => 0,
            'title'     => $title,
            'click_url' => $url,
            'is_read'   => 0
        ]);

        // Notify staff accounts
        $staff = \App\Models\Owner::where('parent_id', $ownerId)->get();

        foreach ($staff as $member) {
            \App\Models\OwnerNotification::create([
                'owner_id'  => $member->id,
                'user_id'   => 0,
                'title'     => $title,
                'click_url' => $url,
                'is_read'   => 0
            ]);
        }

    } catch (\Exception $e) {
        \Log::error('Owner notification error: ' . $e->getMessage());
    }
}
```

---

## 3. Withdrawal Notification Added

### File

`core/app/Http/Controllers/Admin/WithdrawalController.php`

### Added After Approve

```php
ownerNotify(
    $withdraw->owner_id,
    'Your withdrawal request has been approved',
    route('owner.withdraw.history')
);
```

### Added After Reject

```php
ownerNotify(
    $withdraw->owner_id,
    'Your withdrawal request has been rejected',
    route('owner.withdraw.history')
);
```

---

## 4. Wallet Balance Notification

### File

`core/app/Http/Controllers/Admin/ManageOwnersController.php`

Added notification when admin adds or subtracts balance.

```php
ownerNotify(
    $owner->id,
    'Admin added ' . gs('cur_sym') . $amount . ' to your wallet',
    route('owner.report.transaction')
);
```

```php
ownerNotify(
    $owner->id,
    'Admin deducted ' . gs('cur_sym') . $amount . ' from your wallet',
    route('owner.report.transaction')
);
```

---

## 5. Notification Dropdown Improvements

### File

`core/resources/views/owner/partials/topnav.blade.php`

Changes:

* Added safe image loading
* Added empty notification message
* Added support for `click_url`

```blade
href="{{ $notification->click_url ?? route('owner.notification.read', $notification->id) }}"
```

---

## Result

Notifications now work for:

* Withdrawal approved
* Withdrawal rejected
* Wallet balance added
* Wallet balance deducted
* Vendor + staff accounts

All notifications appear in the **vendor dashboard bell icon**.

====
| File                     | Purpose                                |
| ------------------------ | -------------------------------------- |
| WithdrawalController.php | Withdrawal approve/reject notification |
| helpers.php              | `ownerNotify()` helper                 |
| OwnerNotification.php    | Notification model                     |
| AppServiceProvider.php   | Load notifications in dashboard        |
| topnav.blade.php         | Bell icon display                      |
git add core/app/Http/Controllers/Admin/WithdrawalController.php
git add core/app/Http/Helpers/helpers.php
git add core/app/Models/OwnerNotification.php
git add core/app/Providers/AppServiceProvider.php
git add core/resources/views/owner/partials/topnav.blade.php

**File Modified**
core/app/Http/Controllers/Owner/BookingController.php

**Problem**
Laravel with PHP 8.3 throws a TypeError because Carbon date unit methods
(e.g. `addDays()`) require an integer or float.  
However `hotelSetting()` returns a string from the database.

Error example:
Carbon\Carbon::rawAddUnit(): Argument #3 ($value) must be of type int|float, string given

**Affected Methods**
- upcomingCheckIn()
- upcomingCheckout()

**Fix**
Cast the value returned by `hotelSetting()` to integer before passing it to `addDays()`.

**Before**

```php
now()->addDays(hotelSetting('upcoming_checkin_days'))
now()->addDays(hotelSetting('upcoming_checkout_days'))
------======

2026-03-13

core/app/Http/Controllers/Owner/OwnerController.php

Fix Carbon addDays() TypeError in OwnerController dashboard.
Cast hotelSetting() values to integer for PHP 8.3 compatibility.

## 2026-03-13 – Fix Carbon addDays() TypeError (PHP 8.3 Compatibility)
------======

**File Modified**
app/Http/Middleware/OwnerValidity.php

**Problem**
Laravel 11 with PHP 8.3 throws a TypeError because `Carbon::addDays()` requires an integer or float, but `gs('payment_before')` returns a string from the database.

Error:
Carbon\Carbon::rawAddUnit(): Argument #3 ($value) must be of type int|float, string given

**Fix**
Cast the value returned by `gs('payment_before')` to integer.

**Before**
```php
$paymentDeadLine = Carbon::parse($owner->expire_at)->addDays(gs('payment_before'));


## 2026-03-09

### Fix: Advertisement Banners with "Redirect = None" Not Showing in Mobile App

Description:
Resolved an issue where advertisements configured with **Redirect To = None** were not appearing in the Flutter mobile application.

Root Cause:
The API query in `UserController::home()` filtered advertisements using the condition:

`orWhereNotNull('url')`

This caused banners without a redirect URL to be excluded from the API response.

Solution:
Updated the advertisement query to return all active advertisements regardless of redirect configuration.

Previous Logic:
Advertisements were returned only if:

* An owner was assigned, OR
* A redirect URL existed

Corrected Logic:
Advertisements are now returned based on:

* Active status
* Valid end date

Code Updated:

Before:

```php
$ads = Advertisement::whereDate('end_date', '>', now())
    ->where(function ($query) {
        $query->whereHas('owner', function ($ownerQuery) {
            $ownerQuery->active()->notExpired();
        })
        ->orWhereNotNull('url');
    })
    ->inRandomOrder()
    ->limit(5)
    ->get();
```

After:

```php
$ads = Advertisement::whereDate('end_date', '>', now())
    ->where('status', 1)
    ->inRandomOrder()
    ->limit(5)
    ->get();
```

Impact:

* Advertisements configured with **Redirect = None** now display correctly in the Flutter app.
* URL redirects and hotel redirects continue to work as expected.

Files Modified:

* core/app/Http/Controllers/Api/UserController.php

Deployment Step:
Cleared Laravel cache using `/clear` route (`optimize:clear`).

### Feature Added: Language Selection Popup (First Visit)

Description:
Implemented a language selection modal that appears when a user visits the website for the first time. Users can choose between Marathi, Hindi, and English. The selected language is saved in a browser cookie (`site_language`) for 1 year, and Laravel loads the locale automatically on future visits.

UI Features:

* Centered modal popup with dark overlay
* Mobile responsive design
* Language buttons with flag icons
* Priority language order for target audience:

  1. Marathi
  2. English
  3. Hindi

Technical Implementation:

* Cookie-based language detection (`site_language`)
* Integration with existing `/change/{lang}` route
* Locale loaded automatically via `AppServiceProvider`
* Custom modal CSS added to `custom.css`
* Blade layout updated to show popup only on first visit

Files Modified:

* core/resources/views/templates/basic/layouts/app.blade.php
* core/app/Providers/AppServiceProvider.php

Files Updated:

* assets/templates/basic/css/custom.css (Language modal styles)

Additional Notes:

* Popup displays only if `site_language` cookie is not present
* Selected language persists for 1 year
* Laravel cache cleared after deployment


### Project

Kokan Stays (Laravel)

### Issue

Admin → Owner KYC Form Builder
URL: `/admin/owner-form/setting`

When adding a new form field, the **previous field disappeared from the UI**.

### Root Cause

In `form_generator.js`, the script collected **all `options[]` inputs from the entire page**, not only from the modal form.
This caused form data conflicts and overwrote previously generated fields.

### File Modified

```
/assets/global/js/form_generator.js
```

### Old Code

```javascript
var options = $("[name='options[]']").map(function(){return $(this).val();}).get();
```

### Fixed Code

```javascript
var options = form.find("[name='options[]']").map(function(){
    return $(this).val();
}).get();
```

### Improvement Added

Prevent undefined options errors.

```javascript
options: options || []
```

### Result

* Multiple fields can now be added correctly
* Previous fields no longer disappear
* Owner KYC form builder works properly

### Tested

Admin Panel → Owner Form → Setting

Added fields:

* Aadhar Card (File)
* PAN Card (File)
* Owner Photo (File)
* Property Address (Text)

All fields saved and displayed correctly.

### Notes

If the script/theme is updated, reapply this fix in:

```
assets/global/js/form_generator.js
```


### Date: 2026-03-08
Module: Maintenance Mode (Laravel Backend + Flutter App)

Issue
When Maintenance Mode was enabled in the backend, the Flutter mobile app crashed with:

NoSuchMethodError: The method 'forEach' was called on null.

Root Cause
The backend maintenance middleware returned a JSON response without the expected `data.general_setting` structure.
Flutter models expected `data.general_setting`, so parsing failed and caused a runtime error.

Affected Files
Backend:
core/app/Http/Middleware/MaintenanceMode.php

Flutter:
lib/data/model/general_setting/general_setting_response_model.dart
lib/data/controller/splash/splash_controller.dart

Fix Implemented

1. Updated Laravel MaintenanceMode middleware response structure to include:
   data.general_setting = null

2. Allowed critical APIs during maintenance so Flutter can load configuration:

   * /api/language/*
   * /api/general-setting
   * /api/sections/maintenance

3. Blocked login, register, and other APIs during maintenance.

Final Middleware Behavior

Maintenance ON:

* Language API → Allowed
* General Setting API → Allowed
* Login/Register → Blocked
* Other APIs → Blocked
* Website → Redirects to maintenance page

JSON response during maintenance:

{
"remark": "maintenance_mode",
"status": "error",
"message": {
"error": ["Our application is currently in maintenance mode"]
},
"data": {
"general_setting": null
}
}

Result

✔ Flutter app no longer crashes
✔ Splash screen loads safely
✔ Login is prevented during maintenance
✔ Maintenance message displayed correctly
✔ API response structure consistent

Project: KokanStays Mobile + Laravel Backend
