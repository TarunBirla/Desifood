@extends('layouts.admin')

@section('title', 'Product Management | Admin')
@section('page-title', 'Product Management')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <form action="{{ route('admin.products.index') }}" method="GET" style="display: flex; gap: 12px;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or SKU..." style="padding: 8px 14px; border: 1px solid var(--line); border-radius: var(--radius); width: 280px;">
        <button type="submit" class="btn btn-outline btn-sm">Filter</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Add New Product</a>
</div>

<table class="custom-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img src="{{ $product->primaryImage ? $product->primaryImage->image_path : 'https://via.placeholder.com/40' }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: var(--radius);">
                        <strong style="color: var(--ink);">{{ $product->name }}</strong>
                    </div>
                </td>
                <td style="font-size: 0.85rem; color: var(--muted);">{{ $product->sku }}</td>
                <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                <td style="font-weight: 700; color: var(--green);">£{{ number_format($product->effective_price, 2) }}</td>
                <td>
                    <span class="badge-status {{ $product->stock <= 5 ? 'badge-danger' : 'badge-success' }}">
                        {{ $product->stock }} units
                    </span>
                </td>
                <td>
                    <span class="badge-status {{ $product->is_active ? 'badge-success' : 'badge-info' }}">
                        {{ $product->is_active ? 'Active' : 'Disabled' }}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline btn-sm" style="padding: 4px 10px;">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm" style="color: var(--clay); padding: 4px 10px;">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 24px;">{{ $products->links('vendor.pagination.custom') }}</div>

@endsection
