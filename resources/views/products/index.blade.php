@extends('layouts.app')
@section('page-title', 'Products')

@section('content')
    <div class="container mt-3">
        <h4 class="border-bottom border-2 border-secondary my-0 py-2">Shop</h4>
        <div class="row mt-3">
            <div class="col-12 col-md-4 col-xl-3">
                <form action="{{ route('shop.search') }}" method="POST">
                    @csrf

                    <div class="mb-2">
                        <input type="search" name="keyword" class="form-control form-control-sm" value="{{ $filters['keyword'] ?? '' }}" placeholder="Search keyword...">
                    </div>

                    <div class="mb-2">
                        <label>Categories</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="all|all"
                                    {{ (isset($filters['main_category'], $filters['sub_category']) AND $filters['main_category'] === 'all' AND $filters['sub_category'] === 'all') ? 'selected' : '' }}
                            >All Products</option>
                            @foreach ($product_categories AS $key => $value)
                                @php $category = $key.'|all'; @endphp
                                <option value="{{ $category }}"
                                        {{ (isset($filters['main_category'], $filters['sub_category']) AND $filters['main_category'] === $key AND $filters['sub_category'] === 'all') ? 'selected' : '' }}
                                >{{ ucwords($key).' - All' }}</option>

                                @if (empty($value) === false AND is_array($value))
                                    @foreach ($value AS $sub_value => $placeholder)
                                        @php $category = $key.'|'.$sub_value; @endphp
                                        <option value="{{ $category }}"
                                                {{ (isset($filters['main_category'], $filters['sub_category']) AND $filters['main_category'] === $key AND $filters['sub_category'] === $sub_value) ? 'selected' : '' }}
                                        >{{ ucwords($key.' - '.$sub_value) }}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Price</label>
                        <div class="d-flex align-items-center">
                            <input type="number" name="price_from" class="form-control form-control-sm" value="{{ $filters['price_from'] ?? 0 }}" min="0" max="1000000" step="0.01">
                            <span class="mx-2">to</span>
                            <input type="number" name="price_to" class="form-control form-control-sm" value="{{ $filters['price_to'] ?? 1000000 }}" min="0" max="1000000" step="0.01">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label>Sort By</label>
                        <select name="sort" class="form-select form-select-sm">
                            <option value="best sellers" {{ (isset($filters['sort_by']) AND $filters['sort_by'] === 'best sellers') ? 'selected' : '' }}>Best Sellers</option>
                            <option value="name|asc" {{ (isset($filters['sort_by'], $filters['sort_dir']) AND $filters['sort_by'] === 'name' AND $filters['sort_dir'] === 'asc') ? 'selected' : '' }}>Name A-Z</option>
                            <option value="name|desc" {{ (isset($filters['sort_by'], $filters['sort_dir']) AND $filters['sort_by'] === 'name' AND $filters['sort_dir'] === 'desc') ? 'selected' : '' }}>Name Z-A</option>
                            <option value="price|desc" {{ (isset($filters['sort_by'], $filters['sort_dir']) AND $filters['sort_by'] === 'price' AND $filters['sort_dir'] === 'desc') ? 'selected' : '' }}>Highest Price</option>
                            <option value="price|asc" {{ (isset($filters['sort_by'], $filters['sort_dir']) AND $filters['sort_by'] === 'price' AND $filters['sort_dir'] === 'asc') ? 'selected' : '' }}>Lowest Price</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col d-grid d-block">
                            <a href="{{ route('shop') }}" class="btn btn-outline-dark btn-sm">Reset</a>
                        </div>
                        <div class="col d-grid d-block">
                            <button type="submit" class="btn btn-primary btn-sm">Apply Filter</button>
                        </div>
                    </div>
                </form>
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
                    @php echo $pagination @endphp
                @endif
            </div>
        </div>
    </div>
@endsection