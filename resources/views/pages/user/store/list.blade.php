@extends('pages.user.profile')
@section('page-title', $user->name.' - Stores')

@section('profile-content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center border-bottom mt-3 mb-2 pb-2">
                <h4 class="my-0">Stores</h4>
                <div class="text-end">
                    @can('listOwn', [new \App\Models\StoreRequest(), $user->id])
                        <a class="btn btn-primary btn-sm" href="{{ route('user.store-requests',  $user->id) }}">
                            Requests
                        </a>
                    @endcan
                    @can('create', [new \App\Models\Store(), $user->id])
                        <button class="btn btn-primary btn-sm" id="add_store">
                            Add Store
                        </button>
                    @endcan
                </div>
            </div>

            <div id="system_message"></div>

            <div class="alert alert-danger small mt-2 {{ $stores->isNotEmpty() ? 'd-none' : '' }}" id="no_records">No records found.</div>
            <div class="d-flex flex-column {{ $stores->isEmpty() ? 'd-none' : '' }}" id="store_list">
                <div class="d-flex align-items-start bg-light p-2 my-1 d-none" id="item_template">
                    <div class="flex-grow-1 d-flex align-items-start">
                        <img class="card-img store-logo item_logo" title="Store Logo">
                        <div class="ms-2">
                            <a href="#" class="h6 item_name"></a>
                            <p class="my-1 small item_contact_number"></p>
                            <p class="my-1 small item_address_line"></p>
                            <p class="my-1 small item_map_address"></p>
                            <p class="visually-hidden item_map_coordinates"></p>
                            <p class="text-success my-1 small item_open_until"></p>
                            <p class="visually-hidden item_open_until_raw"></p>
                        </div>
                    </div>
                    <div class="d-grid gap-1">
                        <div class="visually-hidden store_id"></div>
                        <a href="#" class="btn btn-outline-dark btn-sm upload_store_logo">
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">photo_camera</i>
                                <span class="ms-1 small d-none d-md-block">Upload</span>
                            </div>
                        </a>
                        <a href="#" class="btn btn-outline-dark btn-sm edit_store">
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">edit</i>
                                <span class="ms-1 small d-none d-md-block">Edit</span>
                            </div>
                        </a>
                        <a href="#" class="btn btn-outline-dark btn-sm transfer_store">
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">double_arrow</i>
                                <span class="ms-1 small d-none d-md-block">Transfer</span>
                            </div>
                        </a>
                        <a href="#" class="btn btn-outline-dark btn-sm close_store">
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">power_settings_new</i>
                                <span class="ms-1 small d-none d-md-block">Close</span>
                            </div>
                        </a>
                    </div>
                </div>
                @if ($stores->isNotEmpty())
                    @foreach ($stores AS $store)
                        <div class="d-flex align-items-start bg-light p-2 my-1" id="item_{{ $store->id }}">
                            <div class="flex-grow-1 d-flex align-items-start">
                                <img class="card-img store-logo item_logo" src="{{ asset('storage/stores/logos/'.($store->logo ?? 'placeholder.jpg')) }}" title="Store Logo">
                                <div class="ms-2">
                                    <a href="{{ route('store.products', $store->id) }}" class="h6 item_name">{{ $store->name }}</a>
                                    <p class="my-1 small item_contact_number">
                                        {{ $store->contact_number }}
                                    </p>
                                    <p class="my-1 small item_address_line">
                                        {{ $store->address_line }}
                                    </p>
                                    <p class="my-1 small item_map_address">
                                        {{ $store->map_address }}
                                    </p>
                                    <p class="visually-hidden item_map_coordinates">
                                        {{ $store->map_coordinates }}
                                    </p>
                                    <p class="small text-{{ strtotime($store->open_until) >= strtotime('now') ? 'success' : 'danger' }} my-1 item_open_until">
                                        @if ($store->open_until !== null)
                                            Open Until {{ date('M d, Y', strtotime($store->open_until)) }}
                                        @else
                                            Closed
                                        @endif
                                    </p>
                                    <p class="visually-hidden item_open_until_raw">
                                        {{ date('Y-m-d', strtotime($store->open_until)) }}
                                    </p>
                                </div>
                            </div>
                            <div class="d-grid gap-1">
                                <div class="visually-hidden store_id">{{ $store->id }}</div>
                                @if (Auth::user()->id === $store->user_id)
                                    <a href="#" class="btn btn-outline-dark btn-sm upload_store_logo">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons fs-16">photo_camera</i>
                                            <span class="ms-1 small d-none d-md-block">Upload</span>
                                        </div>
                                    </a>
                                    <a href="#" class="btn btn-outline-dark btn-sm edit_store">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons fs-16">edit</i>
                                            <span class="ms-1 small d-none d-md-block">Edit</span>
                                        </div>
                                    </a>
                                    <a href="#" class="btn btn-outline-dark btn-sm transfer_store">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons fs-16">double_arrow</i>
                                            <span class="ms-1 small d-none d-md-block">Transfer</span>
                                        </div>
                                    </a>
                                @endif
                                @if ($store->open_until !== null)
                                    <a href="{{ route('store.products', $store->id) }}" class="btn btn-outline-dark btn-sm close_store">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons fs-16">power_settings_new</i>
                                            <span class="ms-1 small d-none d-md-block">Close</span>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_logo" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title">Upload Logo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <form id="form_logo" enctype="multipart/form-data">
                        <div class="my-2">
                            <div class="form-text">Recommended size is 150x150. Larger images will be scaled down.</div>
                            <input type="file" name="logo" class="form-control-file form-control-sm" accept="image/jpeg,image/png">
                        </div>
                    </form>

                    <div class="text-center my-2">
                        <div>Preview</div>
                        <div id="logo_preview"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm" id="upload_logo">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_store" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="attachment-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title" id="attachment-modal-label">Store Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <form id="form_store">
                        <input type="hidden" name="store_id">

                        <div class="my-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">store</i></span>
                                <input type="text" name="name" class="form-control form-control-sm" placeholder="Name">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="my-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">textsms</i></span>
                                <input type="text" name="contact_number" class="form-control form-control-sm" placeholder="Contact Number">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="my-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">house</i></span>
                                <input type="text" name="address_line" class="form-control form-control-sm" placeholder="Address Line">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="my-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">map</i></span>
                                <input type="text" name="map_address" class="form-control form-control-sm" readonly placeholder="Map Address">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="my-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">place</i></span>
                                <input type="text" name="map_coordinates" class="form-control form-control-sm" readonly placeholder="Map Coordinates">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div style="width: 100%; height: 300px;" id="gmap_container"></div>
                            <div class="form-text">
                                Click on your location on the map to get additional location information.
                                Please select a location within {{ implode('/', config('system.service_area')) }}.
                            </div>
                        </div>

                        <div class="my-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">calendar_today</i></span>
                                <input type="date" name="open_until" class="form-control form-control-sm" placeholder="Open Until">
                            </div>
                            <div class="form-text">Set closing date based on your business permit expiry.</div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="my-2">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-16">file_present</i></span>
                                <input type="file" class="form-control-file form-control-sm" name="attachment" accept="application/pdf">
                            </div>
                            <div class="form-text">Combine all necessary documents in a single pdf file for approval.</div>
                            <div class="text-danger small field_error"></div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm" id="submit_application">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_transfer" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title">Transfer Store</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <div class="flex-grow-1 d-flex align-items-start p-2 bg-light">
                        <img class="card-img store-logo" id="item_logo" title="Store Logo">
                        <div class="ms-2">
                            <p class="visually-hidden" id="store_id"></p>
                            <p class="text-primary my-1 small" id="item_name"></p>
                            <p class="my-1 small" id="item_contact_number"></p>
                            <p class="my-1 small" id="item_address_line"></p>
                            <p class="my-1 small" id="item_map_address"></p>
                            <p class="visually-hidden" id="item_map_coordinates"></p>
                            <p class="text-success my-1 small" id="item_open_until"></p>
                            <p class="visually-hidden" id="item_open_until_raw"></p>
                        </div>
                    </div>

                    <p class="border-bottom mt-3 mb-1">
                        Recipient
                    </p>
                    <form>
                        <div class="my-1">
                            <div class="input-group">
                                <input type="text" name="email" class="form-control form-control-sm" placeholder="Email address">
                                <button type="button" class="btn btn-outline-dark btn-sm" id="find_recipient">Search</button>
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>
                        <div class="my-1">
                            <input type="file" name="attachment" class="form-control-file form-control-sm" placeholder="Attachment" accept="application/pdf">
                            <div class="form-text">Combine all your necessary documents into a single pdf file.</div>
                            <div class="text-danger small field_error"></div>
                        </div>
                    </form>

                    <div class="mt-1 p-2 bg-light d-none" id="recipient_info">
                        <div id="recipient_id"></div>
                        <div id="recipient_name"></div>
                        <div id="recipient_email"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm" id="submit_transfer">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Store -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initMap" defer></script>
    <script defer>
        const el_system_message = document.getElementById('system_message');
        const el_no_records = document.getElementById('no_records');
        const el_store_list = document.getElementById('store_list');
        const el_add_store = document.getElementById('add_store');
        const el_modal_store = document.getElementById('modal_store');
        const el_modal_transfer = document.getElementById('modal_transfer');
        const el_item_template = document.getElementById('item_template');

        const el_modal_form_message = el_modal_store.querySelector('#form_message');
        const el_modal_name = el_modal_store.querySelector('[name="name"]');
        const el_modal_contact_number = el_modal_store.querySelector('[name="contact_number"]');
        const el_modal_address_line = el_modal_store.querySelector('[name="address_line"]');
        const el_modal_map_address = el_modal_store.querySelector('[name="map_address"]');
        const el_modal_map_coordinates = el_modal_store.querySelector('[name="map_coordinates"]');
        const el_modal_open_until = el_modal_store.querySelector('[name="open_until"]');
        const el_modal_attachment = el_modal_store.querySelector('[name="attachment"]');

        let gmap;
        let gmap_lat;
        let gmap_lng;
        let gmap_marker;

        function initMap() {
            gmap = new google.maps.Map(document.getElementById("gmap_container"), {
                zoom: 16,
                center: { lat: gmap_lat ?? 16.409447, lng: gmap_lng ?? 120.599264 },
                mapTypeId: google.maps.MapTypeId.HYBRID,
            });

            gmap.addListener('click', function (e) {
                gmap_lat = e.latLng.toJSON().lat;
                gmap_lng = e.latLng.toJSON().lng;

                setGmapMarker(gmap_lat, gmap_lng);

                // retrieve location details from Geocoding API
                axios.get('{{ env('GEOCODING_API_URL') }}/json?latlng=' + gmap_lat + ',' + gmap_lng + '&result_type=administrative_area_level_5|sublocality&key={{ env('GMAP_API_KEY') }}')
                    .then(function (response) {
                        if (response.data.status == 'OK') {
                            el_modal_map_address.value = response.data.results[0].formatted_address;
                            el_modal_map_coordinates.value = gmap_lat + ',' + gmap_lng;
                        } else {
                            alert('Unable to retrieve information.');
                        }
                    });
            });
        }

        function setGmapMarker(lat, lng) {
            if (gmap_marker) {
                gmap_marker.setMap(null);
            }

            if (lat && lng) {
                gmap_marker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: gmap,
                });

                gmap.panTo(new google.maps.LatLng(lat, lng));
            }
        }

        function showUploadLogoModal(store_id) {
            store_id = parseInt(store_id);

            const el_modal_logo_copy = document.getElementById('modal_logo').cloneNode(true);
            el_modal_logo_copy.setAttribute('id', 'modal_logo_' + store_id);
            el_modal_logo_copy.addEventListener('hidden.bs.modal', e => {
                el_modal_logo_copy.remove();
            });

            const el_form_message = el_modal_logo_copy.querySelector('#form_message');
            const el_form_file = el_modal_logo_copy.querySelector('[name="logo"]');
            const el_logo_preview = el_modal_logo_copy.querySelector('#logo_preview');
            const el_upload_logo = el_modal_logo_copy.querySelector('#upload_logo');

            el_form_file.value = '';
            el_logo_preview.innerHTML = '';

            const modal = new bootstrap.Modal(el_modal_logo_copy);
            modal.show();

            el_form_file.addEventListener('change', e => {
                el_form_message.textContent = '';
                el_form_message.removeAttribute('class');

                if (el_form_file.value == '') {
                    el_logo_preview.innerHTML = '';
                } else {
                    const file = el_form_file.files[0];
                    const file_name = file.name;
                    const file_ext = file_name.replace(/^.*\./, '');

                    if (file_ext === 'png' || file_ext === 'jpg' || file_ext === 'jpeg') {
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

                                el_logo_preview.innerHTML = '';
                                el_logo_preview.insertAdjacentElement('beforeEnd', canvas);
                            });
                        });

                        file_reader.readAsDataURL(file);
                    } else {
                        el_form_message.textContent = "Logo is not a valid png/jpg file.";
                        el_form_message.setAttribute('class', 'alert alert-danger small');
                    }
                }
            });

            el_upload_logo.addEventListener('click', e => {
                e.preventDefault();
                el_modal_logo_copy.querySelector('.loading_spinner').classList.remove('d-none');

                const form_data = new FormData();
                form_data.append('logo', el_form_file.value !== '' ? el_form_file.files[0] : null);

                axios.post('/users/{{ $user->id }}/stores/' + store_id + '/upload-logo', form_data)
                    .then(response => {
                        document.getElementById('item_' + store_id).querySelector('.item_logo').src = '{{ asset('storage/stores/logos') }}' + '/' + response.data;
                        el_system_message.textContent = document.getElementById('item_' + store_id).querySelector('.item_name').textContent + '\'s logo has been updated.';
                        el_system_message.setAttribute('class', 'alert alert-success small');
                        el_modal_logo_copy.querySelector('.loading_spinner').classList.add('d-none');
                        modal.hide();
                    })
                    .catch(error => {
                        el_form_message.textContent = error.response.data.logo[0];
                        el_form_message.setAttribute('class', 'alert alert-danger small');
                        el_modal_logo_copy.querySelector('.loading_spinner').classList.add('d-none');
                    });
            });
        }

        function showEditStoreModal(store_id) {
            store_id = parseInt(store_id);

            const el_item = document.getElementById('item_' + store_id);
            const el_form_message = el_modal_store.querySelector('#form_message');
            const el_form_id = el_modal_store.querySelector('[name="store_id"]');
            const el_form_name = el_modal_store.querySelector('[name="name"]');
            const el_form_contact_number = el_modal_store.querySelector('[name="contact_number"]');
            const el_form_address_line = el_modal_store.querySelector('[name="address_line"]');
            const el_form_map_address = el_modal_store.querySelector('[name="map_address"]');
            const el_form_map_coordinates = el_modal_store.querySelector('[name="map_coordinates"]');
            const el_form_open_until = el_modal_store.querySelector('[name="open_until"]');
            const el_form_attachment = el_modal_store.querySelector('[name="attachment"]');

            el_form_message.textContent = '';
            el_form_message.removeAttribute('class');
            el_modal_store.querySelectorAll('.field_error').forEach(field => field.textContent = '');
            el_form_id.value = store_id;
            el_form_name.value = el_item.querySelector('.item_name').textContent.trim();
            el_form_contact_number.value = el_item.querySelector('.item_contact_number').textContent.trim();
            el_form_address_line.value = el_item.querySelector('.item_address_line').textContent.trim();
            el_form_map_address.value = el_item.querySelector('.item_map_address').textContent.trim();
            el_form_map_coordinates.value = el_item.querySelector('.item_map_coordinates').textContent.trim();
            el_form_open_until.value = el_item.querySelector('.item_open_until_raw').textContent.trim();
            el_form_attachment.value = '';

            [lat, lng] = el_form_map_coordinates.value.split(',').map(x => parseFloat(x));
            setGmapMarker(lat, lng);

            const modal = new bootstrap.Modal(el_modal_store);
            modal.show();
        }

        function showTransferStoreModal(store_id) {
            store_id = parseInt(store_id);

            const el_item = document.getElementById('item_' + store_id);
            el_modal_transfer.querySelector('#item_logo').src = el_item.querySelector('.item_logo').src;
            el_modal_transfer.querySelector('#store_id').textContent = parseInt(el_item.querySelector('.store_id').textContent);
            el_modal_transfer.querySelector('#item_name').textContent = el_item.querySelector('.item_name').textContent;
            el_modal_transfer.querySelector('#item_name').textContent = el_item.querySelector('.item_name').textContent;
            el_modal_transfer.querySelector('#item_contact_number').textContent = el_item.querySelector('.item_contact_number').textContent;
            el_modal_transfer.querySelector('#item_address_line').textContent = el_item.querySelector('.item_address_line').textContent;
            el_modal_transfer.querySelector('#item_map_address').textContent = el_item.querySelector('.item_map_address').textContent;
            el_modal_transfer.querySelector('#item_open_until').textContent = 'Open Until '
                + dateFormat(new Date(el_item.querySelector('.item_open_until').textContent), 'mmm dd, yyyy');

            const modal = new bootstrap.Modal(el_modal_transfer);
            modal.show();
        }

        el_add_store.addEventListener('click', e => {
            e.preventDefault();

            const modal = new bootstrap.Modal(el_modal_store);
            modal.show();
        });

        el_store_list.querySelectorAll('.upload_store_logo').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                showUploadLogoModal(btn.parentNode.querySelector('.store_id').textContent);
            });
        });

        el_store_list.querySelectorAll('.edit_store').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                showEditStoreModal(btn.parentNode.querySelector('.store_id').textContent);
            });
        });

        el_store_list.querySelectorAll('.transfer_store').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                showTransferStoreModal(btn.parentNode.querySelector('.store_id').textContent);
            });
        });

        el_modal_store.querySelector('#submit_application').addEventListener('click', e => {
            e.preventDefault();
            el_modal_store.querySelector('.loading_spinner').classList.remove('d-none');

            el_modal_form_message.textContent = '';
            el_modal_form_message.removeAttribute('class');
            el_modal_store.querySelectorAll('.field_error').forEach(field => field.textContent = '');

            const form_data = new FormData(el_modal_store.querySelector('#form_store'));

            axios.post('{{ route('user.new-store-application', $user->id) }}', form_data)
                .then(response => {
                    const data = response.data;
                    el_system_message.setAttribute('class', 'alert alert-success small');

                    if (data.status === 'pending') {
                        el_system_message.innerHTML = 'Your application has been submitted. Your reference number is '
                            + '<a href="/users/' + data.user_id + '/stores/requests/' + data.ref_no + '/view">'
                            + data.ref_no + '</a>.';
                    } else if (data.status === 'accepted') {
                        if (form_data.get('store_id')) {
                            const item = document.getElementById('item_' + form_data.get('store_id'));

                            el_system_message.innerHTML = item.querySelector('.item_name').textContent
                                + ' store has been updated. Your reference number is '
                                + '<a href="/users/' + data.user_id + '/stores/requests/' + data.ref_no + '/view">'
                                + data.ref_no + '</a>.';

                            item.querySelector('.item_name').textContent = form_data.get('name');
                            item.querySelector('.item_name').href = '/stores/' + data.store_id + '/products';
                            item.querySelector('.item_contact_number').textContent = form_data.get('contact_number');
                            item.querySelector('.item_address_line').textContent = form_data.get('address_line');
                            item.querySelector('.item_map_address').textContent = form_data.get('map_address');
                            item.querySelector('.item_map_coordinates').textContent = form_data.get('map_coordinates');
                            item.querySelector('.item_open_until').textContent = 'Open Until '
                                + dateFormat(new Date(form_data.get('open_until')), 'mmm dd, yyyy');
                            item.querySelector('.item_open_until_raw').textContent = form_data.get('open_until');
                        } else {
                            const template_copy = el_item_template.cloneNode(true);
                            template_copy.setAttribute('id', 'item_' + data.store_id);
                            template_copy.classList.remove('d-none');

                            template_copy.querySelector('.item_logo').src = '{{ asset('storage/stores/logos/placeholder.jpg') }}';
                            template_copy.querySelector('.item_name').textContent = form_data.get('name');
                            template_copy.querySelector('.item_name').href = '/stores/' + data.store_id + '/products';
                            template_copy.querySelector('.item_contact_number').textContent = form_data.get('contact_number');
                            template_copy.querySelector('.item_address_line').textContent = form_data.get('address_line');
                            template_copy.querySelector('.item_map_address').textContent = form_data.get('map_address');
                            template_copy.querySelector('.item_map_coordinates').textContent = form_data.get('map_coordinates');
                            template_copy.querySelector('.item_open_until').textContent = 'Open Until '
                                + dateFormat(new Date(form_data.get('open_until')), 'mmm dd, yyyy');
                            template_copy.querySelector('.item_open_until_raw').textContent = form_data.get('open_until');

                            template_copy.querySelector('.upload_store_logo').addEventListener('click', e => {
                                e.preventDefault();
                                showUploadLogoModal(data.store_id);
                            });

                            template_copy.querySelector('.edit_store').addEventListener('click', e => {
                                e.preventDefault();
                                showEditStoreModal(data.store_id);
                            });

                            el_store_list.insertAdjacentElement('afterbegin', template_copy);

                            el_system_message.innerHTML = el_modal_name.value + ' store has been added. Your reference number is '
                                + '<a href="/users/' + data.user_id + '/stores/requests/' + data.ref_no + '/view">'
                                + data.ref_no + '</a>.';
                        }
                    }

                    el_modal_store.querySelector('.loading_spinner').classList.add('d-none');
                    bootstrap.Modal.getInstance(el_modal_store).hide();
                })
                .catch(error => {
                    const errors = error.response.data;

                    if (typeof errors === 'object') {
                        el_modal_name.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('name') ? errors.name : '';
                        el_modal_contact_number.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('contact_number') ? errors.contact_number : '';
                        el_modal_address_line.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('address_line') ? errors.address_line : '';
                        el_modal_map_address.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('map_address') ? errors.map_address : '';
                        el_modal_map_coordinates.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('map_coordinates') ? errors.map_coordinates : '';
                        el_modal_open_until.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('open_until') ? errors.open_until : '';
                        el_modal_attachment.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('attachment') ? errors.attachment : '';
                    } else {
                        el_modal_form_message.setAttribute('class', 'alert alert-danger small');
                        el_modal_form_message.textContent = errors;
                    }

                    el_modal_store.querySelector('.loading_spinner').classList.add('d-none');
                });
        });

        el_modal_transfer.querySelector('form').addEventListener('submit', e => e.preventDefault());

        el_modal_transfer.querySelector('#find_recipient').addEventListener('click', e => {
            e.preventDefault();
            el_modal_transfer.querySelector('.loading_spinner').classList.remove('d-none');

            const el_recipient_info = el_modal_transfer.querySelector('#recipient_info');
            const el_target_email = el_modal_transfer.querySelector('[name="email"]');

            el_modal_transfer.querySelector('#form_message').textContent = '';
            el_modal_transfer.querySelector('#form_message').removeAttribute('class');
            el_recipient_info.classList.remove('d-none');
            el_recipient_info.classList.add('d-none');

            axios.post('{{ route('user.find-email') }}', { email: el_target_email.value })
                .then(response => {
                    el_recipient_info.classList.remove('d-none');
                    el_recipient_info.querySelector('#recipient_id').textContent = response.data.id;
                    el_recipient_info.querySelector('#recipient_name').textContent = response.data.name;
                    el_recipient_info.querySelector('#recipient_email').textContent = response.data.email;

                    el_modal_transfer.querySelector('.loading_spinner').classList.add('d-none');
                })
                .catch(error => {
                    el_modal_transfer.querySelector('#form_message').textContent = error.response.data;
                    el_modal_transfer.querySelector('#form_message').setAttribute('class', 'alert alert-danger small');
                    el_modal_transfer.querySelector('.loading_spinner').classList.add('d-none');
                });
        });

        el_modal_transfer.querySelector('#submit_transfer').addEventListener('click', e => {
            e.preventDefault();
            el_modal_transfer.querySelector('.loading_spinner').classList.remove('d-none');

            el_modal_transfer.querySelector('#form_message').textContent = '';
            el_modal_transfer.querySelector('#form_message').removeAttribute('class');
            el_modal_transfer.querySelectorAll('.field_error').forEach(field => field.textContent = '');

            const form_data = new FormData(el_modal_transfer.querySelector('form'));
            form_data.append('store_id', parseInt(el_modal_transfer.querySelector('#store_id').textContent));

            axios.post('{{ route('user.new-store-transfer', $user->id) }}', form_data)
                .then(response => {
                    const data = response.data;
                    el_system_message.setAttribute('class', 'alert alert-success small');

                    if (data.status === 'pending') {
                        el_system_message.innerHTML = 'Your application has been submitted. Your reference number is '
                            + '<a href="/users/' + data.user_id + '/stores/requests/' + data.ref_no + '/view">'
                            + data.ref_no + '</a>.';
                    } else if (data.status === 'accepted') {
                        const item = document.getElementById('item_' + form_data.get('store_id'));

                        el_system_message.innerHTML = item.querySelector('.item_name').textContent
                            + ' store has been transferred. Your reference number is '
                            + '<a href="/users/' + data.user_id + '/stores/requests/' + data.ref_no + '/view">'
                            + data.ref_no + '</a>.';

                        item.remove();
                    }

                    el_modal_transfer.querySelector('.loading_spinner').classList.add('d-none');
                    bootstrap.Modal.getInstance(el_modal_transfer).hide();
                })
                .catch(error => {
                    const errors = error.response.data;
                    if (typeof errors === 'object') {
                        el_modal_transfer.querySelector('[name="email"]').parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('email') ? errors.email : '';
                        el_modal_transfer.querySelector('[name="attachment"]').parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('attachment') ? errors.attachment : '';
                    } else {
                        el_modal_transfer.querySelector('#form_message').textContent = errors;
                        el_modal_transfer.querySelector('#form_message').setAttribute('class', 'alert alert-danger small');
                    }

                    el_modal_transfer.querySelector('.loading_spinner').classList.add('d-none');
                });
        });
    </script>
@endsection