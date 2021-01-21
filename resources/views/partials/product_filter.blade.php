<form action="{{ $url }}" method="POST">
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
            <option value="sold|desc" {{ (isset($filters['sort_by'], $filters['sort_dir']) AND $filters['sort_by'] === 'sold' AND $filters['sort_dir'] === 'desc') ? 'selected' : '' }}>Best Sellers</option>
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