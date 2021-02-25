@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row bg-white my-3">
            <div class="col-12">
                <div class="d-flex flex-column flex-lg-row align-items-start text-center py-3">
                    <img class="card-img mx-auto store-logo" src="{{ asset('storage/stores/logos/'.($store->logo ?? 'placeholder.jpg')) }}" title="Store Logo">
                    <div class="flex-grow-1 d-flex flex-column align-self-center align-self-lg-start mt-3 mt-lg-0">
                        <a href="{{ route('store.products', $store->id) }}" class="h5 mx-auto ms-lg-2">{{ $store->name }}</a>
                        <p class="my-0 small mx-auto ms-lg-2">
                            {{ $store->contact_number }}
                        </p>
                        <p class="my-0 small mx-auto ms-lg-2">
                            {{ $store->address_line.', '.$store->map_address }}
                        </p>
                        @if (Auth::check() AND preg_match('/admin/i', Auth::user()->role))
                            <p class="my-0 small mx-auto ms-lg-2">
                                Owned by
                                <a href="{{ route('user.stores', $store->user_id) }}">{{ $store->user_name }}</a>
                            </p>
                        @endif
                        @if ($store->open_until !== null)
                            <div class="badge bg-success align-self-start mx-auto ms-lg-2 my-1">OPEN</div>
                        @else
                            <div class="badge bg-danger align-self-start mx-auto ms-lg-2 my-1">CLOSED</div>
                        @endif
                    </div>
                    <div class="row mx-auto mt-3 mt-lg-0 g-1">
                        <div class="col">
                            <a href="{{ route('store.products', $store->id) }}" class="btn btn-primary btn-sm">PRODUCTS</a>
                        </div>
                        <div class="col">
                            <a href="{{ route('store.vouchers', $store->id) }}" class="btn btn-primary btn-sm">VOUCHERS</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row bg-white">
            <div class="col-12">
                @yield('profile-content')
            </div>
        </div>
    </div>
@endsection