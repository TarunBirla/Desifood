@extends('layouts.admin')

@section('title', 'Store Settings | Admin')
@section('page-title', 'Platform Settings & Story Slider')

@section('content')

<div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; padding: 32px; box-shadow: var(--shadow-sm); max-width: 800px;">
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 8px;">General Store Info</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon);">Store Name</label>
                <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'Desi Foods Hounslow' }}" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon);">Store Contact Email</label>
                <input type="email" name="store_email" value="{{ $settings['store_email'] ?? 'info@desifoodshounslow.co.uk' }}" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
            </div>
        </div>

        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 8px;">Homepage "Our Story" Auto Image Slider</h3>
        <div style="margin-bottom: 24px;">
            <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Slider Image URLs (JSON Array or 1 URL per line)</label>
            <textarea name="story_slider_images" rows="5" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 8px; font-family: monospace; font-size: 0.88rem;" placeholder="https://images.unsplash.com/...&#10;https://images.unsplash.com/..."></textarea>
            <span style="font-size: 0.78rem; color: var(--muted); display: block; margin-top: 4px;">Enter image URLs separated by newlines to control the homepage story auto-slider.</span>
        </div>

        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 8px;">Online Payment Gateway Settings</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon);">Razorpay / Payment Key ID</label>
                <input type="text" name="razorpay_key_id" value="{{ $settings['razorpay_key_id'] ?? 'rzp_test_samplekey123' }}" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon);">Razorpay Key Secret</label>
                <input type="password" name="razorpay_key_secret" value="{{ $settings['razorpay_key_secret'] ?? 'sample_razorpay_secret_456' }}" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 8px;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Save Settings</button>
    </form>
</div>

@endsection
