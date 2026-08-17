<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desi Foods Invoice #{{ $order->order_number }}</title>
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <style>
        :root {
            --maroon: #890F14;
            --saffron: #E67E22;
            --saffron-deep: #D35400;
            --cream: #FFF8F0;
            --cream-warm: #F5E6D3;
            --cream-dark: #E8D5C0;
            --charcoal: #2C1810;
            --charcoal-light: #4A3428;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            color: var(--charcoal);
            background: #F9F6F0;
            padding: 30px 15px;
            margin: 0;
            line-height: 1.6;
        }

        .invoice-card {
            max-width: 840px;
            margin: 0 auto;
            background: var(--white);
            border: 1px solid var(--cream-dark);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(44, 24, 16, 0.08);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .invoice-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--maroon) 0%, var(--saffron) 50%, var(--saffron-deep) 100%);
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--cream-warm);
            padding-bottom: 24px;
            margin-bottom: 28px;
        }

        .brand-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--maroon);
            margin-bottom: 4px;
        }

        .inv-badge {
            text-align: right;
        }

        .inv-badge h2 {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px;
            color: var(--saffron-deep);
            margin: 0 0 6px;
            letter-spacing: 0.05em;
        }

        .address-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 32px;
            background: var(--cream);
            padding: 20px;
            border-radius: 14px;
            border: 1px solid var(--cream-dark);
        }

        .address-box h4 {
            font-family: 'Playfair Display', serif;
            color: var(--maroon);
            font-size: 1rem;
            margin: 0 0 8px;
            border-bottom: 1px solid var(--cream-dark);
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--cream-dark);
        }

        th {
            background: var(--cream-warm);
            color: var(--maroon);
            font-weight: 700;
            text-align: left;
            padding: 14px 16px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid var(--cream-dark);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--cream-dark);
            font-size: 14px;
            color: var(--charcoal);
        }

        tr:nth-child(even) td {
            background: rgba(255, 248, 240, 0.4);
        }

        .totals-box {
            margin-left: auto;
            max-width: 340px;
            background: var(--cream);
            border: 1px solid var(--cream-dark);
            border-radius: 14px;
            padding: 20px;
            font-size: 14px;
            line-height: 2;
        }

        .grand-total {
            font-size: 20px;
            font-weight: 700;
            color: var(--maroon);
            border-top: 2px solid var(--cream-dark);
            padding-top: 8px;
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
        }

        .footer-note {
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px solid var(--cream-dark);
            text-align: center;
            font-size: 12px;
            color: var(--charcoal-light);
        }

        .no-print {
            max-width: 840px;
            margin: 0 auto 20px;
            text-align: right;
        }

        .btn-print {
            background: var(--maroon);
            color: var(--white);
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(137, 15, 20, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-print:hover {
            background: var(--saffron-deep);
            transform: translateY(-2px);
        }

        @media print {
            body { background: #fff; padding: 0; }
            .invoice-card { box-shadow: none; border: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-print">
        <i class="fa-solid fa-print"></i> Print / Download PDF Invoice
    </button>
</div>

<div class="invoice-card">
    <!-- Header -->
    <div class="header-row">
        <div>
            <div class="brand-title">Desi Foods Hounslow</div>
            <div style="font-size: 13px; color: var(--charcoal-light); font-weight: 500;">
                <i class="fa-solid fa-location-dot me-1" style="color: var(--saffron);"></i> 3-4 Green Parade, Whitton Road, Hounslow, TW3 2EN<br>
                <i class="fa-solid fa-phone me-1" style="color: var(--saffron);"></i> +44 (0)20 8570 1234 &nbsp;|&nbsp; 
                <i class="fa-solid fa-envelope me-1" style="color: var(--saffron);"></i> support@desifoods.com<br>
                <strong>VAT Reg #:</strong> GB 987 6543 21
            </div>
        </div>
        <div class="inv-badge">
            <h2>OFFICIAL TAX INVOICE</h2>
            <div style="font-size: 13px; color: var(--charcoal-light);">
                <div><strong>Invoice #:</strong> INV-{{ $order->order_number }}</div>
                <div><strong>Order Reference:</strong> #{{ $order->order_number }}</div>
                <div><strong>Invoice Date:</strong> {{ $order->created_at->format('d M Y') }}</div>
                <div style="margin-top: 4px;">
                    <span style="background: rgba(230,126,34,0.15); color: var(--saffron-deep); padding: 3px 10px; border-radius: 12px; font-weight: 700; font-size: 11px; text-transform: uppercase;">
                        Payment: {{ strtoupper($order->payment_method) }} ({{ strtoupper($order->payment_status) }})
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer & Shipping Addresses -->
    <div class="address-grid">
        <div class="address-box">
            <h4>Billed & Shipped To</h4>
            <div style="font-size: 13px; color: var(--charcoal-light);">
                <strong style="color: var(--maroon); font-size: 14px;">{{ $order->shipping_address_json['name'] ?? $order->user->name }}</strong><br>
                {{ $order->shipping_address_json['address_line_1'] ?? '' }}<br>
                @if(!empty($order->shipping_address_json['address_line_2']))
                    {{ $order->shipping_address_json['address_line_2'] }}<br>
                @endif
                {{ $order->shipping_address_json['city'] ?? 'Hounslow' }}, {{ $order->shipping_address_json['state'] ?? 'Greater London' }} - <strong>{{ $order->shipping_address_json['pincode'] ?? '' }}</strong><br>
                <strong>Phone:</strong> {{ $order->shipping_address_json['phone'] ?? $order->user->phone }}
            </div>
        </div>

        <div class="address-box">
            <h4>Fulfilled By Store</h4>
            <div style="font-size: 13px; color: var(--charcoal-light);">
                <strong style="color: var(--maroon); font-size: 14px;">Desi Foods Express Grocery Hub</strong><br>
                3-4 Green Parade, Whitton Road<br>
                Hounslow, TW3 2EN, Greater London, UK<br>
                <strong>Express Delivery:</strong> Same-day / Next-day UK<br>
                <strong>Customer Support:</strong> 020 8570 1234
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Grocery Item Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td style="font-weight: 600; color: var(--maroon);">{{ $index + 1 }}</td>
                    <td>
                        <strong style="color: var(--maroon);">{{ $item->product_name }}</strong>
                        @if($item->variant_name)
                            <span style="font-size: 12px; color: var(--saffron-deep);">({{ $item->variant_name }})</span>
                        @endif
                    </td>
                    <td style="font-weight: 600;">{{ $item->quantity }}</td>
                    <td>£{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right; font-weight: 700; color: var(--maroon);">£{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals Summary Box -->
    <div class="totals-box">
        <div style="display: flex; justify-content: space-between;">
            <span>Item Subtotal:</span>
            <strong>£{{ number_format($order->subtotal, 2) }}</strong>
        </div>
        @if($order->discount_amount > 0)
            <div style="display: flex; justify-content: space-between; color: var(--saffron-deep);">
                <span>Discount ({{ $order->coupon_code }}):</span>
                <strong>-£{{ number_format($order->discount_amount, 2) }}</strong>
            </div>
        @endif
        <div style="display: flex; justify-content: space-between;">
            <span>Shipping & Handling:</span>
            <strong>£{{ number_format($order->shipping_fee, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--charcoal-light);">
            <span>VAT (0% Grocery Rate):</span>
            <span>£0.00</span>
        </div>
        <div class="grand-total">
            <span>Grand Total:</span>
            <span>£{{ number_format($order->grand_total, 2) }}</span>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="footer-note">
        <p style="margin: 0 0 4px; font-weight: 600; color: var(--maroon);">Thank you for shopping with Desi Foods Hounslow!</p>
        <div>This is an official computer-generated VAT tax invoice. For queries, contact support@desifoods.com</div>
    </div>
</div>

</body>
</html>
