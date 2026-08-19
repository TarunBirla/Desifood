<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTestimonialController extends Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('client_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
        }

        $testimonials = $query->paginate(12)->withQueryString();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_title' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'client_avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('client_avatar')) {
            $path = $request->file('client_avatar')->store('testimonials', 'public');
            $validated['client_avatar'] = Storage::url($path);
        }

        $validated['status'] = $request->has('status') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        Testimonial::create($validated);

        return back()->with('success', 'Testimonial created successfully!');
    }

    public function update(Request $request, $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_title' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'client_avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('client_avatar')) {
            if ($testimonial->client_avatar && str_contains($testimonial->client_avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $testimonial->client_avatar);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('client_avatar')->store('testimonials', 'public');
            $validated['client_avatar'] = Storage::url($path);
        }

        $validated['status'] = $request->has('status') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;

        $testimonial->update($validated);

        return back()->with('success', 'Testimonial updated successfully!');
    }

    public function destroy($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        if ($testimonial->client_avatar && str_contains($testimonial->client_avatar, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $testimonial->client_avatar);
            Storage::disk('public')->delete($oldPath);
        }
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted successfully!');
    }
}
