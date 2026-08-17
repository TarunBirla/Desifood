<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartWishlistController extends Controller
{
    /**
     * Get or create cart for logged in user or guest session
     */
    private function getCart(Request $request): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = $request->session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function viewCart(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your shopping cart.');
        }
        $cart = $this->getCart($request)->load('items.product.primaryImage', 'items.variant');
        return view('cart.index', compact('cart'));
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login to add items to cart.', 'redirect' => route('login')], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to add items to your cart.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variant = $request->variant_id ? ProductVariant::find($request->variant_id) : null;

        // Stock check
        $stock = $variant ? $variant->stock : $product->stock;
        if ($stock < $request->quantity) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "Only {$stock} items left in stock."], 422);
            }
            return back()->with('error', "Only {$stock} items left in stock.");
        }

        $cart = $this->getCart($request);
        $unitPrice = $variant ? $variant->effective_price : $product->effective_price;

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $request->variant_id)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $request->quantity;
            if ($stock < $newQty) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => "Maximum available stock is {$stock}."], 422);
                }
                return back()->with('error', "Maximum available stock is {$stock}.");
            }
            $cartItem->update(['quantity' => $newQty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $request->variant_id,
                'quantity' => $request->quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        $cart = $cart->fresh('items');
        $totalItems = $cart->items()->sum('quantity');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Added {$product->name} to cart!",
                'cart_count' => $totalItems
            ]);
        }

        return back()->with('success', "{$product->name} added to cart successfully!");
    }

    public function updateCart(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cartItem = CartItem::with('product', 'variant', 'cart.items')->findOrFail($itemId);
        $stock = $cartItem->variant ? $cartItem->variant->stock : $cartItem->product->stock;

        if ($stock < $request->quantity) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => "Maximum available stock is {$stock}."], 422);
            }
            return back()->with('error', "Maximum available stock is {$stock}.");
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cart = $cartItem->cart->fresh('items');
        $itemSubtotal = $cartItem->unit_price * $cartItem->quantity;
        $cartSubtotal = $cart->items->sum(fn($i) => $i->unit_price * $i->quantity);
        $cartCount = $cart->items->sum('quantity');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cart updated.',
                'item_subtotal' => '£' . number_format($itemSubtotal, 2),
                'cart_subtotal' => '£' . number_format($cartSubtotal, 2),
                'cart_grand_total' => '£' . number_format($cartSubtotal > 35 ? $cartSubtotal : $cartSubtotal + 3.99, 2),
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Cart updated successfully.');
    }

    public function removeCartItem(Request $request, $itemId)
    {
        $cartItem = CartItem::with('cart.items')->find($itemId);
        if ($cartItem) {
            $cart = $cartItem->cart;
            $cartItem->delete();
            $cart = $cart->fresh('items');
            $cartSubtotal = $cart->items->sum(fn($i) => $i->unit_price * $i->quantity);
            $cartCount = $cart->items->sum('quantity');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item removed.',
                    'cart_subtotal' => '£' . number_format($cartSubtotal, 2),
                    'cart_grand_total' => '£' . number_format($cartSubtotal > 35 ? $cartSubtotal : $cartSubtotal + 3.99, 2),
                    'cart_count' => $cartCount,
                ]);
            }
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function viewWishlist()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your wishlist.');
        }
        $wishlists = Wishlist::with('product.primaryImage')->where('user_id', Auth::id())->get();
        return view('account.wishlist', compact('wishlists'));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        if (!Auth::check()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login to save wishlist.', 'redirect' => route('login')], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to save items to your wishlist.');
        }

        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $added = false;
            $msg = 'Removed from wishlist.';
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $request->product_id]);
            $added = true;
            $msg = 'Added to wishlist!';
        }

        if ($request->wantsJson() || $request->ajax()) {
            $count = Wishlist::where('user_id', Auth::id())->count();
            return response()->json(['success' => true, 'added' => $added, 'message' => $msg, 'count' => $count]);
        }

        return back()->with('success', $msg);
    }
}
