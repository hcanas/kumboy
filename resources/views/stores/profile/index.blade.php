@extends('layouts.app')

@section('content')
    <div class="container mt-2 mt-md-5">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header d-flex align-items-start p-2">
                        <img class="card-img store-logo" src="{{ asset('storage/stores/logos/'.($store->logo ?? 'placeholder.jpg')) }}" title="Store Logo">
                        <div class="ms-2">
                            <a href="{{ route('store.products', $store->id) }}" class="h5">{{ $store->name }}</a>
                            <p class="my-1 small">
                                {{ $store->contact_number }}
                            </p>
                            <p class="my-1 small">
                                {{ $store->address.', '.$store->map_address }}
                            </p>
                            @if (Auth::check() AND preg_match('/admin/i', Auth::user()->role))
                                <p class="my-1 small">
                                    Owned by
                                    <a href="{{ route('user.stores', $store->user_id) }}">{{ $store->user_name }}</a>
                                </p>
                            @endif
                            @if ($store->open_until !== null)
                                <p class="text-success fw-bold my-1 small">OPEN</p>
                            @else
                                <p class="text-danger fw-bold my-1 small">CLOSED</p>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @yield('profile-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection