@extends('pages.user.profile')
@section('page-title', $user->name.' - Notifications')

@section('profile-content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1 pb-2">
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
                @php $odd = true; @endphp
                @foreach ($notifications AS $notification)
                    <p class="p-2 mb-1 {{ $odd ? 'bg-light' : '' }}">
                        @switch($notification->data['category'])
                            @case('new_store')
                            @case('update_store')
                            @case('store_transfer')
                                @php
                                    echo str_replace(
                                        $notification->data['ref_no'],
                                        '<a href="'.route($notification->read_at
                                                ? 'user.view-notification'
                                                : 'user.read-notification',
                                                [$user->id, $notification->id])
                                                .'">'
                                                .$notification->data['ref_no']
                                                .'</a>',
                                        $notification->data['message']
                                    );
                                @endphp
                                @break
                        @endswitch
                        <span class="small text-muted">
                            &ndash;
                            {{ $notification->read_at ? 'Read ' : 'Unread ' }}
                            {{ date('M j, Y h:iA', strtotime($notification->read_at ?? $notification->created_at)) }}
                        </span>
                    </p>
                    @php $odd = !$odd; @endphp
                @endforeach

                @php echo $pagination; @endphp
            @endif
        </div>
    </div>
@endsection