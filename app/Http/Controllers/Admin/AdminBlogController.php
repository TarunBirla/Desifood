<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('category')->latest()->paginate(15)->withQueryString();
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|url',
        ]);

        Blog::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(4),
            'category_id' => $request->category_id,
            'featured_image' => $request->featured_image ?: 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=800',
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'is_published' => $request->has('is_published'),
            'published_at' => now(),
        ]);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog article created successfully!');
    }

    public function destroy($id)
    {
        Blog::destroy($id);
        return back()->with('success', 'Blog article deleted.');
    }
}
