@extends('pages.user.profile')
@section('page-title', $user->name.' - Requests')

@section('profile-content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1 pb-2">
                <h4 class="my-0">Requests</h4>
                <form action="{{ route('user.search-store-request', $user->id) }}" METHOD="POST">
                    @csrf
                    <div class="input-group">
                        <input type="search" name="keyword" class="form-control form-control-sm" value="{{ $keyword ?? '' }}" placeholder="Search keyword...">
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
                @php
                    $odd = true;
                    $badge = [
                        'pending' => 'secondary',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'dark',
                    ];
                @endphp
                @foreach ($requests AS $request)
                    <p class="p-2 mb-1 {{ $odd ? 'bg-light' : '' }}">
                        <span>
                            {{ ucfirst(str_replace('_', ' ', $request->category)) }}
                            application with reference number
                            <a href="{{ route('user.store-request-details', [$request->user_id, $request->ref_no]) }}">
                                {{ $request->ref_no }}
                            </a>
                        </span>
                        <span class="mx-1 badge bg-{{ $badge[$request->status] }}">{{ ucwords($request->status) }}</span>
                        <span class="small">&ndash;</span>
                        <span class="text-muted small">
                            {{ date('M j, Y h:iA', strtotime($request->updated_at)) }}
                        </span>
                    </p>
                    @php $odd = !$odd; @endphp
                @endforeach

                @php echo $pagination; @endphp
            @endif
        </div>
    </div>
@endsection