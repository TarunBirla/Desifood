@extends('layouts.admin')

@section('title', 'Store Settings & Homepage Slider | Admin')
@section('page-title', 'Platform Settings & Story Slider Manager')

@section('content')

@php
    $defaultSlides = [
        'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=1200&q=80',
        'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=1200&q=80',
        'https://images.unsplash.com/photo-1542838132-92c53300491e?w=1200&q=80',
        'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=1200&q=80',
        'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=1200&q=80'
    ];
    
    $existingSetting = $settings['story_slider_images'] ?? null;
    if ($existingSetting) {
        $decoded = json_decode($existingSetting, true);
        $currentSlides = is_array($decoded) && count($decoded) > 0 ? $decoded : $defaultSlides;
    } else {
        $currentSlides = $defaultSlides;
    }
@endphp

<div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 36px; box-shadow: var(--shadow-sm); max-width: 900px;" 
     x-data="sliderManager({{ json_encode($currentSlides) }})">

    @if(session('success'))
        <div style="background: #E8F8F5; border: 1px solid #A3E4D7; color: #117864; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- General Store Info -->
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-store" style="color: var(--saffron);"></i> General Store Info
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 32px;">
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Store Name</label>
                <input type="text" name="store_name" value="{{ $settings['store_name'] ?? 'Desi Foods Hounslow' }}" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Store Contact Email</label>
                <input type="email" name="store_email" value="{{ $settings['store_email'] ?? 'info@desifoodshounslow.co.uk' }}" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>
        </div>

        <!-- Homepage "Our Story" Auto Image Slider Manager -->
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 12px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa-solid fa-images" style="color: var(--saffron);"></i> Homepage "Our Story" Auto Slider</span>
            <span style="font-size: 0.85rem; font-family: 'Inter', sans-serif; color: var(--saffron-deep); font-weight: 600;" x-text="images.length + ' Active Slides'"></span>
        </h3>
        <p style="font-size: 0.88rem; color: var(--charcoal-light); margin-bottom: 20px;">
            Upload photo files directly from your phone gallery or laptop storage for the homepage auto-slider.
        </p>

        <!-- Hidden Input sent with form -->
        <input type="hidden" name="story_slider_images" :value="JSON.stringify(images)">

        <!-- Active Images Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; margin-bottom: 28px;">
            <template x-for="(imgUrl, index) in images" :key="index">
                <div style="background: var(--cream); border: 2px solid var(--cream-dark); border-radius: 16px; overflow: hidden; position: relative; box-shadow: var(--shadow-sm); display: flex; flex-direction: column;">
                    <div style="height: 140px; position: relative; overflow: hidden;">
                        <img :src="imgUrl" alt="Slider image" style="width: 100%; height: 100%; object-fit: cover;">
                        <span style="position: absolute; top: 8px; left: 8px; background: var(--maroon); color: var(--white); font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;" x-text="'Slide #' + (index + 1)"></span>
                    </div>

                    <div style="padding: 12px; background: var(--white); display: flex; flex-direction: column; gap: 8px; flex: 1; justify-content: space-between;">
                        <span style="font-size: 0.75rem; color: var(--muted); word-break: break-all; max-height: 36px; overflow: hidden;" x-text="imgUrl"></span>
                        
                        <button type="button" @click="removeImage(index)" 
                                class="btn btn-outline btn-sm" 
                                style="color: #C0392B; border-color: #FADBD8; background: #FDEDEC; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 12px; font-weight: 600; width: 100%;">
                            <i class="fa-solid fa-trash-can"></i> Delete Image
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Upload New Image Section -->
        <div style="background: var(--cream); border: 2px dashed var(--saffron); border-radius: 18px; padding: 20px; margin-bottom: 36px;">
            <h4 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--maroon); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-camera" style="color: var(--saffron-deep);"></i> Upload New Slider Photos
            </h4>
            <label style="font-weight: 600; font-size: 0.88rem; color: var(--maroon); display: block; margin-bottom: 6px;">Upload Photos from Device / Phone Gallery</label>
            <input type="file" name="slider_files[]" multiple accept="image/*" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 10px; background: var(--white); font-size: 0.92rem;">
            <span style="font-size: 0.78rem; color: var(--muted); display: block; margin-top: 6px;">Select 1 or multiple photos from your phone gallery. Photos will be uploaded when saving settings.</span>
        </div>

        <!-- Payment Gateway Settings -->
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 16px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-credit-card" style="color: var(--saffron);"></i> Online Payment Gateway Settings
        </h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 32px;">
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Razorpay / Payment Key ID</label>
                <input type="text" name="razorpay_key_id" value="{{ $settings['razorpay_key_id'] ?? 'rzp_test_samplekey123' }}" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--maroon); display: block; margin-bottom: 6px;">Razorpay Key Secret</label>
                <input type="password" name="razorpay_key_secret" value="{{ $settings['razorpay_key_secret'] ?? 'sample_razorpay_secret_456' }}" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 14px 36px; font-size: 1.05rem; display: inline-flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-floppy-disk"></i> Save Settings & Upload Photos
        </button>
    </form>
</div>

@endsection

@section('scripts')
<script>
    function sliderManager(initialImages) {
        return {
            images: initialImages || [],
            removeImage(index) {
                if (confirm('Are you sure you want to delete this slide image?')) {
                    this.images.splice(index, 1);
                }
            }
        }
    }
</script>
@endsection
