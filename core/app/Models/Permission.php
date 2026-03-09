<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{

    public $timestamps = false;

    public $excludedActions = ['LoginController', 'ForgotPasswordController', 'ResetPasswordController', 'PermissionController', 'OwnerController@profile', 'OwnerController@profileUpdate', 'OwnerController@password', 'OwnerController@passwordUpdate', 'AuthorizationController', 'OwnerController@show2faForm', 'OwnerController@create2fa', 'OwnerController@disable2fa', 'OwnerController@bookingReport', 'OwnerController@paymentReport', 'BookRoomController@updateRoomSessionData'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function () {
            Cache::forget('AllPermissions');
        });
    }
}
