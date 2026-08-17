@extends('layouts.admin')

@section('title', 'Add New Product | Admin')
@section('page-title', 'Add New Product')

@section('content')

<div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 36px; box-shadow: var(--shadow-sm); max-width: 850px;">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div style="grid-column: span 2;">
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Product Name</label>
                <input type="text" name="name" required placeholder="e.g. Shana Frozen Plain Paratha Pack (5pc)" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">SKU Code</label>
                <input type="text" name="sku" required placeholder="e.g. DESI-FZ-099" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Category</label>
                <select name="category_id" required style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Regular Price (£)</label>
                <input type="number" step="0.01" name="price" required placeholder="2.99" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Sale Price (£ Optional)</label>
                <input type="number" step="0.01" name="sale_price" placeholder="1.99" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Initial Stock Quantity</label>
                <input type="number" name="stock" value="50" required style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Brand</label>
                <select name="brand_id" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
                    <option value="">-- Select Brand --</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Image Upload Options -->
            <div style="grid-column: span 2; background: var(--cream); border: 2px dashed var(--saffron); border-radius: 16px; padding: 20px;">
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--maroon); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-camera" style="color: var(--saffron-deep);"></i> Product Photo / Image
                </h4>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 6px;">Option A: Upload Photo from Device / Phone</label>
                        <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 10px; background: var(--white); font-size: 0.88rem;">
                    </div>

                    <div>
                        <label style="font-weight: 600; font-size: 0.85rem; color: var(--maroon); display: block; margin-bottom: 6px;">Option B: Or Paste Image URL</label>
                        <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." style="width: 100%; padding: 10px; border: 1px solid var(--cream-dark); border-radius: 10px; background: var(--white); font-size: 0.88rem;">
                    </div>
                </div>
            </div>

            <div style="grid-column: span 2;">
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Product Description</label>
                <textarea name="description" rows="4" placeholder="Enter product description and details..." style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);"></textarea>
            </div>

            <div style="grid-column: span 2; display: flex; gap: 20px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_active" value="1" checked> Active in Catalog
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_featured" value="1"> Featured
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_trending" value="1"> Trending
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_new_arrival" value="1" checked> New Arrival
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 14px;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 28px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus"></i> Create Product
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline" style="padding: 14px 28px;">Cancel</a>
        </div>
    </form>
</div>

@endsection
