<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage', 'category', 'brand', 'variants'])->where('is_active', true);

        // Filter by Category / Subcategory
        if ($request->has('category') && $request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $categoryIds = array_merge([$category->id], $category->children()->pluck('id')->toArray());
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filter by Brand
        if ($request->has('brand') && $request->brand) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        // Filter by Price Range
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by Availability / Stock
        if ($request->has('in_stock') && $request->in_stock == '1') {
            $query->where('stock', '>', 0);
        }

        // Search Term
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'price_low_high':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('price', 'desc');
                break;
            case 'best_rated':
                $query->orderBy('rating_avg', 'desc');
                break;
            case 'popular':
                $query->orderBy('reviews_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $brands = Brand::where('status', true)->get();
        $attributes = Attribute::with('values')->get();

        return view('catalog.index', compact('products', 'categories', 'brands', 'attributes'));
    }

    public function allCategories()
    {
        $categories = Category::whereNull('parent_id')
            ->where('status', true)
            ->with(['children', 'products'])
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(string $slug)
    {
        $product = Product::with(['primaryImage', 'images', 'category', 'brand', 'variants', 'reviews.user'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedProducts = Product::with(['primaryImage', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('catalog.show', compact('product', 'relatedProducts'));
    }

    public function liveSearch(Request $request)
    {
        $term = $request->get('q', '');
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $products = Product::with(['primaryImage', 'images', 'category', 'brand'])
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            })
            ->take(8)
            ->get()
            ->map(function ($p) {
                $imageObj = $p->primaryImage ?? $p->images->first();
                $imgPath = 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800';

                if ($imageObj && $imageObj->image_path) {
                    $rawPath = $imageObj->image_path;
                    if (str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://')) {
                        $imgPath = $rawPath;
                    } elseif (str_starts_with($rawPath, '/uploads/') || str_starts_with($rawPath, 'uploads/')) {
                        $imgPath = asset(ltrim($rawPath, '/'));
                    } elseif (str_starts_with($rawPath, '/storage/') || str_starts_with($rawPath, 'storage/')) {
                        $imgPath = asset(ltrim($rawPath, '/'));
                    } elseif (str_starts_with($rawPath, '/images/') || str_starts_with($rawPath, 'images/')) {
                        $imgPath = asset(ltrim($rawPath, '/'));
                    } else {
                        $imgPath = asset('storage/' . ltrim($rawPath, '/'));
                    }
                }

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'brand' => $p->brand ? $p->brand->name : 'Desi Foods',
                    'price' => '£' . number_format($p->effective_price, 2),
                    'raw_price' => (float)$p->effective_price,
                    'stock' => $p->stock,
                    'image' => $imgPath,
                    'category' => $p->category ? $p->category->name : '',
                    'url' => route('products.show', $p->slug)
                ];
            });

        return response()->json($products);
    }
}
