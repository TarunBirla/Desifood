@extends('layouts.admin')

@section('title', 'Blog Management | Admin')
@section('page-title', 'CMS Articles & Blog Management')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h3>Published Articles</h3>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">+ Create New Article</a>
</div>

<table class="custom-table">
    <thead>
        <tr>
            <th>Article Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Published Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($blogs as $b)
            <tr>
                <td style="font-weight: 600; color: var(--ink);">{{ $b->title }}</td>
                <td>{{ $b->category ? $b->category->name : 'Uncategorized' }}</td>
                <td><span class="badge-status {{ $b->is_published ? 'badge-success' : 'badge-warning' }}">{{ $b->is_published ? 'Published' : 'Draft' }}</span></td>
                <td>{{ $b->published_at ? $b->published_at->format('d M Y') : 'N/A' }}</td>
                <td>
                    <form action="{{ route('admin.blogs.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Delete this blog post?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline btn-sm" style="color: var(--clay); padding: 4px 10px;">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 24px;">{{ $blogs->links('vendor.pagination.custom') }}</div>

@endsection
