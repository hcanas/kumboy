@extends('layouts.app')
@section('page-title', 'Requests')

@section('content')
    <div class="container">
        <div class="row my-3">
            <div class="col-12 col-lg-8 offset-lg-2 my-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="text-center text-lg-start text-black-50 fw-bolder">REQUESTS</h3>
                    <form action="{{ route('request.search') }}" METHOD="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" name="keyword" class="form-control form-control-sm" placeholder="Search keyword...">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2 bg-white p-3">
                @if ($requests->isEmpty())
                    <div class="alert alert-danger mt-3">No records found.</div>
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
    </div>
@endsection