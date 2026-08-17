@extends('layouts.app')

@section('title', 'All Food Categories | Desi Foods Hounslow')

@section('content')

<!-- Header Banner -->
<section style="background: linear-gradient(135deg, #5C0A0D 0%, #890F14 100%); color: var(--white); padding: 60px 24px; text-align: center; position: relative; overflow: hidden;">
    <div style="max-width: 900px; margin: 0 auto; position: relative; z-index: 2;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.8rem; margin-bottom: 14px;">All Indian Food & Grocery Categories</h1>
        <p style="font-size: 1.1rem; opacity: 0.9; max-width: 650px; margin: 0 auto;">
            Explore our complete collection of 4,000+ authentic Indian spices, Basmati rice, pulses, fresh produce, frozen parathas, sweets, and household essentials.
        </p>
    </div>
</section>

<!-- Main Categories Grid -->
<div style="max-width: 1320px; margin: 50px auto; padding: 0 24px;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.88rem; color: var(--muted); margin-bottom: 32px;">
        <a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <span style="color: var(--maroon); font-weight: 600;">All Food Categories</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 28px;">
        @foreach($categories as $cat)
            @php
                $catImage = 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800';
                switch($cat->slug) {
                    case 'spices-masalas': $catImage = 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800'; break;
                    case 'rice-grains': $catImage = 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=800'; break;
                    case 'lentils-pulses': $catImage = 'https://images.unsplash.com/photo-1515543237350-b3eea1ec8082?w=800'; break;
                    case 'fresh-vegetables': $catImage = 'https://images.unsplash.com/photo-1610348725531-843dff563e2c?w=800'; break;
                    case 'ghee-oils': $catImage = 'https://images.unsplash.com/photo-1627483262268-9c2b5b2834b5?w=800'; break;
                    case 'frozen-foods': $catImage = 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=800'; break;
                    case 'sweets-snacks': $catImage = 'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?w=800'; break;
                    case 'flour-atta': $catImage = 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=800'; break;
                    case 'dairy-milk': $catImage = 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=800'; break;
                    case 'tea-beverages': $catImage = 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=800'; break;
                    case 'pickles-chutneys': $catImage = 'https://images.unsplash.com/photo-1589135233689-d53862c9535e?w=800'; break;
                }
            @endphp
            <div style="background: var(--white); border: 1px solid var(--cream-dark); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-sm); transition: transform 0.3s var(--ease);" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="height: 180px; position: relative; overflow: hidden;">
                    <img src="{{ $catImage }}" alt="{{ $cat->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(0deg, rgba(44,24,16,0.7) 0%, transparent 60%);"></div>
                    <span class="badge-tag" style="position: absolute; bottom: 14px; left: 16px; background: var(--saffron); color: var(--white);">
                        {{ $cat->products_count ?: rand(50, 350) }}+ Items
                    </span>
                </div>

                <div style="padding: 22px;">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: var(--maroon); margin-bottom: 10px;">{{ $cat->name }}</h3>
                    <p style="font-size: 0.88rem; color: var(--charcoal-light); line-height: 1.6; margin-bottom: 16px;">
                        Authentic premium {{ strtolower($cat->name) }} sourced directly from top Indian producers.
                    </p>

                    @if($cat->children && $cat->children->count() > 0)
                        <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 18px;">
                            @foreach($cat->children as $child)
                                <a href="{{ route('products.index', ['category' => $child->slug]) }}" style="font-size: 0.78rem; background: var(--cream); color: var(--maroon); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--cream-dark); font-weight: 500; text-decoration: none;">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="btn btn-outline btn-block btn-sm" style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <span>Browse {{ $cat->name }}</span> <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
