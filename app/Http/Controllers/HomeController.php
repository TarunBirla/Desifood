<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Top 8 categories for Homepage
        $categories = Category::whereNull('parent_id')
            ->where('status', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        // Top 8 featured products for Homepage
        $featuredProducts = Product::with(['primaryImage', 'category', 'brand'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        if ($featuredProducts->count() < 8) {
            $featuredProducts = Product::with(['primaryImage', 'category', 'brand'])
                ->where('is_active', true)
                ->take(8)
                ->get();
        }

        $newArrivals = Product::with(['primaryImage', 'category', 'brand'])
            ->where('is_active', true)
            ->where('is_new_arrival', true)
            ->take(8)
            ->get();

        $trendingProducts = Product::with(['primaryImage', 'category', 'brand'])
            ->where('is_active', true)
            ->where('is_trending', true)
            ->take(8)
            ->get();

        $brands = Brand::where('status', true)->take(12)->get();

        // Story Auto-Slider Images (configurable or authentic Indian food defaults)
        $storyImagesSetting = Setting::get('story_slider_images');
        if ($storyImagesSetting) {
            $storyImages = json_decode($storyImagesSetting, true);
        } else {
            $storyImages = [
                'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=1200&q=80',
                'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=1200&q=80',
                'https://images.unsplash.com/photo-1542838132-92c53300491e?w=1200&q=80',
                'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=1200&q=80',
                'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=1200&q=80'
            ];
        }

        return view('home', compact('categories', 'featuredProducts', 'newArrivals', 'trendingProducts', 'brands', 'storyImages'));
    }
}
