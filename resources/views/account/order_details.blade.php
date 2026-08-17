@extends('layouts.app')

@section('title', 'Order Details #' . $order->order_number . ' | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1100px; margin: 40px auto; padding: 0 24px;" x-data="{ openReviewModal: false, openReturnModal: false, activeProductId: null, activeProductName: '', userRating: 5 }">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--maroon);">Grocery Order Details #{{ $order->order_number }}</h1>
            <p style="color: var(--charcoal-light); font-size: 0.9rem;">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            @if(!in_array($order->order_status, ['returned', 'return_requested', 'cancelled']))
                <button type="button" @click="openReturnModal = true" class="btn btn-outline btn-sm" style="color: #C0392B; border-color: #E74C3C;">
                    <i class="fa-solid fa-rotate-left me-1"></i> Request Return / Issue
                </button>
            @endif
            <a href="{{ route('account.invoice.download', $order->order_number) }}" target="_blank" class="btn btn-primary btn-sm">
                📄 Print Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background-color: rgba(46, 125, 50, 0.1); color: #2E7D32; padding: 14px 20px; border-radius: 12px; border-left: 5px solid #2E7D32; margin-bottom: 24px; font-weight: 600;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <!-- Return Status Notification if submitted -->
    @if($order->order_status === 'return_requested')
        <div style="background: #FDF2E9; border: 1px solid #F5C6CB; border-left: 5px solid var(--saffron-deep); padding: 16px 20px; border-radius: 12px; margin-bottom: 24px;">
            <h4 style="font-family: 'Playfair Display', serif; color: var(--maroon); margin-bottom: 4px;">Return Requested</h4>
            <p style="font-size: 0.9rem; color: var(--charcoal-light); margin: 0;">Your return request for this order is currently being processed by our store manager at Whitton Road.</p>
        </div>
    @endif

    <!-- Stepper Status Timeline -->
    @php
        $statuses = ['pending' => 'Order Placed', 'confirmed' => 'Confirmed', 'packed' => 'Packed', 'shipped' => 'Out for Delivery', 'delivered' => 'Delivered'];
        $currentStatusKey = array_search($order->order_status, array_keys($statuses));
        if ($currentStatusKey === false) $currentStatusKey = 1;
    @endphp
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 32px; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 24px;">Take way Tracking Timeline</h3>
        <div style="display: flex; justify-content: space-between; position: relative;">
            @php $idx = 0; @endphp
            @foreach($statuses as $stKey => $stLabel)
                <div style="text-align: center; position: relative; z-index: 2; flex: 1;">
                    <div style="width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; margin: 0 auto 8px; {{ $idx <= $currentStatusKey ? 'background: var(--maroon); color: var(--white);' : 'background: var(--cream-dark); color: var(--muted);' }}">
                        {{ $idx + 1 }}
                    </div>
                    <div style="font-size: 0.85rem; font-weight: 600; {{ $idx <= $currentStatusKey ? 'color: var(--maroon);' : 'color: var(--muted);' }}">{{ $stLabel }}</div>
                </div>
                @php $idx++; @endphp
            @endforeach
        </div>

        @if($order->tracking_number)
            <div style="background: var(--cream); border: 1px solid var(--cream-dark); border-radius: 12px; padding: 14px 20px; margin-top: 24px; display: flex; justify-content: space-between; font-size: 0.9rem;">
                <span>Take way Option: <strong>{{ $order->delivery_partner ?: 'Local Hounslow Doorstep Express' }}</strong></span>
                <span>Tracking Ref #: <strong style="color: var(--maroon);">{{ $order->tracking_number }}</strong></span>
            </div>
        @endif
    </div>

    <!-- Items Table -->
    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 28px; margin-bottom: 32px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 20px;">Ordered Groceries & Items</h3>
        <div class="table-responsive">
            <table class="custom-table">
            <thead>
                <tr>
                    <th>Grocery Item</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td style="font-weight: 600; color: var(--maroon);">
                            {{ $item->product_name }} {{ $item->variant_name ? "({$item->variant_name})" : '' }}
                        </td>
                        <td style="font-size: 0.85rem; color: var(--muted);">{{ $item->sku }}</td>
                        <td>£{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="font-weight: 700; color: var(--maroon);">£{{ number_format($item->subtotal, 2) }}</td>
                        <td>
                            <button type="button" 
                                    @click="openReviewModal = true; activeProductId = {{ $item->product_id }}; activeProductName = '{{ addslashes($item->product_name) }}'"
                                    class="btn btn-outline btn-sm" style="color: var(--saffron-deep); border-color: var(--saffron); padding: 6px 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-star"></i> Rate & Review
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>

    <!-- Rate & Review Centered Modal Window -->
    <div x-show="openReviewModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position: fixed; inset: 0; background: rgba(44, 24, 16, 0.75); backdrop-filter: blur(8px); z-index: 999999; display: grid; place-items: center; padding: 20px; overflow-y: auto;" 
         x-cloak>
        
        <div style="background: var(--white); border-radius: 24px; padding: 36px; max-width: 520px; width: 100%; box-shadow: 0 25px 60px rgba(0,0,0,0.35); border: 1px solid var(--cream-dark); margin: auto; position: relative;" 
             @click.away="openReviewModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; color: var(--maroon); font-size: 1.5rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-star" style="color: var(--gold);"></i> Rate & Review Product
                </h3>
                <button type="button" @click="openReviewModal = false" style="background: none; border: none; font-size: 1.3rem; cursor: pointer; color: var(--maroon); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--cream);">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <p style="font-size: 0.92rem; color: var(--charcoal-light); margin-bottom: 20px;">Reviewing item: <strong x-text="activeProductName" style="color: var(--maroon);"></strong></p>

            <form action="{{ route('account.review.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" :value="activeProductId">
                <input type="hidden" name="rating" :value="userRating">

                <!-- Star Rating Selector -->
                <div style="margin-bottom: 20px; background: var(--cream); padding: 16px; border-radius: 16px; border: 1px solid var(--cream-dark); text-align: center;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 10px;">Click Stars to Select Rating</label>
                    <div style="display: flex; gap: 12px; font-size: 2.2rem; color: var(--gold); cursor: pointer; justify-content: center;">
                        <template x-for="star in 5">
                            <i class="fa-solid fa-star" 
                               @click="userRating = star"
                               :style="star <= userRating ? 'color: var(--gold); transform: scale(1.1);' : 'color: var(--cream-dark);'"></i>
                        </template>
                    </div>
                </div>

                <!-- Review Title -->
                <div style="margin-bottom: 18px;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Review Headline</label>
                    <input type="text" name="title" required placeholder="e.g. Authentic quality and fresh packaging!" 
                           style="width: 100%; padding: 12px 14px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">
                </div>

                <!-- Review Comment -->
                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Your Detailed Feedback</label>
                    <textarea name="comment" rows="4" required placeholder="Write your review here..." 
                              style="width: 100%; padding: 12px 14px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);"></textarea>
                </div>

                <div style="display: flex; gap: 14px;">
                    <button type="button" @click="openReviewModal = false" class="btn btn-outline" style="flex: 1; padding: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fa-solid fa-paper-plane"></i> Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Request Return Centered Modal Window -->
    <div x-show="openReturnModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="position: fixed; inset: 0; background: rgba(44, 24, 16, 0.75); backdrop-filter: blur(8px); z-index: 999999; display: grid; place-items: center; padding: 20px; overflow-y: auto;" 
         x-cloak>
        
        <div style="background: var(--white); border-radius: 24px; padding: 36px; max-width: 540px; width: 100%; box-shadow: 0 25px 60px rgba(0,0,0,0.35); border: 1px solid var(--cream-dark); margin: auto; position: relative;" 
             @click.away="openReturnModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; color: var(--maroon); font-size: 1.5rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-rotate-left" style="color: #C0392B;"></i> Request Order Return
                </h3>
                <button type="button" @click="openReturnModal = false" style="background: none; border: none; font-size: 1.3rem; cursor: pointer; color: var(--maroon); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--cream);">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <form action="{{ route('account.orders.return', $order->order_number) }}" method="POST">
                @csrf
                <!-- Return Reason Dropdown -->
                <div style="margin-bottom: 18px;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Reason for Return</label>
                    <select name="reason" required style="width: 100%; padding: 12px 14px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);">
                        <option value="Damaged or Spoiled Item">Damaged or Spoiled Item</option>
                        <option value="Wrong Item Delivered">Wrong Item Delivered</option>
                        <option value="Expired Product">Expired Product</option>
                        <option value="Quality Issue">Quality Issue</option>
                        <option value="Missing Items in Box">Missing Items in Package</option>
                        <option value="Other">Other Reason</option>
                    </select>
                </div>

                <!-- Description Textarea -->
                <div style="margin-bottom: 24px;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Please describe the issue in detail</label>
                    <textarea name="description" rows="4" required placeholder="Tell us what was wrong with the item..." 
                              style="width: 100%; padding: 12px 14px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.92rem; outline: none; background: var(--cream);"></textarea>
                </div>

                <div style="display: flex; gap: 14px;">
                    <button type="button" @click="openReturnModal = false" class="btn btn-outline" style="flex: 1; padding: 12px;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px; background: #C0392B; border-color: #C0392B; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fa-solid fa-paper-plane"></i> Submit Return
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
