<?php

namespace App\Models;

use App\Traits\CurrentOwner;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class CoverPhoto extends Model
{
    use CurrentOwner;

    protected $appends = ['cover_photo_url'];

    public function coverPhotoUrl(): Attribute
    {
        return new Attribute(
            get: function () {
                return getImage(getFilePath('coverPhoto') . '/' . $this->cover_photo);
            }
        );
    }
}
