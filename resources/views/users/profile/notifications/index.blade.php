@section('page-title', $user->name.' - Notifications')

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1w pb-2">
            <h4 class="my-0">Notifications</h4>
            <form action="{{ route('user.search-notification', $user->id) }}" METHOD="POST">
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
        @if ($notifications->isEmpty())
            <div class="alert alert-danger mt-2">No records found.</div>
        @else
            <div class="text-secondary mb-2">
{{--                {{ 'Displaying '.$item_start.'-'.$item_end.' of '.$total_count.'.' }}--}}
            </div>

            @if ($notifications->whereNull('read_at')->count() > 0)
                <div class="bg-light px-2">
                    @foreach ($notifications AS $notification)
                        @if ($notification->read_at === null)
                            <p class="mb-1">
                                <span class="small text-secondary">{{ date('Y-m-d H:i:s', strtotime($notification->created_at)) }}</span>
                                &#8285;
                                {{ ucfirst($notification->data['message']) }}
                                @if ($notification->data['type'] === 'store_request')
                                    <a class="small" href="{{ route('user.read-notification', [Auth::user()->id, $notification->id]) }}">{{ $notification->data['code'] }}</a>
                                @elseif ($notification->data['type'] === 'store_received')
                                    <a class="small" href="{{ route('user.read-notification', [Auth::user()->id, $notification->id]) }}">View Store</a>
                                @endif
                            </p>
                        @else
                            @break;
                        @endif
                    @endforeach
                </div>
            @endif
            @if ($notifications->whereNotNull('read_at')->count() > 0)
                <div class="px-2">
                    @foreach ($notifications AS $notification)
                        @if ($notification->read_at !== null)
                            <p class="mb-1">
                                <span class="small text-secondary">{{ date('Y-m-d H:i:s', strtotime($notification->created_at)) }}</span>
                                &#8285;
                                {{ ucfirst($notification->data['message']) }}
                                @if ($notification->data['type'] === 'store_request')
                                    <a class="small" href="{{ route('user.view-notification', [Auth::user()->id, $notification->id]) }}">{{ $notification->data['code'] }}</a>
                                @elseif ($notification->data['type'] === 'store_received')
                                    <a class="small" href="{{ route('user.view-notification', [Auth::user()->id, $notification->id]) }}">View Store</a>
                                @endif
                            </p>
                        @endif
                    @endforeach
                </div>
            @endif

            @include('shared.pagination', [
                'item_start' => $item_start,
                'item_end' => $item_end,
                'total_count' => $total_count,
                'current_page' => $current_page,
                'total_pages' => $total_pages,
                'items_per_page' => $items_per_page,
                'url' => route('user.notifications', $user->id),
            ])
        @endif
    </div>
</div>