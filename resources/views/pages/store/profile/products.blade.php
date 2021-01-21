@extends('pages.store.profile')
@section('page-title', $store->name.' - Products')

@section('profile-content')
    <div class="row">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center border-bottom border-2 border-secondary p-2 mb-2">
                <h4 class="my-0">Products</h4>
                @if (Auth::check() AND Auth::user()->id === $store->user_id)
                    <a href="{{ route('store.add-product', $store->id) }}" class="btn btn-primary btn-sm">Add Product</a>
                @endif
            </div>

            @if (session('message_type'))
                <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
            @endif

            <div class="row">
                <div class="col-12 col-md-4 col-xl-3">
                    @php
                        echo $product_filter;
                    @endphp
                </div>
                <div class="col-12 col-md-8 col-xl-9 mt-3 mt-md-0">
                    @if ($products->isEmpty())
                        <div class="alert alert-danger mt-2">No records found.</div>
                    @else
                        <div class="row row-cols-3 g-2 gx-lg-4 gx-xl-2 row-cols-sm-4 row-cols-md-3 row-cols-lg-4 row-cols-xl-6">
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
        </div>
    </div>

    <script type="text/javascript">
        var cards = document.getElementsByClassName('product-listing');
    </script>
@endsection