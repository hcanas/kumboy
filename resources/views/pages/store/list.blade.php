@extends('layouts.app')
@section('page-title', 'Stores')

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center border-bottom my-2 py-2">
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
                    <div class="alert alert-danger small">No stores found.</div>
                @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2 gx-lg-4 gx-xl-2">
                        @foreach ($stores AS $store)
                            <div class="col">
                                <div class="card h-100">
                                    <div class="card-header p-2 d-flex align-items-start h-100">
                                        <div>
                                            <img class="card-img store-logo" src="{{ asset('storage/stores/logos/'.($store->logo ?? 'placeholder.jpg')) }}" title="Store Logo">
                                        </div>
                                        <div class="d-flex flex-column justify-content-between ms-2 h-100">
                                            <div>
                                                <a href="{{ route('store.products', $store->id) }}" class="h6">{{ $store->name }}</a>
                                                <p class="my-1 small">
                                                    {{ $store->contact_number }}
                                                </p>
                                                <p class="my-1 small">
                                                    {{ $store->address.', '.$store->map_address }}
                                                </p>
                                                @if (Auth::check() AND preg_match('/admin/i', Auth::user()->role))
                                                    <p class="small">
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