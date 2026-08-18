@extends('layouts.app')

@section('title', 'Terms & Conditions | Desi Foods Hounslow')

@section('content')

<div style="max-width: 900px; margin: 40px auto; padding: 0 24px;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--muted); margin-bottom: 20px;">
        <a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; 
        <span style="color: var(--maroon); font-weight: 600;">Terms & Conditions</span>
    </div>

    <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 40px; box-shadow: var(--shadow-sm);">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.2rem; color: var(--maroon); margin-bottom: 8px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 14px;">
            Terms & Conditions
        </h1>
        <div style="font-size: 0.85rem; color: var(--muted); margin-bottom: 28px;">
            Last Updated: {{ $page->updated_at ? $page->updated_at->format('d M Y') : date('d M Y') }}
        </div>

        <div style="font-size: 1rem; color: var(--charcoal); line-height: 1.8;" class="cms-page-body">
            {!! $page->content !!}
        </div>
    </div>
</div>

@endsection
