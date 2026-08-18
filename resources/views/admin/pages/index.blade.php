@extends('layouts.admin')

@section('title', 'Terms & Privacy Policy Content Manager | Admin')
@section('page-title', 'Terms & Conditions and Privacy Policy Editor')

@section('content')

<div style="max-width: 1000px;" x-data="{ tab: 'terms' }">
    @if(session('success'))
        <div style="background: #E8F8F5; border: 1px solid #A3E4D7; color: #117864; padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Tab Toggle Header -->
    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <button type="button" @click="tab = 'terms'" 
                :class="tab === 'terms' ? 'btn-primary' : 'btn-outline-primary'" 
                class="btn" style="border-radius: 30px; padding: 12px 28px; font-weight: 700;">
            <i class="fa-solid fa-file-contract me-2"></i> Terms & Conditions
        </button>
        <button type="button" @click="tab = 'privacy'" 
                :class="tab === 'privacy' ? 'btn-primary' : 'btn-outline-primary'" 
                class="btn" style="border-radius: 30px; padding: 12px 28px; font-weight: 700;">
            <i class="fa-solid fa-shield-halved me-2"></i> Privacy Policy
        </button>
    </div>

    <form action="{{ route('admin.pages.update') }}" method="POST">
        @csrf

        <!-- Tab 1: Terms & Conditions Editor -->
        <div x-show="tab === 'terms'" style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 32px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">
                    <i class="fa-solid fa-file-contract me-2" style="color: var(--saffron);"></i> Edit Terms & Conditions Content
                </h3>
                <a href="{{ route('terms') }}" target="_blank" class="btn btn-outline btn-sm" style="border-radius: 20px; font-weight: 600;">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Preview Live Page
                </a>
            </div>
            <p style="font-size: 0.88rem; color: var(--charcoal-light); margin-bottom: 16px;">
                You can write formatted HTML headings (e.g. <code>&lt;h3&gt;Section Title&lt;/h3&gt;</code>) and paragraphs (<code>&lt;p&gt;Content...&lt;/p&gt;</code>).
            </p>
            <textarea name="terms_content" rows="18" required style="width: 100%; padding: 16px; border: 1.5px solid var(--cream-dark); border-radius: 16px; font-family: 'Consolas', monospace, sans-serif; font-size: 0.95rem; outline: none; line-height: 1.6;">{{ old('terms_content', $terms->content) }}</textarea>
        </div>

        <!-- Tab 2: Privacy Policy Editor -->
        <div x-show="tab === 'privacy'" style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 24px; padding: 32px; box-shadow: var(--shadow-sm);" x-cloak>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--cream-dark); padding-bottom: 12px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin: 0;">
                    <i class="fa-solid fa-shield-halved me-2" style="color: var(--saffron);"></i> Edit Privacy Policy Content
                </h3>
                <a href="{{ route('privacy') }}" target="_blank" class="btn btn-outline btn-sm" style="border-radius: 20px; font-weight: 600;">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Preview Live Page
                </a>
            </div>
            <p style="font-size: 0.88rem; color: var(--charcoal-light); margin-bottom: 16px;">
                You can write formatted HTML headings (e.g. <code>&lt;h3&gt;Section Title&lt;/h3&gt;</code>) and paragraphs (<code>&lt;p&gt;Content...&lt;/p&gt;</code>).
            </p>
            <textarea name="privacy_content" rows="18" required style="width: 100%; padding: 16px; border: 1.5px solid var(--cream-dark); border-radius: 16px; font-family: 'Consolas', monospace, sans-serif; font-size: 0.95rem; outline: none; line-height: 1.6;">{{ old('privacy_content', $privacy->content) }}</textarea>
        </div>

        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary" style="padding: 14px 36px; font-size: 1.05rem; border-radius: 30px; font-weight: 700; display: inline-flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-floppy-disk"></i> Save Terms & Privacy Policy Content
            </button>
        </div>
    </form>
</div>

@endsection
