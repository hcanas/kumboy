@extends('layouts.app')
@section('page-title', 'Set Address')

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
                        <div class="wizard-line"></div>
                        <div>
                            <i class="material-icons fs-48">payment</i>
                        </div>
                        <div class="wizard-line"></div>
                        <div>
                            <i class="material-icons fs-48">check_circle_outline</i>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2">
                        <h4 class="border-bottom mt-3 py-2">Address Details</h4>

                        <div id="form_message"></div>

                        @auth
                            @if (count($address_book) > 0)
                                @foreach ($address_book AS $address)
                                    <div class="form-check mb-2">
                                        <input type="radio" class="form-check-input" name="radio" id="{{ $address->id }}">
                                        <label class="form-check-label">
                                            <div class="visually-hidden id">{{ $address->id }}</div>
                                            <div class="user_address_label">{{ $address->label }}</div>
                                            <div class="user_address_contact_person">{{ $address->contact_person }}</div>
                                            <div class="user_address_contact_number">{{ $address->contact_number }}</div>
                                            <div class="user_address_address">{{ $address->address }}</div>
                                            <div class="user_address_map_address">{{ $address->map_address }}</div>
                                            <div class="user_address_map_coordinates">{{ $address->map_coordinates }}</div>
                                        </label>
                                    </div>
                                @endforeach
                            @endif

                            <div class="form-check mb-2">
                                <input type="radio" class="form-check-input" name="radio" id="new_address" checked>
                                <label class="form-check-label">
                                    Use Different Address
                                </label>
                            </div>
                        @endauth

                        <form id="address_form">
                            @csrf

                            <input type="hidden" name="id" id="user_address_id">

                            <div class="mb-3">
                                <label for="label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control">
                                <div class="text-danger small field_error"></div>
                            </div>

                            <div class="mb-3">
                                <label for="label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control">
                                <div class="form-text">Number Format: 09xx-xxx-xxxx or 074-xxx-xxxx</div>
                                <div class="text-danger small field_error"></div>
                            </div>

                            <div class="mb-3">
                                <label for="address">Address</label>
                                <input type="text" name="address" class="form-control">
                                <div class="form-text">House No. / Building No. / Street Address</div>
                                <div class="text-danger small field_error"></div>
                            </div>

                            <div class="mb-3">
                                <div style="width: 100%; height: 300px;" id="map"></div>
                                <div class="form-text">
                                    Click on your location on the map to get additional location information.
                                    Please select a location within {{ implode('/', config('system.service_area')) }}.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="map_address">Map Address</label>
                                <input type="text" name="map_address" readonly class="form-control">
                                <div class="text-danger small field_error"></div>
                            </div>

                            <div class="mb-3">
                                <label for="map_address">Map Coordinates</label>
                                <input type="text" name="map_coordinates" readonly class="form-control">
                                <div class="text-danger small field_error"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <a href="{{ route('order.cart') }}" class="btn btn-secondary btn-sm d-flex justify-content-center align-items-center">
                                    <i class="material-icons">shopping_cart</i>
                                    <span class="ms-1">View Cart</span>
                                </a>
                                <button type="submit" class="btn btn-primary btn-sm d-flex justify-content-center align-items-center">
                                    <i class="material-icons">payment</i>
                                    <span class="ms-1">Proceed to Payment</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initMap" defer></script>
    <script>
        sessionStorage.setItem('order_address_status', '');
        const order_cart_status = sessionStorage.getItem('order_cart_status') ?? null;

        if (order_cart_status !== 'ok') {
            window.location = '{{ route('order.cart') }}';
        }

        let order_address = sessionStorage.getItem('order_address') ? JSON.parse(sessionStorage.getItem('order_address')) : null;
        const address_form = document.getElementById('address_form');
        const form_message = document.getElementById('form_message');
        const input_user_address_id = document.getElementById('user_address_id');
        const input_contact_person = document.getElementsByName('contact_person')[0];
        const input_contact_number = document.getElementsByName('contact_number')[0];
        const input_address = document.getElementsByName('address')[0];
        const input_map_address = document.getElementsByName('map_address')[0];
        const input_map_coordinates = document.getElementsByName('map_coordinates')[0];

        let map;
        let marker;
        let lat;
        let lng;

        if (order_address) {
            if (order_address.hasOwnProperty('user_address_id')) {
                let radio = document.querySelector('#\\3' + order_address.user_address_id) ?? null;

                if (radio) {
                    radio.checked = true;
                }
            }

            fillForm(order_address);
        }

        const input_radios = document.querySelectorAll('.form-check-input') ?? null;

        if (input_radios) {
            input_radios.forEach(radio => {
                radio.addEventListener('change', e => {
                    if (this.checked === true) {
                        clearFormErrors();

                        if (radio.getAttribute('id') === 'new_address') {
                            clearForm();
                        } else {
                            const label = radio.parentNode.querySelector('.form-check-label');

                            fillForm({
                                user_address_id: label.querySelector('.id').textContent,
                                contact_person: label.querySelector('.user_address_contact_person').textContent,
                                contact_number: label.querySelector('.user_address_contact_number').textContent,
                                address: label.querySelector('.user_address_address').textContent,
                                map_address: label.querySelector('.user_address_map_address').textContent,
                                map_coordinates: label.querySelector('.user_address_map_coordinates').textContent,
                            });
                        }
                    }
                });
            });
        }

        function clearForm()
        {
            input_user_address_id.value = '';
            input_contact_person.value = '';
            input_contact_number.value = '';
            input_address.value = '';
            input_map_address.value = '';
            input_map_coordinates.value = '';

            if (marker) {
                clearMapMarker();
            }
        }

        function fillForm(data)
        {
            input_user_address_id.value = data.user_address_id;
            input_contact_person.value = data.contact_person;
            input_contact_number.value = data.contact_number;
            input_address.value = data.address;
            input_map_address.value = data.map_address;
            input_map_coordinates.value = data.map_coordinates;
            [lat, lng] = data.map_coordinates.split(',').map(x => parseFloat(x));

            if (map) {
                setMapMarker(lat, lng);
            }
        }

        function clearFormErrors()
        {
            form_message.setAttribute('class', '');
            form_message.textContent = '';
            document.querySelectorAll('.field_error').forEach(field => {
                field.textContent = '';
            });
        }

        function initMap() {
            const baguio = { lat: 16.409447, lng: 120.599264 };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: (lat && lng) ? { lat: lat, lng: lng } : baguio,
                mapTypeId: google.maps.MapTypeId.HYBRID,
            });

            if (lat && lng) {
                setMapMarker(lat, lng);
            }

            map.addListener('click', e => {
                lat = e.latLng.toJSON().lat;
                lng = e.latLng.toJSON().lng;

                if (marker) {
                    clearMapMarker();
                }

                setMapMarker(lat, lng);

                // retrieve location details from Geocoding API
                axios.get('{{ env('GEOCODING_API_URL') }}/json?latlng=' + lat + ',' + lng + '&result_type=administrative_area_level_5|sublocality&key={{ env('GMAP_API_KEY') }}')
                    .then(response => {
                        if (response.data.status == 'OK') {
                            input_map_coordinates.value = lat + ',' + lng;
                            input_map_address.value = response.data.results[0].formatted_address;
                        } else {
                            alert('Unable to retrieve information.');
                        }
                    });
            });
        }

        function setMapMarker(lat, lng) {
            marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map,
            });

            map.panTo(new google.maps.LatLng(lat, lng));
        }

        function clearMapMarker()
        {
            marker.setMap(null);
        }

        address_form.addEventListener('submit', e => {
            e.preventDefault();

            clearFormErrors();

            let formData = new FormData(address_form);

            axios.post('{{ route('order.validate-address') }}', formData)
            .then(response => {
                sessionStorage.setItem('order_address', JSON.stringify({
                    user_address_id: input_user_address_id.value,
                    contact_person: input_contact_person.value,
                    contact_number: input_contact_number.value,
                    address: input_address.value,
                    map_address: input_map_address.value,
                    map_coordinates: input_map_coordinates.value,
                }));
                sessionStorage.setItem('order_address_status', 'ok');
                window.location = '{{ route('order.payment') }}';
            })
            .catch(error => {
                let errors = error.response.data;

                if (typeof errors === 'object') {
                    input_contact_person.parentNode.querySelector('.field_error').textContent = errors.hasOwnProperty('contact_person') ? errors.contact_person : '';
                    input_contact_number.parentNode.querySelector('.field_error').textContent = errors.hasOwnProperty('contact_number') ? errors.contact_number : '';
                    input_address.parentNode.querySelector('.field_error').textContent = errors.hasOwnProperty('address') ? errors.address : '';
                    input_map_address.parentNode.querySelector('.field_error').textContent = errors.hasOwnProperty('map_address') ? errors.map_address : '';
                    input_map_coordinates.parentNode.querySelector('.field_error').textContent = errors.hasOwnProperty('map_coordinates') ? errors.map_coordinates : '';
                } else {
                    form_message.setAttribute('class', 'alert alert-danger small');
                    form_message.textContent = errors.toString();
                }
            });
        });
    </script>
@endsection