@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="pager-btn disabled" aria-disabled="true">‹ Précédent</span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Précédent</a>
        @endif

        @if ($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant ›</a>
        @else
            <span class="pager-btn disabled" aria-disabled="true">Suivant ›</span>
        @endif
    </nav>

    <style>
        .pager { display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 8px; margin: 8px 0; font-size: .88rem; }
        .pager-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 14px; border: 1px solid var(--line); border-radius: 999px; background: var(--card, #fff); color: var(--ink, #221f1d); font-weight: 700; line-height: 1; transition: .2s ease; }
        a.pager-btn:hover { border-color: var(--accent, #b1372f); color: var(--accent, #b1372f); }
        .pager-btn.disabled { opacity: .4; cursor: default; }
    </style>
@endif
