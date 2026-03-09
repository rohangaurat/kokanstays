<?php

namespace App\Constants;

class FileInfo {
    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
     */

    public function fileInfo() {
        $data['verify'] = [
            'path' => 'assets/verify',
        ];
        $data['default'] = [
            'path' => 'assets/images/default.png',
        ];
        $data['ticket'] = [
            'path' => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path' => 'assets/images/logo_icon',
        ];
        $data['favicon'] = [
            'size' => '128x128',
        ];
        $data['extensions'] = [
            'path' => 'assets/images/extensions',
            'size' => '36x36',
        ];
        $data['seo'] = [
            'path' => 'assets/images/seo',
            'size' => '1180x600',
        ];
        $data['userProfile'] = [
            'path' => 'assets/images/user/profile',
            'size' => '350x300',
        ];
        $data['adminProfile'] = [
            'path' => 'assets/admin/images/profile',
            'size' => '400x400',
        ];
        $data['roomTypeImage'] = [
            'path' => 'assets/images/roomType',
        ];
        $data['push'] = [
            'path' => 'assets/images/push_notification',
        ];
        $data['appPurchase'] = [
            'path' => 'assets/in_app_purchase_config',
        ];
        $data['maintenance'] = [
            'path' => 'assets/images/maintenance',
            'size' => '660x325',
        ];
        $data['language'] = [
            'path' => 'assets/images/language',
            'size' => '50x50',
        ];
        $data['gateway'] = [
            'path' => 'assets/images/gateway',
            'size' => '',
        ];
        $data['withdrawMethod'] = [
            'path' => 'assets/images/withdraw_method',
            'size' => '',
        ];
        $data['pushConfig'] = [
            'path' => 'assets/admin',
        ];
        $data['ownerProfile'] = [
            'path' => 'assets/owner/images/profile',
            'size' => '400x400',
        ];
        $data['hotelImage'] = [
            'path' => 'assets/images/hotel/image',
            'size' => '285x170',
        ];
        $data['hotelCoverImage'] = [
            'path' => 'assets/images/hotel/image',
            'size' => '955x250',
        ];
        $data['coverPhoto'] = [
            'path' => 'assets/images/hotel/cover',
        ];
        $data['ads'] = [
            'path' => 'assets/images/ads',
            'size' => '640x230',
        ];
        $data['city'] = [
            'path' => 'assets/images/city',
            'size' => '300x400',
        ];
        $data['facility'] = [
            'path' => 'assets/images/facilities',
            'size' => '40x40',
        ];
        $data['amenity'] = [
            'path' => 'assets/images/amenities',
            'size' => '40x40',
        ];
        return $data;
    }
}
