<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        $request->validate(['status' => 'required|in:approved,rejected,pending']);

        $review->update(['status' => $request->status]);

        // Recalculate product rating average & count
        $product = $review->product;
        if ($product) {
            $approvedReviews = Review::where('product_id', $product->id)->where('status', 'approved');
            $avg = $approvedReviews->avg('rating') ?: 0.00;
            $count = $approvedReviews->count();
            $product->update(['rating_avg' => $avg, 'reviews_count' => $count]);
        }

        return back()->with('success', "Review #{$review->id} status updated to " . ucfirst($request->status) . ".");
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $product = $review->product;
        $review->delete();

        if ($product) {
            $approvedReviews = Review::where('product_id', $product->id)->where('status', 'approved');
            $avg = $approvedReviews->avg('rating') ?: 0.00;
            $count = $approvedReviews->count();
            $product->update(['rating_avg' => $avg, 'reviews_count' => $count]);
        }

        return back()->with('success', 'Review deleted successfully.');
    }
}
