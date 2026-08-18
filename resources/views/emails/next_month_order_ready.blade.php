<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your {{ $targetMonthName }} Recurring Order Is Ready to Review</title>
</head>
<body style="font-family: Arial, sans-serif; background: #FFFDF9; color: #2C2C2C; padding: 20px; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; background: #FFFFFF; border: 1px solid #EAE4D9; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="text-align: center; border-bottom: 2px solid #EAE4D9; padding-bottom: 20px; margin-bottom: 24px;">
            <h1 style="color: #890F14; font-family: 'Georgia', serif; font-size: 1.8rem; margin: 0 0 8px 0;">Desi Foods Hounslow</h1>
            <p style="color: #E67E22; font-weight: 700; margin: 0; font-size: 0.95rem;">Authentic Indian Groceries & Fresh Produce</p>
        </div>

        <!-- Greeting -->
        <h2 style="color: #890F14; font-size: 1.3rem;">Hello {{ $user->name }},</h2>
        <p>Your grocery order list for <strong>{{ $targetMonthName }}</strong> is ready for your review!</p>

        <!-- Transparent Info Box -->
        <div style="background: #FFF8EE; border-left: 4px solid #E67E22; padding: 14px 18px; border-radius: 8px; margin-bottom: 24px;">
            <strong style="color: #890F14; display: block; margin-bottom: 4px;">Important Note: No Payment Has Been Taken</strong>
            <span style="font-size: 0.9rem; color: #555;">This is a friendly reminder to review your recurring selections. You can edit quantities, remove or add items, and complete your order manually whenever you are ready.</span>
        </div>

        <!-- Products Table -->
        <h3 style="color: #890F14; font-size: 1.1rem; border-bottom: 1px solid #EAE4D9; padding-bottom: 8px;">Selected Products for {{ $targetMonthName }}</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
            <thead>
                <tr style="background: #FFF8EE; text-align: left;">
                    <th style="padding: 10px; font-size: 0.85rem; color: #890F14;">Product</th>
                    <th style="padding: 10px; font-size: 0.85rem; color: #890F14; text-align: center;">Qty</th>
                    <th style="padding: 10px; font-size: 0.85rem; color: #890F14; text-align: right;">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recurringOrders as $item)
                    @php $price = $item->variant ? $item->variant->effective_price : ($item->product ? $item->product->effective_price : $item->unit_price); @endphp
                    <tr style="border-bottom: 1px solid #F0F0F0;">
                        <td style="padding: 10px; font-weight: 600; color: #2C2C2C;">
                            {{ $item->product ? $item->product->name : 'Product' }}
                            @if($item->variant)
                                <div style="font-size: 0.78rem; color: #777;">{{ $item->variant->variant_name }}</div>
                            @endif
                        </td>
                        <td style="padding: 10px; text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right; font-weight: 700; color: #890F14;">£{{ number_format($price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Subtotal -->
        <div style="text-align: right; margin-bottom: 28px; font-size: 1.1rem;">
            <span>Estimated Total: </span>
            <strong style="color: #890F14; font-size: 1.4rem; margin-left: 6px;">£{{ number_format($totalEst, 2) }}</strong>
        </div>

        <!-- CTA Buttons -->
        <div style="text-align: center; margin-bottom: 32px;">
            <a href="{{ route('account.recurring') }}" style="background: #890F14; color: #FFFFFF; padding: 14px 32px; border-radius: 30px; text-decoration: none; font-weight: 700; display: inline-block; font-size: 1rem; box-shadow: 0 4px 10px rgba(137, 15, 20, 0.25);">
                Review & Checkout Order →
            </a>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #EAE4D9; padding-top: 16px; font-size: 0.82rem; color: #777; text-align: center;">
            Desi Foods Hounslow &bull; 3-4 Green Parade, Whitton Road, Hounslow TW3 2EN<br>
            If you need help, contact support at <a href="mailto:support@desifoods.co.uk" style="color: #E67E22;">support@desifoods.co.uk</a>
        </div>
    </div>
</body>
</html>
