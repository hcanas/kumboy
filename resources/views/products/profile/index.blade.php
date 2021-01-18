@extends('layouts.app')
@section('page-title', $product->name)

@section('content')
    <div class="container pt-5">
        <div class="row">
            <div class="col-12 col-lg-6 border border-2 border-light">
                <div id="carouselExampleControls" class="carousel carousel-dark slide" data-bs-ride="carousel" data-bs-interval="false">
                    <div class="carousel-inner">
                        @if (count($product->images) > 0)
                            @for ($i = 0; $i < count($product->images); $i++)
                                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/products/images/original/'.$product->images[$i]->filename) }}" class="d-block img-fluid mx-auto" alt="{{ $product->name }}">
                                </div>
                            @endfor
                        @else
                            <div class="carousel-item active">
                                <img src="{{ asset('storage/products/images/original/'.'placeholder.jpg') }}" class="d-block img-fluid mx-auto" alt="{{ $product->name }}">
                            </div>
                        @endif
                    </div>
                    @if (count($product->images) > 1)
                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-12 col-lg-6 mt-2 mt-lg-0 d-flex flex-column">
                <h4 class="mb-2">{{ $product->name }}</h4>
                <div class="d-flex justify-content-between mb-2">
                    <div class="h1 text-primary my-0">&#8369;{{ number_format($product->price, 2, '.', ',') }}</div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-18">star_border</i>
                        <i class="material-icons fs-18">star_border</i>
                        <i class="material-icons fs-18">star_border</i>
                        <i class="material-icons fs-18">star_border</i>
                        <i class="material-icons fs-18">star_border</i>
                        <span class="text-muted small ms-2">No ratings yet.</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="flex-grow-1">
                        <span class="text-muted small">Sold by</span>
                        <a href="{{ route('store.products', $product->store->id) }}" class="small">{{ $product->store->name }}</a>
                    </div>
                    @if (Auth::check() AND Auth::user()->id === $product->store->user_id)
                        <a href="{{ route('store.edit-product', [$product->store->id, $product->id]) }}" class="btn btn-outline-dark btn-sm d-flex align-items-center me-2">
                            <i class="material-icons fs-18">edit</i>
                            <span class="ms-1">Edit</span>
                        </a>
                    @endif
                    <button class="btn btn-primary btn-sm d-flex align-items-center" id="add_to_cart">
                        <i class="material-icons fs-18">add_shopping_cart</i>
                        <span class="ms-1">Add To Cart</span>
                        <span class="visually-hidden" id="product_id">{{ $product->id }}</span>
                    </button>
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="2">Specifications</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        // for variant comparison
                        $specifications;
                    @endphp
                    @foreach ($product->specifications AS $specification)
                        @php
                            $specifications[$specification->name] = $specification->value;
                        @endphp
                        <tr>
                            <td>{{ $specification->name }}</td>
                            <td>{{ $specification->value }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <h5 class="border-bottom border-secondary">Variants</h5>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <h5 class="border-bottom border-secondary">Ratings</h5>
            </div>
        </div>
    </div>

    <script>
        let btn_add_to_cart = document.getElementById('add_to_cart');
        let product_id = document.getElementById('product_id').textContent;

        if (Cart.getItemIndex(product_id)) {
            disableBtn(btn_add_to_cart);
        } else {
            btn_add_to_cart.addEventListener('click', function (e) {
                e.preventDefault();

                Cart.addItem(product_id, 1);
                disableBtn(btn_add_to_cart);
            });
        }

        function disableBtn(btn) {
            btn.disabled = true;
            btn.classList.replace('btn-primary', 'btn-outline-dark');
            btn.querySelector('i').textContent = 'check';
            btn.querySelector('span').textContent = 'Already in Cart';
        }
    </script>
@endsection