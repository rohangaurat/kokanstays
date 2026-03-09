<?php

namespace App\Http\Controllers;

use App\Models\Page;

class VendorController extends Controller
{
    public function home()
    {
        $pageTitle = 'Vendor';
        $sections = Page::where('tempname', activeTemplate())->where('slug', 'vendor')->first();
        $seoContents = $sections->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('vendor.home', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        $user = auth()->user();
        $sections = Page::where('tempname', activeTemplate())->where('slug', 'contact')->first();
        $seoContents = @$sections->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('vendor.contact', compact('pageTitle', 'user', 'sections', 'seoContents', 'seoImage'));
    }
}
