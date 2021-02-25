@extends('pages.store.profile')
@section('page-title', $store->name.' - Products')

@section('profile-content')
    <div class="row my-3">
        <div class="col-12 col-lg-3">
            @can('manage', [new \App\Models\Product(), $store->user_id])
                <div class="d-grid d-block mb-3">
                    <button type="button" class="btn btn-primary btn-sm my-2" id="add_product">
                        <div class="d-flex justify-content-center align-items-center">
                            <i class="material-icons fs-16">add</i>
                            <span class="ms-1 small">ADD PRODUCT</span>
                        </div>
                    </button>
                </div>
            @endcan
            @php
                echo $product_filter;
            @endphp
        </div>
        <div class="col-12 col-lg-9">
            <div id="system_message"></div>

            @if ($products->isEmpty())
                <div class="text-center text-muted">No products found.</div>
            @else
                <div class="row row-cols-3 row-cols-md-4 row-cols-lg-6 gx-1 gx-lg-2 gy-2 g-lg-2" id="product_list">
                    <div class="col d-none" id="product_template">
                        <a href="#" class="card-link-wrapper redirect_product_page">
                            <div class="card product-listing">
                                <img src="" class="card-img-top product_image">
                                <div class="card-body p-2">
                                    <p class="mb-1 small ellipsis product_name"></p>
                                    <p class="mb-1 text-primary product_price"></p>
                                </div>
                            </div>
                        </a>
                    </div>
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
                        <input type="hidden" name="id">

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">label</i></span>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Name">
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
                                        <option value="{{ $category }}">{{ ucwords($main_cat).' - All' }}</option>

                                        @if (empty($value) === false AND is_array($value))
                                            @foreach ($value AS $sub_cat => $placeholder)
                                                @php $category = $main_cat.'|'.$sub_cat; @endphp
                                                <option value="{{ $category }}">{{ ucwords($main_cat.' - '.$sub_cat) }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">list_alt</i></span>
                                <textarea name="specifications" class="form-control form-control-sm" placeholder="Specifications"></textarea>
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
                                <input type="number" name="qty" class="form-control form-control-sm" min="0" step="1" placeholder="Quantity">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">sell</i></span>
                                <input type="number" name="price" class="form-control form-control-sm" min="0" step="0.01" placeholder="Price">
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
                            <div class="text-center my-1">Preview</div>
                            <div id="preview"></div>
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
        const el_product_list = document.getElementById('product_list');
        const el_product_template = document.getElementById('product_template');
        const el_modal_product = document.getElementById('modal_product');
        const el_form_message = el_modal_product.querySelector('#form_message');
        const el_form_product = el_modal_product.querySelector('#form_product');
        const el_form_name = el_form_product.querySelector('[name="name"]');
        const el_form_category = el_form_product.querySelector('[name="category"]');
        const el_form_specifications = el_form_product.querySelector('[name="specifications"]');
        const el_form_qty = el_form_product.querySelector('[name="qty"]');
        const el_form_price = el_form_product.querySelector('[name="price"]');
        const el_form_images = el_form_product.querySelector('[name="images[]"]');
        const el_images_preview = el_modal_product.querySelector('#preview');

        document.getElementById('add_product').addEventListener('click', e => {
            const modal = new bootstrap.Modal(document.getElementById('modal_product'), {
                keyboard: false,
                backdrop: 'static',
            });
            modal.show();
        });

        el_modal_product.addEventListener('show.bs.modal', e => {
            el_modal_product.querySelectorAll('input').forEach(input => input.value = '');
            el_modal_product.querySelectorAll('textarea').forEach(input => input.value = '');
            el_modal_product.querySelectorAll('.field_error').forEach(field => field.textContent = '');
            el_images_preview.innerHTML = '';
        });

        el_form_images.addEventListener('change', e => {
            el_form_images.parentNode.parentNode.querySelector('.field_error').textContent = '';

            if (el_form_images.value == '') {
                el_images_preview.innerHTML = '';
            } else {
                const files = el_form_images.files;

                let invalid_images = [];
                for (const file of files) {
                    const file_ext = file.name.replace(/^.*\./, '');
                    console.log(file_ext);

                    if (file.size > (500 * 1024)
                        || (file_ext.toLowerCase() !== 'png'
                            && file_ext.toLowerCase() !== 'jpg'
                            && file_ext.toLowerCase() !== 'jpeg')
                    ) {
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

                                el_images_preview.insertAdjacentElement('beforeEnd', canvas);
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

            el_form_message.textContent = '';
            el_form_message.removeAttribute('class');
            el_modal_product.querySelectorAll('.field_error').forEach(field => field.textContent = '');

            const form_data = new FormData(el_form_product);

            axios.post('{{ route('store.add-product', $store->id) }}', form_data)
                .then(response => {
                    const data = response.data;

                    const template_copy = el_product_template.cloneNode(true);
                    template_copy.removeAttribute('id');
                    template_copy.classList.remove('d-none');

                    template_copy.querySelector('.redirect_product_page').href = '/products/' + data.id + '/info';
                    template_copy.querySelector('.product_image').src = '/storage/products/images/preview/' + data.preview;
                    template_copy.querySelector('.product_name').textContent = data.name;
                    template_copy.querySelector('.product_price').textContent = currency_formatter.format(data.price);

                    el_product_list.insertAdjacentElement('afterbegin', template_copy);

                    el_modal_product.querySelector('.loading_spinner').classList.add('d-none');
                    el_system_message.textContent = data.name + ' has been added.';
                    el_system_message.setAttribute('class', 'alert alert-success small');
                    bootstrap.Modal.getInstance(el_modal_product).hide();
                })
                .catch(error => {
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
@endsection