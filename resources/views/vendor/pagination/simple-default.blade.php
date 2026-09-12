@if ($paginator->hasPages())
    <nav class="pagination-nav" aria-label="Pagination Navigation">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class="fas fa-chevron-left" style="font-size:11px;"></i> Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left" style="font-size:11px;"></i> Previous
                    </a>
                </li>
            @endif

            {{-- Current Page indicator --}}
            <li class="page-item page-info">
                <span class="page-link page-indicator">
                    @if (method_exists($paginator, 'lastPage'))
                        Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
                    @else
                        Page {{ $paginator->currentPage() }}
                    @endif
                </span>
            </li>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        Next <i class="fas fa-chevron-right" style="font-size:11px;"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Next <i class="fas fa-chevron-right" style="font-size:11px;"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif

