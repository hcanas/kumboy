@extends('pages.user.profile')
@section('page-title', $user->name.' - Address Book')

@section('profile-content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center my-3">
                <h4 class="text-black-50 my-0">Address Book</h4>
                @if (Auth::user()->id === $user->id)
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal_address">
                        Add Address
                    </button>
                @endif
            </div>

            @if (session('message_type'))
                <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
            @endif

            <div class="alert alert-danger mt-2 {{ $list->isNotEmpty() ? 'd-none' : '' }}" id="no_records">No records found.</div>

            <div class="row row-cols-1 row-cols-lg-2 g-2 my-1 {{ $list->isEmpty() ? 'd-none' : '' }}" id="address_list">
                <div class="col col-lg-6 d-none" id="item_template">
                    <div class="d-flex align-items-start p-2 h-100 bg-light">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">label</i>
                                <span class="ms-1 item_label"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">person</i>
                                <span class="ms-1 item_contact_person"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">textsms</i>
                                <span class="ms-1 item_contact_number"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">house</i>
                                <span class="ms-1 item_address_line"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">map</i>
                                <span class="ms-1 item_map_address"></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="material-icons fs-16">place</i>
                                <span class="ms-1 item_map_coordinates"></span>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <div class="visually-hidden item_id"></div>
                            <button class="btn btn-outline-dark btn-sm edit_address" data-bs-toggle="modal" data-bs-target="#modal_address">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons fs-16">edit</i>
                                    <span class="ms-1 small d-none d-lg-inline">Edit</span>
                                </div>
                            </button>
                            <button class="btn btn-outline-dark btn-sm delete_address" data-bs-toggle="modal" data-bs-target="#modal_delete_address">
                                <div class="d-flex align-items-center">
                                    <i class="material-icons fs-16">delete</i>
                                    <span class="ms-1 small d-none d-lg-inline">Delete</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                @if ($list->isNotEmpty())
                    @foreach ($list AS $address)
                        <div class="col col-lg-6" id="item_{{ $address->id }}">
                            <div class="d-flex align-items-start p-2 h-100 bg-light">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons fs-16">label</i>
                                        <span class="ms-1 item_label">{{ $address->label }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons fs-16">person</i>
                                        <span class="ms-1 item_contact_person">{{ $address->contact_person }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons fs-16">textsms</i>
                                        <span class="ms-1 item_contact_number">{{ $address->contact_number }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons fs-16">house</i>
                                        <span class="ms-1 item_address_line">{{ $address->address_line }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons fs-16">map</i>
                                        <span class="ms-1 item_map_address">{{ $address->map_address }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons fs-16">place</i>
                                        <span class="ms-1 item_map_coordinates">
                                            {{ substr_replace($address->map_coordinates, ' ', strpos($address->map_coordinates, ',') + 1, 0) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <div class="visually-hidden item_id">{{ $address->id }}</div>
                                    <button class="btn btn-outline-dark btn-sm edit_address" data-bs-toggle="modal" data-bs-target="#modal_address">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons fs-16">edit</i>
                                            <span class="ms-1 small d-none d-lg-inline">Edit</span>
                                        </div>
                                    </button>
                                    <button class="btn btn-outline-dark btn-sm delete_address" data-bs-toggle="modal" data-bs-target="#modal_delete_address">
                                        <div class="d-flex align-items-center">
                                            <i class="material-icons fs-16">delete</i>
                                            <span class="ms-1 small d-none d-lg-inline">Delete</span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_address" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title" id="modal-label">Set Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <form id="form_address">
                        <input type="hidden" name="id">
                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">label</i></span>
                                <input type="text" class="form-control form-select-sm" name="label" placeholder="Label">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">person</i></span>
                                <input type="text" class="form-control form-select-sm" name="contact_person" placeholder="Contact Person">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">textsms</i></span>
                                <input type="text" class="form-control form-select-sm" name="contact_number" placeholder="Contact Number">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">house</i></span>
                                <input type="text" class="form-control form-select-sm" name="address_line" placeholder="Address Line">
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">map</i></span>
                                <input type="text" class="form-control form-select-sm" name="map_address" placeholder="Map Address" readonly>
                            </div>
                            <div class="text-danger small field_error"></div>
                        </div>

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="material-icons fs-14">place</i></span>
                                <input type="text" class="form-control form-select-sm" name="map_coordinates" placeholder="Map Coordinates" readonly>
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
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary btn-sm" id="save_address">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_delete_address" data-bs-keyboard="false" data-bs-backdrop="static" tabindex="-1" aria-labelledby="modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title" id="modal-label">Delete Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="form_message"></div>

                    <div class="d-none" id="delete_id"></div>

                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">label</i>
                        <span class="ms-1" id="delete_label"></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">person</i>
                        <span class="ms-1" id="delete_contact_person"></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">textsms</i>
                        <span class="ms-1" id="delete_contact_number"></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">house</i>
                        <span class="ms-1" id="delete_address_line"></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">map</i>
                        <span class="ms-1" id="delete_map_address"></span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">place</i>
                        <span class="ms-1" id="delete_map_coordinates"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-outline-dark btn-sm" id="confirm_delete_address">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initMap" defer></script>
    <script defer>
        const el_no_records = document.getElementById('no_records');
        const el_address_list = document.getElementById('address_list');
        const el_modal_address = document.getElementById('modal_address');
        const el_modal_delete_address = document.getElementById('modal_delete_address');

        const el_modal_form_message = el_modal_address.querySelector('#form_message');
        const el_modal_id = el_modal_address.querySelector('[name="id"]');
        const el_modal_label = el_modal_address.querySelector('[name="label"]');
        const el_modal_contact_person = el_modal_address.querySelector('[name="contact_person"]');
        const el_modal_contact_number = el_modal_address.querySelector('[name="contact_number"]');
        const el_modal_address_line = el_modal_address.querySelector('[name="address_line"]');
        const el_modal_map_address = el_modal_address.querySelector('[name="map_address"]');
        const el_modal_map_coordinates = el_modal_address.querySelector('[name="map_coordinates"]');
        const el_modal_save_address = el_modal_address.querySelector('#save_address');

        const el_modal_delete_form_message = el_modal_delete_address.querySelector('#form_message');
        const el_modal_delete_id = el_modal_delete_address.querySelector('#delete_id');
        const el_modal_delete_label = el_modal_delete_address.querySelector('#delete_label');
        const el_modal_delete_contact_person = el_modal_delete_address.querySelector('#delete_contact_person');
        const el_modal_delete_contact_number = el_modal_delete_address.querySelector('#delete_contact_number');
        const el_modal_delete_address_line = el_modal_delete_address.querySelector('#delete_address_line');
        const el_modal_delete_map_address = el_modal_delete_address.querySelector('#delete_map_address');
        const el_modal_delete_map_coordinates = el_modal_delete_address.querySelector('#delete_map_coordinates');
        const el_modal_delete_confirm = el_modal_delete_address.querySelector('#confirm_delete_address');

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
                            el_modal_address.querySelector('[name="map_address"]').value =
                                response.data.results[0].formatted_address;
                            el_modal_address.querySelector('[name="map_coordinates"]').value =
                                gmap_lat + ',' + gmap_lng;
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

        el_modal_save_address.addEventListener('click', e => {
            e.preventDefault();
            el_modal_address.querySelector('.loading_spinner').classList.remove('d-none');

            const form_data = new FormData(el_modal_address.querySelector('#form_address'));

            el_modal_form_message.removeAttribute('class');
            el_modal_form_message.textContent = '';
            el_modal_address.querySelectorAll('.field_error').forEach(field => field.textContent = '');

            axios.post('{{ route('user.save-address', $user->id) }}', form_data)
                .then(response => {
                    const address = response.data;

                    if (form_data.get('id')) {
                        const item = el_address_list.querySelector('#item_' + form_data.get('id'));

                        item.querySelector('.item_label').textContent = address.label;
                        item.querySelector('.item_contact_person').textContent = address.contact_person;
                        item.querySelector('.item_contact_number').textContent = address.contact_number;
                        item.querySelector('.item_address_line').textContent = address.address_line;
                        item.querySelector('.item_map_address').textContent = address.map_address;
                        item.querySelector('.item_map_coordinates').textContent = address.map_coordinates;
                    } else {
                        el_address_list.classList.remove('d-none');
                        el_no_records.classList.remove('d-none');
                        el_no_records.classList.add('d-none');

                        const el_item_copy = el_address_list.querySelector('#item_template').cloneNode(true);
                        el_item_copy.setAttribute('id', 'item_' + address.id);
                        el_item_copy.classList.remove('d-none');

                        el_item_copy.querySelector('.item_id').textContent = address.id;
                        el_item_copy.querySelector('.item_label').textContent = address.label;
                        el_item_copy.querySelector('.item_contact_person').textContent = address.contact_person;
                        el_item_copy.querySelector('.item_contact_number').textContent = address.contact_number;
                        el_item_copy.querySelector('.item_address_line').textContent = address.address_line;
                        el_item_copy.querySelector('.item_map_address').textContent = address.map_address;
                        el_item_copy.querySelector('.item_map_coordinates').textContent = address.map_coordinates;

                        el_item_copy.querySelector('.edit_address').addEventListener('click', e => {
                            const item = el_item_copy.querySelector('.edit_address').parentNode.parentNode.parentNode;

                            setModalAddress({
                                id: item.querySelector('.item_id').textContent.trim(),
                                label: item.querySelector('.item_label').textContent.trim(),
                                contact_person: item.querySelector('.item_contact_person').textContent.trim(),
                                contact_number: item.querySelector('.item_contact_number').textContent.trim(),
                                address_line: item.querySelector('.item_address_line').textContent.trim(),
                                map_address: item.querySelector('.item_map_address').textContent.trim(),
                                map_coordinates: item.querySelector('.item_map_coordinates').textContent.replace(/\s+/g, ' ').trim(),
                            });
                        });

                        el_item_copy.querySelector('.delete_address').addEventListener('click', e => {
                            el_modal_delete_form_message.removeAttribute('class');
                            el_modal_delete_form_message.textContent = '';

                            const item = el_item_copy.querySelector('.delete_address').parentNode.parentNode.parentNode;

                            el_modal_delete_id.textContent = item.querySelector('.item_id').textContent.trim();
                            el_modal_delete_label.textContent = item.querySelector('.item_label').textContent.trim();
                            el_modal_delete_contact_person.textContent = item.querySelector('.item_contact_person').textContent.trim();
                            el_modal_delete_contact_number.textContent = item.querySelector('.item_contact_number').textContent.trim();
                            el_modal_delete_address_line.textContent = item.querySelector('.item_address_line').textContent.trim();
                            el_modal_delete_map_address.textContent = item.querySelector('.item_map_address').textContent.trim();
                            el_modal_delete_map_coordinates.textContent = item.querySelector('.item_map_coordinates').textContent.replace(/\s+/g, ' ').trim();
                        });

                        el_address_list.insertAdjacentElement('afterbegin', el_item_copy);
                    }

                    el_modal_address.querySelector('.loading_spinner').classList.add('d-none');
                    clearModalAddress();
                    bootstrap.Modal.getInstance(el_modal_address).hide();
                })
                .catch(error => {
                    const errors = error.response.data;

                    if (typeof errors === 'object') {
                        el_modal_label.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('label') ? errors.label : '';
                        el_modal_contact_person.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('contact_person') ? errors.contact_person : '';
                        el_modal_contact_number.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('contact_number') ? errors.contact_number : '';
                        el_modal_address_line.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('address_line') ? errors.address_line : '';
                        el_modal_map_address.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('map_address') ? errors.map_address : '';
                        el_modal_map_coordinates.parentNode.parentNode.querySelector('.field_error').textContent =
                            errors.hasOwnProperty('map_coordinates') ? errors.map_coordinates : '';
                    } else {
                        el_modal_form_message.setAttribute('class', 'alert alert-danger small');
                        el_modal_form_message.textContent = error.response.data.toString();
                    }

                    el_modal_address.querySelector('.loading_spinner').classList.add('d-none');
                });
        });

        el_address_list.querySelectorAll('.edit_address').forEach(btn => {
            btn.addEventListener('click', e => {
                const item = btn.parentNode.parentNode.parentNode;

                setModalAddress({
                    id: item.querySelector('.item_id').textContent.trim(),
                    label: item.querySelector('.item_label').textContent.trim(),
                    contact_person: item.querySelector('.item_contact_person').textContent.trim(),
                    contact_number: item.querySelector('.item_contact_number').textContent.trim(),
                    address_line: item.querySelector('.item_address_line').textContent.trim(),
                    map_address: item.querySelector('.item_map_address').textContent.trim(),
                    map_coordinates: item.querySelector('.item_map_coordinates').textContent.replace(/\s+/g, ' ').trim(),
                });
            });
        });

        function setModalAddress(address) {
            el_modal_id.value = address.hasOwnProperty('id') ? address.id : '';
            el_modal_label.value = address.hasOwnProperty('label') ? address.label : '';
            el_modal_contact_person.value = address.hasOwnProperty('contact_person') ? address.contact_person : '';
            el_modal_contact_number.value = address.hasOwnProperty('contact_number') ? address.contact_number : '';
            el_modal_address_line.value = address.hasOwnProperty('address_line') ? address.address_line : '';
            el_modal_map_address.value = address.hasOwnProperty('map_address') ? address.map_address : '';
            el_modal_map_coordinates.value = address.hasOwnProperty('map_coordinates') ? address.map_coordinates : '';

            if (address.hasOwnProperty('map_coordinates')) {
                [lat, lng] = address.map_coordinates.split(',').map(x => parseFloat(x));
                setGmapMarker(lat, lng);
            } else {
                setGmapMarker(null, null);
            }
        }

        function clearModalAddress() {
            setModalAddress({});
        }

        el_address_list.querySelectorAll('.delete_address').forEach(btn => {
            btn.addEventListener('click', e => {
                el_modal_delete_form_message.removeAttribute('class');
                el_modal_delete_form_message.textContent = '';

                const item = btn.parentNode.parentNode.parentNode;

                el_modal_delete_id.textContent = item.querySelector('.item_id').textContent.trim();
                el_modal_delete_label.textContent = item.querySelector('.item_label').textContent.trim();
                el_modal_delete_contact_person.textContent = item.querySelector('.item_contact_person').textContent.trim();
                el_modal_delete_contact_number.textContent = item.querySelector('.item_contact_number').textContent.trim();
                el_modal_delete_address_line.textContent = item.querySelector('.item_address_line').textContent.trim();
                el_modal_delete_map_address.textContent = item.querySelector('.item_map_address').textContent.trim();
                el_modal_delete_map_coordinates.textContent = item.querySelector('.item_map_coordinates').textContent.replace(/\s+/g, ' ').trim();
            });
        });

        el_modal_delete_confirm.addEventListener('click', e => {
            e.preventDefault();
            el_modal_delete_address.querySelector('.loading_spinner').classList.remove('d-none');

            const form_data = new FormData();
            form_data.append('id', parseInt(el_modal_delete_id.textContent));

            axios.post('{{ route('user.delete-address', $user->id) }}', form_data)
                .then(response => {
                    el_address_list.querySelector('#item_' + form_data.get('id')).remove();

                    el_modal_delete_address.querySelector('.loading_spinner').classList.add('d-none');
                    bootstrap.Modal.getInstance(el_modal_delete_address).hide();
                })
                .catch(error => {
                    el_modal_delete_form_message.setAttribute('class', 'alert alert-danger small');
                    el_modal_delete_form_message.textContent = error.response.data;

                    el_modal_delete_address.querySelector('.loading_spinner').classList.add('d-none');
                });
        });
    </script>
@endsection