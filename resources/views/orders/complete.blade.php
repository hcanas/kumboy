@extends('layouts.app')
@section('page-title', 'Payment')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 mt-5">
                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2 d-flex align-items-center">
                        <div>
                            <i class="material-icons fs-48 text-primary">shopping_cart</i>
                        </div>
                        <div class="wizard-line bg-primary"></div>
                        <div>
                            <i class="material-icons fs-48 text-primary">house</i>
                        </div>
                        <div class="wizard-line bg-primary"></div>
                        <div>
                            <i class="material-icons fs-48 text-primary">payment</i>
                        </div>
                        <div class="wizard-line bg-primary"></div>
                        <div>
                            <i class="material-icons fs-48 text-primary">check_circle_outline</i>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2 mt-5">
                        <h4 class="text-center">Order has been placed!</h4>
                        <p class="text-center my-0">Tracking Number</p>
                        <p class="h1 text-center text-primary bg-light py-2">{{ $order->tracking_number }}</p>

                        @php
                        $total = 0;
                        foreach ($order->items AS $item) {
                            $total += ($item->qty * $item->price);
                        }
                        @endphp

                        <p class="text-center">Please prepare the exact amount of &#8369;{{ number_format($total, 2, '.', ',') }}</p>
                        <p class="text-center">You can check the status of your order <a href="#">here</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script defer>
        sessionStorage.clear();
        document.querySelectorAll('.cart_item_count').forEach(el => {
            el.textContent = '';
        });
    </script>
@endsection