@if ($paginator->hasPages())
    <nav aria-label="Pagination Navigation">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class="fas fa-chevron-left" style="font-size:10px;"></i></span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        <i class="fas fa-chevron-left" style="font-size:10px;"></i>
                    </a>
                </li>
            @endif

            {{-- Page Number Links --}}
            @if (method_exists($paginator, 'lastPage'))
                @php
                    $current = $paginator->currentPage();
                    $last    = $paginator->lastPage();
                    $start   = max(1, $current - 2);
                    $end     = min($last, $current + 2);
                @endphp

                {{-- First page + ellipsis --}}
                @if ($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                    </li>
                    @if ($start > 2)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @endif

                @for ($page = $start; $page <= $end; $page++)
                    @if ($page == $current)
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Last page + ellipsis --}}
                @if ($end < $last)
                    @if ($end < $last - 1)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($last) }}">{{ $last }}</a>
                    </li>
                @endif
            @else
                {{-- SimplePaginator: no page count available --}}
                <li class="page-item page-info disabled">
                    <span class="page-link" style="font-size:12px;">Page {{ $paginator->currentPage() }}</span>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        <i class="fas fa-chevron-right" style="font-size:10px;"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
                </li>
            @endif
        </ul>
    </nav>
@endif
