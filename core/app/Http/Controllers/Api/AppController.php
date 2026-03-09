<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Frontend;
use App\Models\City;
use App\Models\Country;
use App\Models\Language;
use Illuminate\Support\Facades\Cookie;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function generalSetting()
    {
        $data = [
            'general_setting' => gs(),
            'social_login_redirect' => route('user.social.login.callback', ''),
            'social_login_status' => [
                'google_login' => gs('socialite_credentials')->google->status,
                'facebook_login' => gs('socialite_credentials')->facebook->status,
                'linkedin_login' => gs('socialite_credentials')->linkedin->status,
            ]
        ];

        $notify[] = 'General setting data';
        return responseSuccess('general_setting', $notify, $data);
    }

    public function getCountries()
    {
        $countries = Country::active()->orderBy('name')->get();
        $notify[] = 'All countries';

        return responseSuccess('country_data', $notify, [
            'countries' => $countries,
        ]);
    }

    public function getLanguage($code = null)
    {
        $languages = Language::get();
        $languageCodes = $languages->pluck('code')->toArray();

        if (($code && !in_array($code, $languageCodes))) {
            $notify[] = 'Invalid code given';
            return responseError('validation_error', $notify);
        }

        if (!$code) {
            $code = Language::where('is_default', Status::YES)->first()?->code ?? 'en';
        }

        $jsonFile = file_get_contents(resource_path('lang/' . $code . '.json'));

        $notify[] = 'Language';

        return responseSuccess('language', $notify, [
            'languages' => $languages,
            'file' => json_decode($jsonFile) ?? [],
            'code' => $code,
            'image_path' => getFilePath('language')
        ]);
    }

    public function policies()
    {
        $policies = getContent('policy_pages.element', orderById: true);
        $notify[] = 'All policies';

        return responseSuccess('policy_data', $notify, [
            'policies' => $policies,
        ]);
    }

    public function policyContent($slug)
    {
        $policy = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->first();
        if (!$policy) {
            $notify[] = 'Policy not found';
            return responseError('policy_not_found', $notify);
        }
        $seoContents = $policy->seo_content;
        $seoImage = @$seoContents->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        $notify[] = 'Policy content';
        return responseSuccess('policy_content', $notify, [
            'policy' => $policy,
            'seo_content' => $seoContents,
            'seo_image' => $seoImage
        ]);
    }


    public function faq()
    {
        $faq = getContent('faq.element', orderById: true);
        $notify[] = 'FAQ';
        return responseSuccess('faq', $notify, ['faq' => $faq]);
    }
    public function cookie()
    {
        $cookie = Frontend::where('data_keys', 'cookie.data')->first();
        $notify[] = 'Cookie policy';
        return responseSuccess('cookie_data', $notify, [
            'cookie' => $cookie
        ]);
    }
    public function cookieAccept()
    {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
        $notify[] = 'Cookie accepted';
        return responseSuccess('cookie_accepted', $notify);
    }


    public function getPopularHotels()
    {
        $owners = Owner::active()->notExpired()->whereHas('hotelSetting')
            ->withCount(['bookings as total_bookings' => function ($query) {
                $query->where('status', Status::BOOKING_ACTIVE)->whereDate('created_at', '>=', Carbon::now()->subDays(gs('popularity_count_from')));
            }])->having('total_bookings', '>', 0)->orderBy('total_bookings')->with('hotelSetting')->withMin(['roomTypes as minimum_fare' => function ($query) {
                $query->active();
            }], 'fare')->apiQuery();

        $notify[] = 'Popular hotels';
        return responseSuccess('popular_hotels', $notify, [
            'owners' => $owners
        ]);
    }

    public function popularCities()
    {
        $popularCities = City::active()->popular()
            ->withCount(['hotelSettings as total_hotel' => function ($hotelSetting) {
                $hotelSetting->whereHas('owner', function ($owner) {
                    $owner->where('status', Status::USER_ACTIVE)->whereDate('owners.expire_at', '>=', now())->whereHas('roomTypes', function ($roomTypes) {
                        $roomTypes->where('status', Status::ROOM_TYPE_ACTIVE);
                    });
                });
            }])->having('total_hotel', '>', 0)->with('country:id,name')
            ->orderBy('total_hotel', 'DESC')->apiQuery();

        $notify[] = 'Popular destination';
        return responseSuccess('popular_destinations', $notify, [
            'popular_cities' => $popularCities
        ]);
    }

    public function searchCities(Request $request)
    {
        $search = $request->keywords;
        $cities = City::active()->select('id', 'name', 'country_id', 'image')->where(function ($city) use ($search) {
            $city->where('name', 'like', "%$search%")
                ->orWhereHas('country', function ($country) use ($search) {
                    $country->where('name', 'like', "%$search%");
                });
        })->withCount(['hotelSettings as total_hotel' => function ($hotelSetting) {
            $hotelSetting->whereHas('owner', function ($owner) {
                $owner->where('status', Status::USER_ACTIVE)->whereDate('owners.expire_at', '>=', now())->whereHas('roomTypes', function ($roomTypes) {
                    $roomTypes->where('status', Status::ROOM_TYPE_ACTIVE);
                });
            });
        }])->with('country:id,name')->orderBy('total_hotel', 'DESC')->apiQuery();

        $notify[] = 'Search destinations';
        return responseSuccess('search_destinations', $notify, [
            'cities' => $cities
        ]);
    }

    public function featuredHotels()
    {
        $featuredHotels = Owner::owner()->active()->notExpired()->featured()->whereHas('hotelSetting')->with('hotelSetting')->apiQuery();
        $notify[] = 'Featured hotels';

        return responseSuccess('featured_hotels', $notify, [
            'featured_hotels' => $featuredHotels
        ]);
    }

    public function allSections($key = null)
    {
        $items = Frontend::where('data_keys', 'like', '%.content')
            ->orWhere('data_keys', 'like', '%.element')
            ->orWhere('data_keys', 'like', '%.data')
            ->get();

        $groupedItems = $items->groupBy(function ($item) {
            return explode('.', $item->data_keys)[0]; // Group by section key
        });

        $data = $groupedItems->map(function ($group, $sectionKey) {
            $content   = $group->firstWhere('data_keys', "{$sectionKey}.content");
            $elements  = $group->filter(fn($item) => str_ends_with($item->data_keys, '.element'));
            $dataItems = $group->filter(fn($item) => str_ends_with($item->data_keys, '.data'));

            return [
                'key'      => $sectionKey,
                'content'  => $content->data_values ?? null,
                'elements' => $elements->pluck('data_values')->toArray(),
                'data'     => $dataItems->pluck('data_values')->first(),
            ];
        })->values();

        return $key ? $data->firstWhere('key', $key) : $data;
    }
}
