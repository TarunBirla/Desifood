@extends('layouts.app')

@section('title', 'Frequently Asked Questions (FAQs) | Desi Foods Hounslow')

@section('content')

<div style="max-width: 1000px; margin: 40px auto; padding: 0 24px;">
    <!-- Header Hero -->
    <div style="text-align: center; margin-bottom: 40px;">
        <span class="badge-status badge-info" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; padding: 6px 16px; margin-bottom: 12px; display: inline-block;">
            <i class="fa-solid fa-circle-question me-1"></i> Support & Guidance
        </span>
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--maroon); margin-bottom: 12px;">Frequently Asked Questions</h1>
        <p style="color: var(--charcoal-light); font-size: 1.05rem; max-width: 650px; margin: 0 auto; line-height: 1.6;">
            Have questions about takeaway grocery pickup, store timing, payment options, or next-month recurring orders? Find instant answers below.
        </p>
    </div>

    <!-- FAQ Accordion List -->
    <div style="display: flex; flex-direction: column; gap: 16px;">
        @forelse($faqs as $index => $faq)
            <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }" 
                 style="background: var(--white); border: 1.5px solid var(--cream-dark); border-radius: 18px; overflow: hidden; box-shadow: var(--shadow-sm); transition: all 0.3s ease;">
                <button @click="open = !open" 
                        style="width: 100%; text-align: left; padding: 20px 24px; background: none; border: none; font-size: 1.1rem; font-weight: 700; color: var(--maroon); cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 16px; outline: none;">
                    <span style="display: flex; align-items: center; gap: 12px;">
                        <span style="color: var(--saffron-deep); font-family: 'Playfair Display', serif; font-size: 1.2rem;">Q{{ $index + 1 }}.</span>
                        <span>{{ $faq->question }}</span>
                    </span>
                    <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'" style="color: var(--saffron); font-size: 0.95rem; transition: transform 0.3s;"></i>
                </button>

                <div x-show="open" x-collapse 
                     style="padding: 0 24px 20px 52px; color: var(--charcoal-light); font-size: 0.98rem; line-height: 1.7; border-top: 1px dashed var(--cream-dark); margin-top: 4px; padding-top: 16px;">
                    {!! nl2br(e($faq->answer)) !!}
                </div>
            </div>
        @empty
            <div style="text-align: center; padding: 50px; background: var(--white); border-radius: 20px; border: 1px solid var(--cream-dark); color: var(--muted);">
                No FAQs available at the moment.
            </div>
        @endforelse
    </div>

    <!-- Contact Support Box -->
    <div style="background: var(--cream); border: 1.5px solid var(--cream-dark); border-radius: 24px; padding: 32px; margin-top: 48px; text-align: center;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; color: var(--maroon); margin-bottom: 8px;">Still Have Questions?</h3>
        <p style="color: var(--charcoal-light); font-size: 0.95rem; margin-bottom: 20px;">
            Visit our Hounslow store directly or call our support team. We're always happy to assist you!
        </p>
        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <a href="tel:+442085701234" class="btn btn-primary" style="border-radius: 30px; padding: 12px 28px; font-weight: 700;">
                <i class="fa-solid fa-phone me-2"></i> Call +44 20 8570 1234
            </a>
            <a href="{{ route('home') }}#contact" class="btn btn-outline-primary" style="border-radius: 30px; padding: 12px 28px; font-weight: 700;">
                <i class="fa-solid fa-store me-2"></i> Visit Hounslow Store
            </a>
        </div>
    </div>
</div>

@endsection
