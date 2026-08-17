<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'primaryImage']);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = Attribute::with('values')->get();

        return view('admin.products.create', compact('categories', 'brands', 'attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'sku' => $request->sku,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'is_trending' => $request->has('is_trending'),
            'is_new_arrival' => $request->has('is_new_arrival'),
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/products'))) {
                mkdir(public_path('uploads/products'), 0777, true);
            }
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = '/uploads/products/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        if ($imagePath) {
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'is_primary' => true,
            ]);
        }

        Inventory::create(['product_id' => $product->id, 'current_stock' => $product->stock]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit($id)
    {
        $product = Product::with(['images', 'variants', 'primaryImage'])->findOrFail($id);
        $categories = Category::all();
        $brands = Brand::all();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
            'is_trending' => $request->has('is_trending'),
            'is_new_arrival' => $request->has('is_new_arrival'),
        ]);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            if (!file_exists(public_path('uploads/products'))) {
                mkdir(public_path('uploads/products'), 0777, true);
            }
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = '/uploads/products/' . $filename;
        } elseif ($request->filled('image_url')) {
            $imagePath = $request->image_url;
        }

        if ($imagePath) {
            $primaryImg = ProductImage::where('product_id', $product->id)->where('is_primary', true)->first();
            if ($primaryImg) {
                $primaryImg->update(['image_path' => $imagePath]);
            } else {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => true,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return back()->with('success', 'Product deleted.');
    }
}
