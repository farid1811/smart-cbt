@if ($paginator->hasPages())
<nav style="display:flex;justify-content:center;padding:1rem;gap:4px;" aria-label="Pagination">

    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <span style="display:flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:6px;background:var(--surface,#fff);border:1px solid var(--border,#e2e8f0);color:var(--text-muted,#64748b);opacity:0.4;font-size:0.82rem;font-weight:500;">← Sebelumnya</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="display:flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:6px;background:var(--surface,#fff);border:1px solid var(--border,#e2e8f0);color:var(--text,#0f172a);text-decoration:none;font-size:0.82rem;font-weight:500;transition:all 0.2s;" rel="prev">← Sebelumnya</a>
    @endif

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="display:flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:6px;background:var(--surface,#fff);border:1px solid var(--border,#e2e8f0);color:var(--text,#0f172a);text-decoration:none;font-size:0.82rem;font-weight:500;transition:all 0.2s;" rel="next">Berikutnya →</a>
    @else
        <span style="display:flex;align-items:center;justify-content:center;height:32px;padding:0 12px;border-radius:6px;background:var(--surface,#fff);border:1px solid var(--border,#e2e8f0);color:var(--text-muted,#64748b);opacity:0.4;font-size:0.82rem;font-weight:500;">Berikutnya →</span>
    @endif
</nav>
@endif
