<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Language extends Model
{
    public function isDefaultBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->is_default == Status::YES) {
                $html = '<span class="badge badge--success">' . trans('Default') . '</span>';
            } elseif ($this->is_default == Status::NO) {
                $html = '<span class="badge badge--warning">' . trans('Selectable') . '</span>';
            }
            return $html;
        });
    }
}
