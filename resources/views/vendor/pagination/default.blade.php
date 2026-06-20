@if ($paginator->hasPages())
<nav style="display:flex;justify-content:center;align-items:center;padding:1.25rem;gap:4px;flex-wrap:wrap;" aria-label="Pagination">

    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span style="display:flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;background:#fff;border:1px solid #e2e8f0;color:#94a3b8;opacity:0.5;font-size:0.82rem;font-weight:500;user-select:none;">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="display:flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;background:#fff;border:1px solid #e2e8f0;color:#475569;text-decoration:none;font-size:0.82rem;font-weight:500;transition:all 0.2s;" rel="prev" aria-label="Previous">‹</a>
    @endif

    {{-- Pagination Elements --}}
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <span style="display:flex;align-items:center;justify-content:center;min-width:32px;height:32px;color:#94a3b8;font-size:0.82rem;">{{ $element }}</span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="display:flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;background:#2563eb;border:1px solid #2563eb;color:#fff;font-size:0.82rem;font-weight:600;" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="display:flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;background:#fff;border:1px solid #e2e8f0;color:#475569;text-decoration:none;font-size:0.82rem;font-weight:500;transition:all 0.2s;">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="display:flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;background:#fff;border:1px solid #e2e8f0;color:#475569;text-decoration:none;font-size:0.82rem;font-weight:500;transition:all 0.2s;" rel="next" aria-label="Next">›</a>
    @else
        <span style="display:flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 8px;border-radius:6px;background:#fff;border:1px solid #e2e8f0;color:#94a3b8;opacity:0.5;font-size:0.82rem;font-weight:500;user-select:none;">›</span>
    @endif

    <span style="margin-left:0.75rem;font-size:0.78rem;color:#94a3b8;font-weight:500;">
        Hal {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        ({{ $paginator->total() }} data)
    </span>
</nav>
@endif
