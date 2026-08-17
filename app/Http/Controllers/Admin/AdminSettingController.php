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
        
        if (isset($data['story_slider_images'])) {
            if (is_array($data['story_slider_images'])) {
                $data['story_slider_images'] = json_encode(array_values(array_filter($data['story_slider_images'])));
            } elseif (is_string($data['story_slider_images'])) {
                $decoded = json_decode($data['story_slider_images'], true);
                if (is_array($decoded)) {
                    $data['story_slider_images'] = json_encode(array_values(array_filter($decoded)));
                } else {
                    $lines = array_filter(array_map('trim', explode("\n", $data['story_slider_images'])));
                    $data['story_slider_images'] = json_encode(array_values($lines));
                }
            }
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Store settings and Homepage Story Slider updated successfully.');
    }
}
