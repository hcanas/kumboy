@extends('layouts.app')
@section('page-title', 'Products')

@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="col-12">
                <div class="d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-end border-bottom my-2">
                    <h4>Shop</h4>
                    <div class="my-2">
                        @php
                            echo $product_filter;
                        @endphp
                    </div>
                </div>
                @if ($products->isEmpty())
                    <div class="alert alert-danger">No records found.</div>
                @else
                    <div class="row row-cols-3 row-cols-md-4 row-cols-lg-auto gx-1 gx-lg-2 gy-2 g-lg-2 px-lg-2 px-xxl-3">
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