@extends('layouts.admin')

@section('title', 'Edit Product #' . $product->id . ' | Admin')
@section('page-title', 'Edit Product: ' . $product->name)

@section('content')

<div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 36px; box-shadow: var(--shadow-sm); max-width: 850px;">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
            <div style="grid-column: span 2;">
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Product Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">SKU Code (Read Only)</label>
                <input type="text" value="{{ $product->sku }}" disabled style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream-dark); color: var(--muted);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Category</label>
                <select name="category_id" required style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Regular Price (£)</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Sale Price (£ Optional)</label>
                <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Stock Quantity</label>
                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
            </div>

            <div>
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Brand</label>
                <select name="brand_id" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">
                    <option value="">-- Select Brand --</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ $product->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Image File Upload Section -->
            <div style="grid-column: span 2; background: var(--cream); border: 2px dashed var(--saffron); border-radius: 16px; padding: 20px;">
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--maroon); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-camera" style="color: var(--saffron-deep);"></i> Change Product Photo
                </h4>
                <label style="font-weight: 600; font-size: 0.88rem; color: var(--maroon); display: block; margin-bottom: 6px;">Upload New Photo from Phone Gallery / Device</label>
                <input type="file" name="image_file" accept="image/*" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 10px; background: var(--white); font-size: 0.92rem; margin-bottom: 12px;">

                @if($product->primaryImage)
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 0.82rem; color: var(--muted);">Current Active Image:</span>
                        <img src="{{ $product->primaryImage->image_path }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--cream-dark);">
                    </div>
                @endif
            </div>

            <div style="grid-column: span 2;">
                <label style="font-weight: 600; font-size: 0.9rem; color: var(--maroon); display: block; margin-bottom: 6px;">Product Description</label>
                <textarea name="description" rows="5" style="width: 100%; padding: 12px; border: 1px solid var(--cream-dark); border-radius: 12px; font-size: 0.95rem; background: var(--cream);">{{ old('description', $product->description) }}</textarea>
            </div>

            <div style="grid-column: span 2; display: flex; gap: 20px; flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}> Active in Catalog
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}> Featured
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_trending" value="1" {{ $product->is_trending ? 'checked' : '' }}> Trending
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500;">
                    <input type="checkbox" name="is_new_arrival" value="1" {{ $product->is_new_arrival ? 'checked' : '' }}> New Arrival
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 14px;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 28px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-floppy-disk"></i> Update Product
            </button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline" style="padding: 14px 28px;">Cancel</a>
        </div>
    </form>
</div>

@endsection
