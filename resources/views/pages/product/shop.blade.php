@extends('layouts.app')
@section('page-title', 'Products')

@section('content')
    <div class="container">
        <div class="row my-3">
            <div class="col-12 my-0">
                <h3 class="text-center text-lg-start text-black-50 fw-bolder">SHOP</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-3 bg-white p-3">
                @php
                    echo $product_filter;
                @endphp
            </div>
            <div class="col-12 col-lg-9 bg-white p-3">
                @if ($products->isEmpty())
                    <div class="alert alert-danger">No records found.</div>
                @else
                    <div class="row row-cols-3 row-cols-md-4 row-cols-lg-6 gx-1">
                        @foreach ($products AS $product)
                            <div class="col mb-2">
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