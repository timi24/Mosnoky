@if ($paginator->hasPages())
    <nav class="notifications-pagination" role="navigation" aria-label="Pagination des notifications">
        <p class="notifications-pagination-summary">
            Affichage de {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }} sur {{ $paginator->total() }} notifications
        </p>

        <div class="notifications-pagination-controls">
            @if ($paginator->onFirstPage())
                <span class="notifications-pagination-button is-disabled" aria-disabled="true">← Précédente</span>
            @else
                <a class="notifications-pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Précédente</a>
            @endif

            <span class="notifications-pagination-current">Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a class="notifications-pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next">Suivante →</a>
            @else
                <span class="notifications-pagination-button is-disabled" aria-disabled="true">Suivante →</span>
            @endif
        </div>
    </nav>

    <style>
        .notifications-pagination { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-top: 20px; padding: 14px 16px; background: var(--card, #fff); border: 1px solid var(--line); border-radius: 14px; box-shadow: 0 2px 10px rgba(34, 31, 29, .04); }
        .notifications-pagination-summary { margin: 0; color: var(--muted); font-size: .84rem; }
        .notifications-pagination-controls { display: flex; align-items: center; gap: 9px; }
        .notifications-pagination-button { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: 0 13px; border: 1px solid var(--line); border-radius: 999px; color: var(--ink, #292521); text-decoration: none; font-size: .84rem; font-weight: 700; transition: border-color .2s ease, background .2s ease, color .2s ease; }
        a.notifications-pagination-button:hover { border-color: var(--accent, #b1372f); background: var(--accent, #b1372f); color: #fff; }
        .notifications-pagination-button.is-disabled { color: var(--muted); opacity: .55; }
        .notifications-pagination-current { padding: 0 5px; color: var(--muted); font-size: .82rem; white-space: nowrap; }
        @media (max-width: 640px) { .notifications-pagination { align-items: flex-start; flex-direction: column; } .notifications-pagination-controls { width: 100%; justify-content: space-between; gap: 6px; } .notifications-pagination-button { padding: 0 10px; } }
        @media (prefers-reduced-motion: reduce) { .notifications-pagination-button { transition: none; } }
    </style>
@endif
