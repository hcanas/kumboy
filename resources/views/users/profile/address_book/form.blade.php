@section('page-title', $user->name.' - Add Address')

<div class="row">
    <div class="col-12">
        <h4 class="border-bottom mt-3 py-2">{{ $form_title }}</h4>

        @if (session('message_type'))
            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
        @endif

        <form id="add-address-form" method="POST">
            @csrf

            <div class="mb-3">
                <label for="label">Label</label>
                <input type="text" name="label" class="form-control" value="{{ isset($form_data['label']) ? $form_data['label'] : old('label') }}">
                @error('label')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="label">Contact Person</label>
                <input type="text" name="contact_person" class="form-control" value="{{ isset($form_data['contact_person']) ? $form_data['contact_person'] : old('contact_person') }}">
                @error('contact_person')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="{{ isset($form_data['contact_number']) ? $form_data['contact_number'] : old('contact_number') }}">
                <div class="form-text">Number Format: 09xx-xxx-xxxx or 074-xxx-xxxx</div>
                @error('contact_number')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" value="{{ isset($form_data['address']) ? $form_data['address'] : old('address') }}">
                <div class="form-text">House No. / Building No. / Street Address</div>
                @error('address')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
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
                <input type="text" name="map_address" readonly class="form-control" value="{{ isset($form_data['map_address']) ? $form_data['map_address'] : old('map_address') }}">
                @error('map_address')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="map_address">Map Coordinates</label>
                <input type="text" name="map_coordinates" readonly class="form-control" value="{{ isset($form_data['map_coordinates']) ? $form_data['map_coordinates'] : old('map_coordinates') }}">
                @error('map_coordinates')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid d-sm-block mb-3">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    var map;
    var marker;
    var inputMapCoordinates = document.getElementsByName('map_coordinates')[0];
    var inputMapAddress = document.getElementsByName('map_address')[0];
    var lat;
    var lng;

    var oldLatLng = "{{ isset($form_data['map_coordinates']) ? $form_data['map_coordinates'] : old('map_coordinates') }}";

    if (oldLatLng) {
        oldLatLng = oldLatLng.split(',');

        lat = parseFloat(oldLatLng[0]);
        lng = parseFloat(oldLatLng[1]);
    }

    function initMap() {
        const baguio = { lat: 16.409447, lng: 120.599264 };
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 16,
            center: oldLatLng ? { lat: lat, lng: lng } : baguio,
            mapTypeId: google.maps.MapTypeId.HYBRID,
        });

        if (oldLatLng) {
            marker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map,
            });
        }

        map.addListener('click', function (e) {
            if (marker) {
                marker.setMap(null);
            }

            marker = new google.maps.Marker({
                position: e.latLng,
                map,
            });

            lat = e.latLng.toJSON().lat;
            lng = e.latLng.toJSON().lng;

            // retrieve location details from Geocoding API
            axios.get('{{ env('GEOCODING_API_URL') }}/json?latlng=' + lat + ',' + lng + '&result_type=administrative_area_level_5|sublocality&key={{ env('GMAP_API_KEY') }}')
                .then(function (response) {
                    if (response.data.status == 'OK') {
                        inputMapCoordinates.value = lat + ',' + lng;
                        inputMapAddress.value = response.data.results[0].formatted_address;
                    } else {
                        alert('Unable to retrieve information.');
                    }
                });
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initMap" defer></script>