@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 24px;">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span style="padding: 8px 14px; border-radius: 8px; background: var(--cream, #f8f9fa); color: #888888; border: 1px solid var(--line, #e2e8f0); font-size: 0.85rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i> Prev
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: #ffffff; color: var(--maroon, #890F14); border: 1px solid var(--line, #e2e8f0); font-size: 0.85rem; font-weight: 600; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i> Prev
            </a>
        @endif

        {{-- Pagination Elements --}}
        <div style="display: flex; align-items: center; gap: 4px; flex-wrap: wrap;">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="padding: 8px 12px; font-size: 0.85rem; color: #888888; font-weight: 600;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 8px 14px; border-radius: 8px; background: var(--maroon, #890F14); color: #ffffff; font-size: 0.85rem; font-weight: 700; box-shadow: 0 2px 6px rgba(137,15,20,0.3); border: 1px solid var(--maroon, #890F14);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="padding: 8px 14px; border-radius: 8px; background: #ffffff; color: #333333; border: 1px solid var(--line, #e2e8f0); font-size: 0.85rem; font-weight: 600; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.2s ease;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding: 8px 14px; border-radius: 8px; background: #ffffff; color: var(--maroon, #890F14); border: 1px solid var(--line, #e2e8f0); font-size: 0.85rem; font-weight: 600; text-decoration: none; box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px;">
                Next <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
            </a>
        @else
            <span style="padding: 8px 14px; border-radius: 8px; background: var(--cream, #f8f9fa); color: #888888; border: 1px solid var(--line, #e2e8f0); font-size: 0.85rem; font-weight: 600; cursor: not-allowed; display: inline-flex; align-items: center; gap: 6px;">
                Next <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
            </span>
        @endif
    </nav>
@endif
