<?php

namespace App\Models;

use App\Models\Permission;
use App\Traits\CurrentOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    use CurrentOwner;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public static function hasPermission($code = null)
    {
        $owner = authOwner();
        if ($owner->parent_id == 0) {
            return true;
        }

        $roleName        = $owner->role->name;
        $permissionCache = self::formattedPermissionName($roleName, $owner->parent_id);
        $permissions     = Cache::get($permissionCache);

        if (!$permissions) {
            $permissions = $owner->role->permissions->pluck('code')->toArray();
            Cache::put($permissionCache, $permissions);
        }

        if (is_array($code)) {

            $permissionsInString = implode(', ', $permissions);
            $codesInString       = implode(', ', $code);

            if (str_contains($codesInString, '*')) {
                foreach ($code as $route) {
                    $route = str_replace('*', '', $route);
                    if (str_contains($permissionsInString, $route)) {
                        return true;
                    }
                }
            }

            if (empty(array_intersect($code, $permissions))) {
                return false;
            }
            return true;
        }


        $allPermissions = Cache::get('AllPermissions');
        if (!$allPermissions) {
            $allPermissions = Permission::select('code')->get()->pluck('code')->toArray();
            Cache::put('AllPermissions', $allPermissions);
        }

        $routeName = $code ?? request()->route()->getName();
        if (in_array($routeName, $allPermissions) && !in_array($routeName, $permissions)) {
            return false;
        }

        return true;
    }

    protected static function boot()
    {
        parent::boot();
        $owner = authOwner();
        if (!app()->runningInConsole() || !app()->runningUnitTests()) {
            $roles = static::get()->map(function ($role) use ($owner) {

                $ownerId = $owner->parent_id > 0 ? $owner->parent_id : $owner->id;
                return self::formattedPermissionName($role->name, $ownerId);
            })->toArray();

            static::saved(function () use ($roles) {
                foreach ($roles as $value) {
                    \Cache::forget($value);
                }
            });
        }
    }

    private static function formattedPermissionName($role, $uid)
    {
        $role = str_replace(" ", '_', strtolower($role));
        return $role . '_permission_' . $uid;
    }
}
