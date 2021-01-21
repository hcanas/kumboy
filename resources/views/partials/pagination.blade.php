<nav class="my-3 d-flex justify-content-between align-items-center">
    <p class="small text-muted">
        {{ $item_start.'-'.$item_end.' of '.$total_count }}
    </p>
    @if ($total_pages > 1)
        <ul class="pagination justify-content-center">
            @php
                $keyword = empty($keyword) ? null : $keyword;
            @endphp

            @if ($current_page > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ route($route_name, array_merge($route_params, ['current_page' => $current_page - 1, 'keyword' => $keyword]))  }}">&lt;</a>
                </li>
            @endif

            <li class="page-item disabled">
                <a class="page-link" href="#">{{ $current_page }}</a>
            </li>

            @if ($current_page < $total_pages)
                <li class="page-item">
                    <a class="page-link" href="{{ route($route_name, array_merge($route_params, ['current_page' => $current_page + 1, 'keyword' => $keyword]))  }}">&gt;</a>
                </li>
            @endif
        </ul>
    @endif
</nav>