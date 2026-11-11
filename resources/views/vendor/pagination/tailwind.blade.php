@if ($paginator->hasPages())
    <nav class="mosnoky-pagination" role="navigation" aria-label="Pagination">
        <p class="mosnoky-pagination-summary">
            Affichage de {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }} sur {{ $paginator->total() }} éléments
        </p>

        <div class="mosnoky-pagination-controls">
            @if ($paginator->onFirstPage())
                <span class="mosnoky-pagination-button is-disabled" aria-disabled="true">← Précédente</span>
            @else
                <a class="mosnoky-pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Précédente</a>
            @endif

            <div class="mosnoky-pagination-pages">
                <span class="mosnoky-pagination-current">Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}</span>
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="mosnoky-pagination-ellipsis" aria-hidden="true">{{ $element }}</span>
                    @elseif (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="mosnoky-pagination-page is-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="mosnoky-pagination-page" href="{{ $url }}" aria-label="Aller à la page {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            @if ($paginator->hasMorePages())
                <a class="mosnoky-pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivante →</a>
            @else
                <span class="mosnoky-pagination-button is-disabled" aria-disabled="true">Suivante →</span>
            @endif
        </div>
    </nav>

    <style>
        .mosnoky-pagination { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 20px; padding: 14px 16px; background: var(--card, #fff); border: 1px solid var(--line, #ece4d8); border-radius: 14px; box-shadow: 0 2px 10px rgba(34, 31, 29, .04); }
        .mosnoky-pagination-summary { margin: 0; color: var(--muted, #857b72); font-size: .84rem; }
        .mosnoky-pagination-controls, .mosnoky-pagination-pages { display: flex; align-items: center; gap: 7px; }
        .mosnoky-pagination-button, .mosnoky-pagination-page { display: inline-flex; align-items: center; justify-content: center; min-width: 38px; min-height: 38px; padding: 0 11px; border: 1px solid var(--line, #ece4d8); border-radius: 999px; color: var(--ink, #292521); text-decoration: none; font-size: .84rem; font-weight: 700; transition: border-color .2s ease, background .2s ease, color .2s ease; }
        a.mosnoky-pagination-button:hover, a.mosnoky-pagination-page:hover { border-color: var(--accent, #b1372f); background: var(--accent, #b1372f); color: #fff; }
        .mosnoky-pagination-page.is-current { border-color: var(--accent, #b1372f); background: var(--accent, #b1372f); color: #fff; }
        .mosnoky-pagination-current { padding: 0 4px; color: var(--muted, #857b72); font-size: .82rem; white-space: nowrap; }
        .mosnoky-pagination-button.is-disabled { color: var(--muted, #857b72); opacity: .55; }
        .mosnoky-pagination-ellipsis { padding: 0 2px; color: var(--muted, #857b72); }
        @media (max-width: 760px) { .mosnoky-pagination { align-items: flex-start; flex-direction: column; } .mosnoky-pagination-controls { width: 100%; justify-content: space-between; gap: 5px; } .mosnoky-pagination-pages { gap: 4px; } .mosnoky-pagination-button, .mosnoky-pagination-page { min-width: 34px; min-height: 36px; padding: 0 8px; } }
        @media (prefers-reduced-motion: reduce) { .mosnoky-pagination-button, .mosnoky-pagination-page { transition: none; } }
    </style>
@endif
