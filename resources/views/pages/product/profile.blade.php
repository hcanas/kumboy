@extends('layouts.app')
@section('page-title', $product->name)

@section('content')
    <div class="container mt-lg-5">
        <div class="row bg-white p-lg-3">
            <div class="col-12 col-lg-6">
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
            <div class="col-12 col-lg-6 mt-3 mt-lg-0 d-flex flex-column">
                <div id="system_message"></div>

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
                        <a href="#" class="btn btn-outline-dark btn-sm d-flex align-items-center me-2" id="edit_product">
                            <i class="material-icons fs-16">edit</i>
                            <span class="ms-1 small">Edit</span>
                        </a>
                    @endif
                    @if ($product->qty > 0)
                        <button class="btn btn-primary btn-sm d-flex align-items-center" id="add_to_cart">
                            <i class="material-icons fs-16">add_shopping_cart</i>
                            <span class="ms-1 small">Add To Cart</span>
                            <span class="visually-hidden" id="product_id">{{ $product->id }}</span>
                        </button>
                    @else
                        <button class="btn btn-outline-dark btn-sm" disabled>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">production_quantity_limits</i>
                                <span class="ms-1 small">Out of Stock</span>
                            </div>
                        </button>
                    @endif
                </div>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Specifications</span>
                                <span class="fw-normal">{{ $product->qty.' items available' }}</span>
                            </div>
                        </th>
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
        <div class="row bg-white mt-lg-3 py-3 p-lg-3">
            <div class="col">
                <h5 class="border-bottom">Variants</h5>
                @if ($product->variants->isEmpty())
                    <div class="text-center text-muted">No variants for this product.</div>
                @else
                    <div class="row row-cols-auto g-2">
                        @foreach ($product->variants AS $variant)
                            <div class="col">
                                <a href="{{ route('product.info', $variant->id) }}" class="card-link-wrapper">
                                    <div class="card product-listing">
                                        <img src="{{ asset('storage/products/images/preview/'.($variant->preview ?? 'placeholder.jpg')) }}" class="card-img-top product_image">
                                        <div class="card-body p-2">
                                            <p class="mb-1 small ellipsis">{{ $variant->name }}</p>
                                            <p class="mb-1 text-primary">{{ $variant->price }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="row bg-white my-lg-3 py-3 p-lg-3">
            <div class="col">
                <h5 class="border-bottom">Ratings</h5>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_product" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title">Product Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <form id="form_product" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">label</i></span>
                                <input type="text" name="name" class="form-control form-control-sm" value="{{ $product->name }}" placeholder="Name">
                            </div>
                            <div class="form-text">If you are inserting a product variant, make sure that the names are identical.</div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">category</i></span>
                                <select name="category" class="form-select form-select-sm">
                                    @foreach (config('system.product_categories') AS $main_cat => $value)
                                        @php $category = $main_cat.'|all'; @endphp
                                        <option value="{{ $category }}"
                                        {{ ($product->sub_category === null AND $category === $product->main_category.'|all') ? 'selected' : '' }}>
                                            {{ ucwords($main_cat).' - All' }}
                                        </option>

                                        @if (empty($value) === false AND is_array($value))
                                            @foreach ($value AS $sub_cat => $placeholder)
                                                @php $category = $main_cat.'|'.$sub_cat; @endphp
                                                <option value="{{ $category }}"
                                                        {{ $category === $product->main_category.'|'.$product->sub_category ? 'selected' : '' }}>
                                                    {{ ucwords($main_cat.' - '.$sub_cat) }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                @php $consolidated = ''; @endphp
                                @foreach ($specifications AS $key => $val)
                                    @php $consolidated .= ' | '.$key.' : '.$val; @endphp
                                @endforeach
                                <span class="input-group-text"><i class="material-icons fs-16">list_alt</i></span>
                                <textarea name="specifications" class="form-control form-control-sm" placeholder="Specifications">{{ ltrim($consolidated, ' | ') }}</textarea>
                            </div>
                            <div class="form-text">
                                Insert product specifications as a <code>name:value</code> pair with a vertical bar <code>|</code> as separator.
                                For example,<br> Color : Red | Size : L | Camera : 14MP Rear, 5MP Front
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">inventory</i></span>
                                <input type="number" name="qty" class="form-control form-control-sm" min="0" step="1" value="{{ $product->qty }}" placeholder="Quantity">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">sell</i></span>
                                <input type="number" name="price" class="form-control form-control-sm" min="0" step="0.01" value="{{ $product->price }}" placeholder="Price">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">camera_alt</i></span>
                                <input type="file" name="images[]" class="form-control-file form-control-sm" accept="image/png,image/jpeg" multiple>
                            </div>
                            <div class="form-text">Recommended size for images is 512x512 in jpg/png format no larger than 500KB.</div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="col-12">
                            <div class="text-center my-1">New Images Preview</div>
                            <div id="new_preview"></div>
                            <div class="text-center mt-5">Old Images Preview</div>
                            <div class="row row-cols-2 row-cols-lg-3" id="old_preview">
                                @foreach ($product->images AS $image)
                                    <div class="col text-center mt-2 mt-lg-0" style="width: 150px;">
                                        <img src="{{ asset('storage/products/images/preview/'.$image->filename) }}">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="deleted_images[{{ $image->id }}]">
                                            <label class="form-check-label small">Delete</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm" id="save_product">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script defer>
        const currency_formatter = new Intl.NumberFormat('en-PH', { style : 'currency', currency : 'PHP' });
        const el_system_message = document.getElementById('system_message');
        const el_modal_product = document.getElementById('modal_product');
        const el_form_message = el_modal_product.querySelector('#form_message');
        const el_form_product = el_modal_product.querySelector('#form_product');
        const el_form_name = el_form_product.querySelector('[name="name"]');
        const el_form_category = el_form_product.querySelector('[name="category"]');
        const el_form_specifications = el_form_product.querySelector('[name="specifications"]');
        const el_form_qty = el_form_product.querySelector('[name="qty"]');
        const el_form_price = el_form_product.querySelector('[name="price"]');
        const el_form_images = el_form_product.querySelector('[name="images[]"]');
        const el_new_images_preview = el_modal_product.querySelector('#new_preview');

        document.getElementById('edit_product').addEventListener('click', e => {
            e.preventDefault();
            const modal = new bootstrap.Modal(el_modal_product, {
                keyboard: false,
                backdrop: 'static',
            });

            modal.show();
        });

        el_modal_product.addEventListener('show.bs.modal', e => {
            el_form_images.value = '';
            el_new_images_preview.innerHTML = '';
        });

        el_form_images.addEventListener('change', e => {
            el_form_images.parentNode.parentNode.querySelector('.field_error').textContent = '';

            if (el_form_images.value == '') {
                el_new_images_preview.innerHTML = '';
            } else {
                const files = el_form_images.files;

                let invalid_images = [];
                for (const file of files) {
                    const file_ext = file.name.replace(/^.*\./, '');

                    if (file.size > (500 * 1024) || (file_ext !== 'png' && file_ext !== 'jpg' && file_ext !== 'jpeg')) {
                        invalid_images.push(file.name);
                    }
                }

                if (invalid_images.length > 0) {
                    el_form_images.parentNode.parentNode.querySelector('.field_error').textContent =
                        'Some files are too large or has invalid format (' + invalid_images.join(', ') + ')';
                } else {
                    for (const file of files) {
                        const file_reader = new FileReader();

                        file_reader.addEventListener('load', function (e) {
                            const image = new Image();
                            image.src = e.target.result;

                            image.addEventListener('load', function (e) {
                                const width = this.width;
                                const height = this.height;
                                const ratio = 150/(width > height ? width : height);
                                const padding_x = 150 - (width * ratio);
                                const padding_y = 150 - (height * ratio);

                                const canvas = document.createElement('canvas');
                                canvas.setAttribute('width', 150);
                                canvas.setAttribute('height', 150);

                                const canvas_context = canvas.getContext('2d');
                                canvas_context.drawImage(
                                    image,
                                    padding_x / 2,
                                    padding_y / 2,
                                    width * ratio,
                                    height * ratio
                                );

                                el_new_images_preview.insertAdjacentElement('beforeEnd', canvas);
                            });
                        });

                        file_reader.readAsDataURL(file);
                    }
                }
            }
        });

        el_modal_product.querySelector('#save_product').addEventListener('click', e => {
            e.preventDefault();
            el_modal_product.querySelector('.loading_spinner').classList.remove('d-none');

            const form_data = new FormData(el_form_product);

            axios.post('{{ route('product.edit', $product->id) }}', form_data)
                .then(response => {
                    el_modal_product.querySelector('.loading_spinner').classList.add('d-none');
                    bootstrap.Modal.getInstance(el_modal_product).hide();
                    window.location.href = window.location.href;
                })
                .catch(error => {
                    console.log(error.response);
                    const errors = error.response.data;

                    if (typeof errors === 'object') {
                        el_form_name.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('name') ? errors.name : '';
                        el_form_category.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('category') ? errors.category : '';
                        el_form_specifications.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('specifications') ? errors.specifications : '';
                        el_form_qty.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('qty') ? errors.qty : '';
                        el_form_price.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('price') ? errors.price : '';
                        el_form_images.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('images') ? errors.images : '';
                    } else {
                        el_form_message.textContent = error.response.data;
                        el_form_message.setAttribute('class', 'alert alert-danger small');
                    }

                    el_modal_product.querySelector('.loading_spinner').classList.add('d-none');
                });
        });
    </script>

    <script defer>
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