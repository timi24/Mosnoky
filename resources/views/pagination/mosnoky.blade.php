@if ($paginator->hasPages())
    <nav class="pager" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="pager-btn disabled" aria-disabled="true">‹ Précédent</span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Précédent</a>
        @endif

        <div class="pager-pages">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pager-dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pager-btn active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pager-btn" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivant ›</a>
        @else
            <span class="pager-btn disabled" aria-disabled="true">Suivant ›</span>
        @endif

        <p class="pager-info">{{ $paginator->firstItem() }} à {{ $paginator->lastItem() }} sur {{ $paginator->total() }}</p>
    </nav>

    <style>
        .pager { display: flex; align-items: center; justify-content: center; flex-wrap: wrap; gap: 8px; margin: 8px 0; font-size: .88rem; }
        .pager-pages { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; justify-content: center; }
        .pager-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px; padding: 0 14px; border: 1px solid var(--line); border-radius: 999px; background: var(--card, #fff); color: var(--ink, #221f1d); font-weight: 700; line-height: 1; transition: .2s ease; }
        a.pager-btn:hover { border-color: var(--accent, #b1372f); color: var(--accent, #b1372f); }
        .pager-btn.active { background: var(--accent, #b1372f); border-color: var(--accent, #b1372f); color: #fff; }
        .pager-btn.disabled { opacity: .4; cursor: default; }
        .pager-dots { color: var(--muted, #857b72); padding: 0 4px; }
        .pager-info { flex-basis: 100%; margin: 4px 0 0; text-align: center; color: var(--muted, #857b72); font-size: .8rem; }
    </style>
@endif
