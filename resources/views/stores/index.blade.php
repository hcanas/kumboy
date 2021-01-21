@extends('layouts.app')
@section('page-title', 'Stores')

@section('content')
    <div class="container">
        <div class="row mt-2 mt-md-5">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center border-bottom mb-2 pb-2">
                    <h4 class="my-0">Stores</h4>
                    <form action="{{ route('store.search') }}" METHOD="POST">
                        @csrf
                        <div class="input-group">
                            <input type="search" name="keyword" class="form-control form-control-sm" value="{{ $keyword ?? null }}" placeholder="Search keyword...">
                            <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        </div>
                    </form>
                </div>
                @if ($stores->isEmpty())
                    <div class="alert alert-danger small">No Records Found</div>
                @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($stores AS $store)
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-header p-2 d-flex align-items-start h-100">
                                        <div>
                                            <img class="card-img store-logo" src="{{ asset('storage/stores/logos/'.($store->logo ?? 'placeholder.jpg')) }}" title="Store Logo">
                                        </div>
                                        <div class="ms-2">
                                            <a href="{{ route('store.products', $store->id) }}" class="h6">{{ $store->name }}</a>
                                            <p class="my-1 small">
                                                {{ $store->contact_number }}
                                            </p>
                                            <p class="my-1 small">
                                                {{ $store->address.', '.$store->map_address }}
                                            </p>
                                            @if ($store->open_until !== null)
                                                <p class="small text-success my-1">OPEN</p>
                                            @else
                                                <p class="small text-danger my-1">CLOSED</p>
                                            @endif
                                            @if (Auth::check() AND preg_match('/admin/i', Auth::user()->role))
                                                <p class="small">
                                                    Owned by
                                                    <a href="{{ route('user.stores', $store->user_id) }}">{{ $store->user_name }}</a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @php echo $pagination; @endphp
                @endif
            </div>
        </div>
    </div>
@endsection