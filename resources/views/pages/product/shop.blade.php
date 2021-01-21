@extends('layouts.app')
@section('page-title', 'Products')

@section('content')
    <div class="container mt-3">
        <h4 class="border-bottom border-2 border-secondary my-0 py-2">Shop</h4>
        <div class="row mt-3">
            <div class="col-12 col-md-4 col-xl-3">
                @php
                    echo $product_filter;
                @endphp
            </div>
            <div class="col-12 col-md-8 col-xl-9 mt-3 mt-md-0">
                @if ($products->isEmpty())
                    <div class="alert alert-danger">No records found.</div>
                @else
                    <div class="row row-cols-3 g-2 gx-lg-4 gx-xl-2 row-cols-sm-4 row-cols-md-3 row-cols-lg-4 row-cols-xl-6">
                        @foreach ($products AS $product)
                            <div class="col">
                                <a href="{{ route('product.info', $product->id) }}" class="card-link-wrapper">
                                    <div class="card product-listing h-100">
                                        <img src="{{ asset('storage/products/images/preview/'.($product->preview ?? 'placeholder.jpg')) }}" class="card-img-top">
                                        <div class="card-body p-2">
                                            <p class="my-1 small ellipsis">{{ $product->name }}</p>
                                            <p class="my-1 text-primary">&#8369;{{ number_format($product->price, 2) }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @php
                        echo $pagination
                    @endphp
                @endif
            </div>
        </div>
    </div>
@endsection