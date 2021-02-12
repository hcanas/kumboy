@extends('pages.user.profile')
@section('page-title', $user->name.' - Activity Log')

@section('profile-content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1 pb-2">
                <h4 class="my-0">Activity Log</h4>
                <form action="{{ route('user.search-activity', $user->id) }}" METHOD="POST">
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

            @if ($activities->isEmpty())
                <div class="alert alert-danger mt-2">No records found.</div>
            @else
                @php $odd = true; @endphp
                @foreach ($activities AS $activity)
                    <p class="p-2 mb-1 {{ $odd ? 'bg-light' : '' }}">
                        @switch($activity->category)
                            @case('generic')
                                {{ $activity->action_taken }}
                                @break
                            @case('new_store')
                            @case('update_store')
                            @case('store_transfer')
                                @php
                                    preg_match('/<ref_no>(.+)<\/ref_no>/i', $activity->action_taken, $match);
                                    $ref_no = $match[1];

                                    echo str_replace(
                                        [
                                            '<ref_no>',
                                            '</ref_no>'
                                        ],
                                        [
                                            '<a href="'
                                            .route('user.store-request-details', [$activity->user_id, $ref_no])
                                            .'">',
                                            '</a>'
                                        ],
                                        $activity->action_taken
                                    );
                                @endphp
                                @break
                        @endswitch
                        <span class="small text-muted">&ndash; {{ date('M j, Y h:iA', strtotime($activity->date_recorded)) }}</span>
                    </p>
                    @php $odd = !$odd; @endphp
                @endforeach

                @php echo $pagination @endphp
            @endif
        </div>
    </div>
@endsection