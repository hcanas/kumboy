@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row d-flex align-items-stretch mt-3 profile">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header h-100">
                        <div class="d-flex align-items-start p-2">
                            <img class="card-img store-logo" src="{{ asset('storage/stores/logos/'.($store->logo ?? 'placeholder.jpg')) }}" title="Store Logo">
                            <div class="d-flex flex-column justify-content-between ms-2 h-100">
                                <div>
                                    <a href="{{ route('store.products', $store->id) }}" class="h5">{{ $store->name }}</a>
                                    <p class="my-1 small">
                                        {{ $store->contact_number }}
                                    </p>
                                    <p class="my-1 small">
                                        {{ $store->address_line.', '.$store->map_address }}
                                    </p>
                                    @if (Auth::check() AND preg_match('/admin/i', Auth::user()->role))
                                        <p class="my-1 small">
                                            Owned by
                                            <a href="{{ route('user.stores', $store->user_id) }}">{{ $store->user_name }}</a>
                                        </p>
                                    @endif
                                </div>
                                @if ($store->open_until !== null)
                                    <div class="badge bg-success align-self-start">OPEN</div>
                                @else
                                    <div class="badge bg-danger align-self-start">CLOSED</div>
                                @endif
                            </div>
                        </div>
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="#">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Vouchers</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        @yield('profile-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection