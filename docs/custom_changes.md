KOKANSTAYS – CUSTOM LOG

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
