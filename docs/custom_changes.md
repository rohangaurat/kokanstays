KOKANSTAYS – CUSTOM LOG


## 2026-03-13 – Fix Carbon addDays() TypeError (PHP 8.3 Compatibility)

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
