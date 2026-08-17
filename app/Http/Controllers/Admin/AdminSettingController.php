<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', 'slider_files');
        
        if (isset($data['story_slider_images'])) {
            if (is_array($data['story_slider_images'])) {
                $currentImages = array_values(array_filter($data['story_slider_images']));
            } elseif (is_string($data['story_slider_images'])) {
                $decoded = json_decode($data['story_slider_images'], true);
                if (is_array($decoded)) {
                    $currentImages = array_values(array_filter($decoded));
                } else {
                    $lines = array_filter(array_map('trim', explode("\n", $data['story_slider_images'])));
                    $currentImages = array_values($lines);
                }
            }
        } else {
            $currentImages = [];
        }

        // Handle uploaded slider files from device / phone gallery
        if ($request->hasFile('slider_files')) {
            $uploadedUrls = [];
            foreach ($request->file('slider_files') as $file) {
                $filename = time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                if (!file_exists(public_path('uploads/sliders'))) {
                    mkdir(public_path('uploads/sliders'), 0777, true);
                }
                $file->move(public_path('uploads/sliders'), $filename);
                $uploadedUrls[] = '/uploads/sliders/' . $filename;
            }
            $currentImages = array_merge($currentImages, $uploadedUrls);
        }

        $data['story_slider_images'] = json_encode(array_values($currentImages));

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Store settings and Homepage Story Slider updated successfully.');
    }
}
