@extends('layouts.app')
@section('page-title', 'Users')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8 offset-md-2">
                <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-1 pb-2">
                    <h4 class="my-0">Users</h4>
                    <form action="{{ route('user.search') }}" METHOD="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" name="keyword" class="form-control form-control-sm" value="{{ $keyword ?? '' }}" placeholder="Search keyword...">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </div>
                    </form>
                </div>
                @if ($users->isEmpty())
                    <div class="alert alert-danger mt-3">No records found.</div>
                @else
                    @if ($users->isNotEmpty())
                        @php $odd = true; @endphp
                        @foreach ($users AS $user)
                            <div class="d-flex justify-content-between p-2 {{ $odd ? 'bg-light' : '' }}">
                                <div>
                                    <a href="{{ route('user.activity-log', $user->id) }}" class="h6">{{ $user->name }}</a> <br>
                                    <span class="small text-muted">{{ ucwords($user->role) }}</span>
                                </div>
                                <div class="text-end">
                                    <span class="small">
                                        @php $pos = strpos($user->email, '@'); @endphp
                                        {{ substr_replace($user->email, str_repeat('*', $pos - 4), 4, $pos - 4) }}
                                    </span> <br>
                                    <span class="small text-{{ $user->banned_until === null ? 'success' : 'danger' }}">
                                        @if ($user->banned_until === null)
                                            Active
                                        @else
                                            Banned
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @php $odd = !$odd; @endphp
                        @endforeach
                    @endif

                    @php echo $pagination; @endphp
                @endif
            </div>
        </div>
    </div>
@endsection