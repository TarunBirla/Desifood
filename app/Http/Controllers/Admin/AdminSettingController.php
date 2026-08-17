<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token');
        
        if (isset($data['story_slider_images']) && !empty($data['story_slider_images'])) {
            $lines = array_filter(array_map('trim', explode("\n", $data['story_slider_images'])));
            if (!empty($lines)) {
                $data['story_slider_images'] = json_encode(array_values($lines));
            }
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Store settings updated successfully.');
    }
}
