<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::with('parent')->withCount('products');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', (bool)$request->status);
        }

        $categories = $query->orderBy('sort_order', 'asc')
                            ->orderBy('id', 'desc')
                            ->paginate(15)
                            ->withQueryString();

        $allCategories = Category::orderBy('name', 'asc')->get();

        return view('admin.categories.index', compact('categories', 'allCategories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255|unique:categories,slug',
            'parent_id'       => 'nullable|exists:categories,id',
            'image'           => 'nullable|string',
            'image_file'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'banner'          => 'nullable|string',
            'banner_file'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'description'     => 'nullable|string',
            'sort_order'      => 'nullable|integer',
            'status'          => 'nullable',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $imagePath = $request->image;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_img_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0777, true);
            }
            $file->move(public_path('uploads/categories'), $filename);
            $imagePath = '/uploads/categories/' . $filename;
        }

        $bannerPath = $request->banner;
        if ($request->hasFile('banner_file')) {
            $file = $request->file('banner_file');
            $filename = time() . '_banner_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0777, true);
            }
            $file->move(public_path('uploads/categories'), $filename);
            $bannerPath = '/uploads/categories/' . $filename;
        }

        Category::create([
            'name'            => $request->name,
            'slug'            => $slug,
            'parent_id'       => $request->parent_id ?: null,
            'image'           => $imagePath,
            'banner'          => $bannerPath,
            'description'     => $request->description,
            'sort_order'      => $request->sort_order ?? 0,
            'status'          => $request->has('status') ? (bool)$request->status : true,
            'seo_title'       => $request->seo_title,
            'seo_description' => $request->seo_description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'parent_id'       => 'nullable|exists:categories,id|different:id',
            'image'           => 'nullable|string',
            'image_file'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'banner'          => 'nullable|string',
            'banner_file'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'description'     => 'nullable|string',
            'sort_order'      => 'nullable|integer',
            'status'          => 'nullable',
            'seo_title'       => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name);
        $originalSlug = $slug;
        $count = 1;
        while (Category::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $imagePath = $category->image;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_img_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0777, true);
            }
            $file->move(public_path('uploads/categories'), $filename);
            $imagePath = '/uploads/categories/' . $filename;
        } elseif ($request->filled('image')) {
            $imagePath = $request->image;
        }

        $bannerPath = $category->banner;
        if ($request->hasFile('banner_file')) {
            $file = $request->file('banner_file');
            $filename = time() . '_banner_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0777, true);
            }
            $file->move(public_path('uploads/categories'), $filename);
            $bannerPath = '/uploads/categories/' . $filename;
        } elseif ($request->filled('banner')) {
            $bannerPath = $request->banner;
        }

        $category->update([
            'name'            => $request->name,
            'slug'            => $slug,
            'parent_id'       => $request->parent_id ?: null,
            'image'           => $imagePath,
            'banner'          => $bannerPath,
            'description'     => $request->description,
            'sort_order'      => $request->sort_order ?? 0,
            'status'          => $request->has('status') ? (bool)$request->status : false,
            'seo_title'       => $request->seo_title,
            'seo_description' => $request->seo_description,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has associated products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Cannot delete category '{$category->name}' because it has {$category->products()->count()} associated products. Please reassign or delete the products first.");
        }

        // Unassign parent_id from child categories
        Category::where('parent_id', $category->id)->update(['parent_id' => null]);

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
