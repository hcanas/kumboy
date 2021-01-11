@section('page-title', $user->name.' - Activity Log')

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1 pb-2">
            <h4 class="my-0">Activity Log</h4>
            <form action="{{ route('user.search-activity', $user->id) }}" METHOD="POST">
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

        @if ($activities->isEmpty())
            <div class="alert alert-danger mt-2">No records found.</div>
        @else
            @foreach ($activities AS $activity)
                <p class="mb-1">
                    <span class="small text-secondary">{{ $activity->date_recorded }}  &#8285;</span>
                    {{ $activity->action_taken }}
                </p>
            @endforeach

            @php echo $pagination @endphp
        @endif
    </div>
</div>