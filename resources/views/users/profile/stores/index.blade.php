@section('page-title', $user->name.' - Stores')

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-2 pb-2">
            <h4 class="my-0">Stores</h4>
                <div class="text-end">
                    @can('viewStoreRequests', [new \App\Models\StoreRequest(), $user->id])
                        <a class="btn btn-primary btn-sm" href="{{ route('user.store-requests',  $user->id) }}">Requests</a>
                    @endcan
                    @can('addStore', [new \App\Models\Store(), $user->id])
                        <a href="{{ route('user.add-store', $user->id) }}" class="btn btn-primary btn-sm">Add Store</a>
                    @endcan
                </div>
        </div>

        @if (session('message_type'))
            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
        @endif


        @if ($stores->isEmpty())
            <div class="alert alert-danger mt-2">No records found.</div>
        @else
            <div class="row row-cols-1 g-4">
                @foreach ($stores AS $store)
                    <div class="col">
                        <div class="d-flex align-items-start bg-light p-2">
                            <div class="flex-grow-1 d-flex align-items-start">
                                <img class="card-img store-logo" src="{{ asset('storage/stores/logos/'.($store->logo ?? 'placeholder.jpg')) }}" title="Store Logo">
                                <div class="ms-2">
                                    <a href="{{ route('store.products', $store->id) }}" class="h6">{{ $store->name }}</a>
                                    <p class="my-1 small">
                                        {{ $store->contact_number }}
                                    </p>
                                    <p class="my-1 small">
                                        {{ $store->address.', '.$store->map_address }}
                                    </p>
                                    @if ($store->open_until !== null)
                                        <p class="small text-success my-1">Open Until {{ date('M d, Y', strtotime($store->open_until)) }}</p>
                                    @else
                                        <p class="small text-danger my-1">CLOSED</p>
                                    @endif
                                </div>
                            </div>
                            <div class="d-grid gap-1">
                                @if (Auth::user()->id === $store->user_id)
                                    <a href="{{ route('user.upload-store-logo', [$store->user_id, $store->id]) }}" class="btn btn-secondary btn-sm d-flex align-items-center upload_store_logo">
                                        <i class="material-icons">photo_camera</i>
                                        <span class="ms-1 d-none d-md-block">Upload Logo</span>
                                    </a>
                                @endif
                                @if ($user->id === Auth::user()->id)
                                    <a href="{{ route('user.edit-store', [$user->id, $store->id]) }}" class="btn btn-primary btn-sm d-flex align-items-center">
                                        <i class="material-icons">edit</i>
                                        <span class="ms-1 d-none d-md-block">Edit</span>
                                    </a>
                                    <a href="{{ route('user.transfer-store', [$user->id, $store->id]) }}" class="btn btn-primary btn-sm d-flex align-items-center">
                                        <i class="material-icons">double_arrow</i>
                                        <span class="ms-1 d-none d-md-block">Transfer</span>
                                    </a>
                                @endif
                                @if ($store->open_until !== null)
                                    <a href="{{ route('store.products', $store->id) }}" class="btn btn-danger btn-sm d-flex align-items-center">
                                        <i class="material-icons">power_settings_new</i>
                                        <span class="ms-1 d-none d-md-block">Close</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>



<div class="modal fade" id="logo_modal" tabindex="-1" aria-labelledby="attachment-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachment-modal-label">Upload Logo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div id="upload_message"></div>
                    </div>
                </div>
                <div class="row d-flex justify-content-between">
                    <div class="col-12 col-md-8">
                        <form>
                            <label>Logo</label>
                            <input type="file" name="logo" class="form-control form-control-file" id="store_logo" accept="image/png,image/jpeg">
                            <div class="form-text">Logo should be 150x150 pixels in png or jpeg format. If it exceeds the set dimensions, the logo will be resized accordingly.</div>
                            <div class="text-danger small" id="form_error"></div>
                        </form>
                    </div>
                    <div class="col-12 col-md-4">
                        <div>Preview</div>
                        <div id="logo_preview">
                        </div>

                        <button type="button" class="btn btn-secondary btn-sm d-flex align-items-center d-none" id="form_upload_submit">
                            <i class="material-icons">photo_camera</i>
                            <span class="ms-1">Upload</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var modal = new bootstrap.Modal(document.getElementById('logo_modal'), {
        backdrop: 'static',
        keyboard: false,
    });

    var btn_upload_logo = document.getElementsByClassName('upload_store_logo');
    var file_store_logo = document.getElementById('store_logo');
    var form_error = document.getElementById('form_error');
    var logo_preview = document.getElementById('logo_preview');
    var form_upload_submit = document.getElementById('form_upload_submit');
    var upload_message = document.getElementById('upload_message');

    var file;
    var file_name;
    var file_ext;

    var canvas;
    var canvas_context;

    var url;

    Array.from(btn_upload_logo).forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            url = btn.href;
            modal.show();
        });
    });

    file_store_logo.addEventListener('change', function (e) {
        if (file_store_logo.value == '') {
            form_upload_submit.classList.add('d-none');
            logo_preview.innerHTML = '';
        } else {
            file = this.files[0];
            file_name = file.name;
            file_ext = file_name.replace(/^.*\./, '');

            if (file_ext === 'png' || file_ext === 'jpg' || file_ext === 'jpeg') {
                var file_reader = new FileReader();

                file_reader.addEventListener('load', function (e) {
                    var image = new Image();
                    image.src = e.target.result;

                    image.addEventListener('load', function (e) {
                        var w = this.width;
                        var h = this.height;

                        canvas = document.createElement('canvas');
                        canvas.setAttribute('width', w > 150 ? 150 : w.toString());
                        canvas.setAttribute('height', h > 150 ? 150 : h.toString());

                        canvas_context = canvas.getContext('2d');
                        canvas_context.drawImage(image, 0, 0, w > 150 ? 150 : w, h > 150 ? 150 : h);

                        logo_preview.innerHTML = '';
                        logo_preview.insertAdjacentElement('beforeEnd', canvas);

                        form_upload_submit.classList.remove('d-none');
                    });
                });

                file_reader.readAsDataURL(file);
            } else {
                form_error.innerText = "Invalid file type.";
            }
        }
    });

    form_upload_submit.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        canvas.toBlob(function (blob) {
            var form_data = new FormData();
            form_data.append('store_logo', blob, file_name + '.' + file_ext);

            axios.post(url, form_data)
                .then(function (response) {
                    upload_message.setAttribute('class', 'alert alert-success');
                    upload_message.innerText = 'Logo has been uploaded.';

                    document.getElementsByClassName('modal')[0].addEventListener('hidden.bs.modal', function (e) {
                        location.reload();
                    });
                })
                .catch(function (error) {
                    upload_message.setAttribute('class', 'alert alert-danger');

                    if (error.status === 400) {
                        upload_message.innerText = error.response.data.store_logo;
                    } else {
                        upload_message.innerText = error.response.data;
                    }
                });
        });
    });
</script>