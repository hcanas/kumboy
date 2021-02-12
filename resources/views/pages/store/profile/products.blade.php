@extends('pages.store.profile')
@section('page-title', $store->name.' - Products')

@section('profile-content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-end border-bottom mb-2">
                @if (Auth::check() AND Auth::user()->id === $store->user_id)
                    <a href="{{ route('store.add-product', $store->id) }}" class="btn btn-primary btn-sm my-2">Add Product</a>
                @endif
                <div class="my-2">
                    @php
                        echo $product_filter;
                    @endphp
                </div>
            </div>

            @if (session('message_type'))
                <div class="alert alert-{{ session('message_type') }} small">{{ session('message_content') }}</div>
            @endif

            @if ($products->isEmpty())
                <div class="alert alert-danger mt-2">No records found.</div>
            @else
                <div class="row row-cols-3 row-cols-md-4 row-cols-lg-auto gx-1 gx-lg-2 gy-2 g-lg-2">
                    @foreach ($products AS $product)
                        <div class="col">
                            <a href="{{ route('product.info', $product->id) }}" class="card-link-wrapper">
                                <div class="card product-listing">
                                    <img src="{{ asset('storage/products/images/preview/'.(file_exists('storage/products/images/preview/'.($product->preview ?? 'none')) ? $product->preview : 'placeholder.jpg')) }}" class="card-img-top">
                                    <div class="card-body p-2">
                                        <p class="mb-1 small ellipsis">{{ $product->name }}</p>
                                        <p class="mb-1 text-primary">&#8369;{{ number_format($product->price, 2, '.', ',') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                @php echo $pagination; @endphp
            @endif
        </div>
    </div>
@endsection