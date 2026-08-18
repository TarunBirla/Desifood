<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class AdminPageController extends Controller
{
    public function index()
    {
        $terms = CmsPage::firstOrCreate(
            ['slug' => 'terms-and-conditions'],
            ['title' => 'Terms & Conditions', 'content' => 'Default Terms & Conditions content.']
        );

        $privacy = CmsPage::firstOrCreate(
            ['slug' => 'privacy-policy'],
            ['title' => 'Privacy Policy', 'content' => 'Default Privacy Policy content.']
        );

        return view('admin.pages.index', compact('terms', 'privacy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'terms_content' => 'required|string',
            'privacy_content' => 'required|string',
        ]);

        CmsPage::where('slug', 'terms-and-conditions')->update([
            'title' => 'Terms & Conditions',
            'content' => $request->terms_content,
        ]);

        CmsPage::where('slug', 'privacy-policy')->update([
            'title' => 'Privacy Policy',
            'content' => $request->privacy_content,
        ]);

        return back()->with('success', 'Terms & Conditions and Privacy Policy updated successfully!');
    }
}
