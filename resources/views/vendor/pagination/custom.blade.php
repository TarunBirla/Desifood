@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: 30px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 10px 16px; border-radius: 12px; background: var(--cream); color: var(--muted); border: 1px solid var(--cream-dark); font-size: 0.88rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-chevron-left"></i> Previous
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding: 10px 16px; border-radius: 12px; background: var(--white); color: var(--maroon); border: 1px solid var(--cream-dark); font-size: 0.88rem; font-weight: 600; text-decoration: none; box-shadow: var(--shadow-sm); transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-chevron-left"></i> Previous
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="padding: 10px 14px; font-size: 0.88rem; color: var(--muted); font-weight: 600;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 10px 16px; border-radius: 12px; background: var(--maroon); color: var(--white); font-size: 0.88rem; font-weight: 700; box-shadow: 0 4px 12px rgba(137,15,20,0.25); border: 1px solid var(--maroon);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="padding: 10px 16px; border-radius: 12px; background: var(--white); color: var(--charcoal); border: 1px solid var(--cream-dark); font-size: 0.88rem; font-weight: 600; text-decoration: none; box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding: 10px 16px; border-radius: 12px; background: var(--white); color: var(--maroon); border: 1px solid var(--cream-dark); font-size: 0.88rem; font-weight: 600; text-decoration: none; box-shadow: var(--shadow-sm); transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;">
                Next <i class="fa-solid fa-chevron-right"></i>
            </a>
        @else
            <span style="padding: 10px 16px; border-radius: 12px; background: var(--cream); color: var(--muted); border: 1px solid var(--cream-dark); font-size: 0.88rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 6px;">
                Next <i class="fa-solid fa-chevron-right"></i>
            </span>
        @endif
    </nav>
@endif
