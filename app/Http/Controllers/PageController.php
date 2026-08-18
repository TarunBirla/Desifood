<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\Faq;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function faqs()
    {
        $faqs = Faq::where('is_active', true)->orderBy('order', 'asc')->get();
        return view('pages.faqs', compact('faqs'));
    }

    public function terms()
    {
        $page = CmsPage::where('slug', 'terms-and-conditions')->firstOrFail();
        return view('pages.terms', compact('page'));
    }

    public function privacy()
    {
        $page = CmsPage::where('slug', 'privacy-policy')->firstOrFail();
        return view('pages.privacy', compact('page'));
    }
}
