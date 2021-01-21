@extends('stores.profile.index')
@section('page-title', $store->name.' - Add Product')

@section('profile-content')
    <div class="row">
        <div class="col">
            <h4 class="border-bottom p-2">{{ $form_title }}</h4>

            @if (session('message_type'))
                <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
            @endif

            <form method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $form_data['name'] ?? old('name') }}">
                        <div class="form-text">If you are inserting a product variant, make sure that the names are identical.</div>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label>Category</label>
                        <select name="category" class="form-select">
                            @foreach ($categories AS $main_cat => $value)
                                @php $category = $main_cat.'|all'; @endphp
                                <option value="{{ $category }}"
                                        {{ ((isset($form_data['main_category']) AND $form_data['main_category'] === $category) OR old('category') === $category) ? 'selected' : '' }}
                                >{{ ucwords($main_cat).' - All' }}</option>

                                @if (empty($value) === false AND is_array($value))
                                    @foreach ($value AS $sub_cat => $placeholder)
                                        @php $category = $main_cat.'|'.$sub_cat; @endphp
                                        <option value="{{ $category }}"
                                                {{ ((isset($form_data['main_category'], $form_data['sub_category']) AND $form_data['main_category'].'|'.$form_data['sub_category'] === $category) OR old('category') === $category) ? 'selected' : '' }}
                                        >{{ ucwords($main_cat.' - '.$sub_cat) }}</option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                        @error('category')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label>Specifications</label>
                        @if (isset($form_data['specifications']))
                            @php
                            $specifications = '';

                            foreach ($form_data['specifications'] AS $spec) {
                                $specifications .= $spec['name'].':'.$spec['value'].' | ';
                            }
                            @endphp
                        @endif
                        <textarea name="specifications" class="form-control" id="specifications">{{ isset($form_data['specifications']) ? trim($specifications, '| ') : old('specifications') }}</textarea>
                        <div class="form-text">
                            Insert product specifications as a <code>name:value</code> pair with a vertical bar <code>|</code> as separator.<br>
                            For example, Color : Red | Size : L | Camera : 14MP Rear, 5MP Front
                        </div>
                        @error('specifications')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label>Quantity</label>
                        <input type="number" name="qty" class="form-control" value="{{ $form_data['qty'] ?? old('qty') }}" min="1">
                        @error('qty')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label>Price</label>
                        <input type="number" name="price" class="form-control" value="{{ $form_data['price'] ?? old('price') }}" min="1" step="0.01">
                        @error('price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label>Images</label>
                        <input type="file" name="images[]" id="images" class="form-control form-control-file" accept="image/png,image/jpeg" multiple>
                        <div class="form-text">It is recommended that images are 512x512 pixels in png or jpeg format and must not be larger than 500KB.</div>
                        <div class="text-danger" id="images_error"></div>
                        @error('images')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        Preview
                        <div id="preview"></div>
                    </div>

                    @if (isset($form_data['images']))
                        <div class="col-12">
                            <div>Current Images</div>
                            <div class="row">
                                @foreach ($form_data['images'] AS $image)
                                    <div class="col img-preview d-flex flex-grow-0 flex-column mt-5 m-md-1">
                                        <img src="{{ asset('storage/products/images/original/'.$image['filename']) }}" class="img-preview">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <input type="checkbox" name="removed[{{ $image['id'] }}]">
                                            <span class="ms-1">Remove</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        let images = document.getElementById('images');
        let images_error = document.getElementById('images_error');
        let preview = document.getElementById('preview');

        images.addEventListener('change', function (e) {
            preview.innerHTML = '';
            images_error.innerText = '';

            if (images.value != '') {
                Array.from(this.files).forEach(function (file) {
                    let canvas = document.createElement('canvas');
                    let canvas_context = canvas.getContext('2d');

                    file_name = file.name;
                    file_ext = file_name.replace(/^.*\./, '');

                    if (file_ext === 'png' || file_ext === 'jpg' || file_ext === 'jpeg') {
                        if (file.size / 1024 <= 500) {
                            let file_reader = new FileReader();

                            file_reader.addEventListener('load', function (e) {
                                let image = new Image();
                                image.src = e.target.result;

                                image.addEventListener('load', function (e) {
                                    canvas = document.createElement('canvas');
                                    canvas.setAttribute('class', 'img-thumbnail m-1');
                                    canvas.setAttribute('width', '150');
                                    canvas.setAttribute('height', '150');

                                    canvas_context = canvas.getContext('2d');

                                    let w = this.width;
                                    let h = this.height;
                                    let ratio = w / h;
                                    let pad_x = 0;
                                    let pad_y = 0;

                                    if (ratio === 1) {
                                        // square
                                        canvas_context.drawImage(image, 6, 6, 133, 133);
                                    } else if (ratio < 1) {
                                        // vertical
                                        w = 133 * ratio;
                                        h = 133;
                                        pad_x = (150 - w) / 2;
                                        pad_y = (150 - h) / 2;
                                        canvas_context.drawImage(image, pad_x, pad_y, w, h);
                                    } else if (ratio > 1) {
                                        // horizontal
                                        w = 133;
                                        h = 133 * ratio;
                                        pad_x = (150 - w) / 2;
                                        pad_y = (150 - h) / 2;
                                        canvas_context.drawImage(image, pad_x, pad_y, w, h);
                                    }

                                    preview.insertAdjacentElement('beforeEnd', canvas);
                                });
                            });

                            file_reader.readAsDataURL(file);
                        } else {
                            images_error.innerText = 'Some files are too large.';
                        }
                    } else {
                        images_error.innerText = 'Some files are of invalid file type.';
                    }
                });
            }
        });

        let specifications = document.getElementById('specifications');
        resizeTextArea(specifications);

        specifications.addEventListener('keyup', function (e) {
            resizeTextArea(this);
        });

        function resizeTextArea(textarea) {
            textarea.style.overflow = 'hidden';
            textarea.style.height = '62px';
            textarea.style.height = textarea.scrollHeight + 'px';
        }
    </script>
@endsection