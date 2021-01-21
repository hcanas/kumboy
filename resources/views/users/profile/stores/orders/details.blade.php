@extends('users.profile.index')
@section('page-title', 'Order Details - '.$order->tracking_number)

@section('profile-content')
    <div class="d-flex flex-column">
        @if (session('message_type'))
            <div class="alert alert-danger small">{{ session('message_content') }}</div>
        @endif

        @php
            $order->total = 0;
            $order->total_qty = 0;
        @endphp

        <h5 class="border-bottom mt-2">{{ $order->tracking_number }}</h5>
        @foreach ($order->items AS $item)
            @php
                $order->total += ($item->qty * $item->price);
                $order->total_qty += $item->qty;
            @endphp
            <div class="row mt-2 items" id="item_template">
                <div class="col-12 col-lg-6 d-flex align-items-center mb-2">
                    @php
                        $preview = file_exists('storage/products/images/thumbnail/'.($item->product->preview ?? 'none'))
                            ? $item->product->preview
                            : 'placeholder.jpg';
                    @endphp
                    <img src="{{ asset('storage/products/images/thumbnail/'.$preview) }}">
                    <div class="ms-1 ellipsis">
                        <a href="{{ route('product.info', $item->product_id) }}" class="h6">{{ $item->name }}</a>
                        <p class="text-secondary small my-0 ellipsis">{{ $item->specifications }}</p>
                    </div>
                </div>
                <div class="col-12 col-lg-6 mb-2">
                    <div class="row d-flex justify-content-between">
                        <div class="col">
                            <span class="item_qty">Qty: {{ number_format($item->qty) }}</span>
                            <span class="text-secondary small">({{ number_format($item->product->qty).' remaining' }})</span>
                        </div>
                        <div class="col">
                            <div class="h6 text-primary text-center my-0">&#8369;{{ number_format($item->qty * $item->price, 2) }}</div>
                            <div class="text-secondary text-center small">(&#8369;{{ number_format($item->price, 2) }})</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <h5 class="border-bottom mt-2">Address</h5>
        <div>{{ $order->contact_person }}</div>
        <div>{{ $order->contact_number }}</div>
        <div>{{ $order->address.', '.$order->map_address }}</div>
        <div>{{ $order->map_coordinates }}</div>

        <h5 class="border-bottom mt-2">Payment</h5>
        <div class="d-flex flex-column mt-2">
            <div class="d-flex justify-content-between align-items-center">
                <span>{{ $order->total_qty }} Items</span>
                <span class="h6 text-primary">&#8369;{{ number_format($order->total, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span>Delivery Fee</span>
                <span class="h6 text-primary">&#8369;{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center border-top py-2 my-2">
                <span>Total</span>
                <span class="h6 text-primary">&#8369;{{ number_format($order->total + $order->delivery_fee, 2) }}</span>
            </div>
        </div>
    </div>
@endsection