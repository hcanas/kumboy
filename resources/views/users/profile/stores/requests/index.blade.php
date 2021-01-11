@section('page-title', $user->name.' - Requests')

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1w pb-2">
            <h4 class="my-0">Requests</h4>
            <form action="{{ route('user.search-store-request', $user->id) }}" METHOD="POST">
                @csrf
                <div class="input-group">
                    <input type="search" name="keyword" class="form-control form-control-sm" placeholder="Search keyword...">
                    <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center">
                        <i class="material-icons d-md-none">search</i>
                        <span class="d-none d-sm-inline">Search</span>
                    </button>
                </div>
            </form>
        </div>

        @if ($requests->isEmpty())
            <div class="alert alert-danger mt-2">No records found.</div>
        @else
            @foreach ($requests AS $request)
                <p class="mb-1">
                    <span class="small text-secondary">{{ $request->created_at }}</span>
                    &#8231;
                    {{ ucwords(str_replace('_', ' ', $request->type)) }}
                    &#8231;
                    <a href="{{ route('user.store-request-details', [$request->user_id, $request->code]) }}">{{ $request->code }}</a>
                    &#8231;
                    <span class="small">
                        {{ ucwords($request->status) }}
                        
                        @if (in_array($request['status'], ['approved', 'rejected']) AND preg_match('/admin/i', Auth::user()->role))
                            by
                            <a href="{{ route('user.activity-log', $request['evaluated_by']) }}">{{ $request['evaluator_name'] }}</a>
                        @endif
                    </span>
                </p>
            @endforeach

            @include('shared.pagination', [
                'item_start' => $item_start,
                'item_end' => $item_end,
                'total_count' => $total_count,
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'items_per_page' => $items_per_page,
                'url' => route('user.store-requests', $request['user_id']),
            ])
        @endif
    </div>
</div>