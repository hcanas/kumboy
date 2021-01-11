<p class="mb-1">
    <span class="fw-bold">Store Name:</span>
    <span>{{ $store['name'] }}</span>
</p>
<p class="mb-1">
    <span class="fw-bold">Contact Number:</span>
    <span>{{ $store['contact_number'] }}</span>
</p>
<p class="mb-1">
    <span class="fw-bold">Address:</span>
    <span>{{ $store['address'] }}</span>
</p>

<div style="width: 100%; height: 300px;" id="map"></div>

<p class="mb-1 mt-2">
    <span class="fw-bold">Map Address:</span>
    <span>{{ $store['map_address'] }}</span>
</p>
<p class="mb-1">
    <span class="fw-bold">Map Coordinates:</span>
    <span>{{ $store['map_coordinates'] }}</span>
</p>
<p class="mb-1">
    <span class="fw-bold">Attachment:</span>
    <a href="#" data-bs-toggle="modal" data-bs-target="#attachment-modal">View Attachment</a>
</p>
<p class="mb-1">
    <span class="fw-bold">Open Until:</span>
    <span>{{ date('Y-m-d', strtotime($store['open_until'])) }}</span>
</p>

<p class="mb-3">
    <span class="fw-bold">Transfer To:</span>
    <span>
        <a href="{{ route('user.activity-log', $store_transfer['target_id']) }}">{{ $store_transfer['target_name'] }}</a>
    </span>
</p>

<div class="modal fade" id="attachment-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="attachment-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="attachment-modal-label">Attachment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <embed src="{{ asset('storage/attachments/'.$request['store_transfer']['attachment']) }}" frameborder="0" width="100%" height="400px">
            </div>
        </div>
    </div>
</div>

<script>
    function initMap() {
        var coordinates = "{{ $store['map_coordinates'] }}";
        coordinates = coordinates.split(',');

        lat = parseFloat(coordinates[0]);
        lng = parseFloat(coordinates[1]);

        const address = { lat: lat, lng: lng };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 18,
            center: address,
            mapTypeId: google.maps.MapTypeId.HYBRID,
        });

        const marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map,
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initMap" defer></script>