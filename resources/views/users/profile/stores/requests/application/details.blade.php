<p class="mb-1">
    <span class="fw-bold">Store Name:</span>
    <span>
        {{ $store_application['name'] }}
        @if (empty($store_original) === false AND $store_application['name'] !== $store_original['name'])
            {{ '(currently '.$store_original['name'].')' }}
        @endif
    </span>
</p>
<p class="mb-1">
    <span class="fw-bold">Contact Number:</span>
    <span>
        {{ $store_application['contact_number'] }}
        @if (empty($store_original) === false AND $store_application['contact_number'] !== $store_original['contact_number'])
            {{ '(currently '.$store_original['contact_number'].')' }}
        @endif
    </span>
</p>
<p class="mb-1">
    <span class="fw-bold">Address:</span>
    <span>
        {{ $store_application['address'] }}
        @if (empty($store_original) === false AND $store_application['address'] !== $store_original['address'])
            {{ '(currently '.$store_original['address'].')' }}
        @endif
    </span>
</p>

<div style="width: 100%; height: 300px;" id="map"></div>

<p class="mb-1 mt-2">
    <span class="fw-bold">Map Address:</span>
    <span>
        {{ $store_application['map_address'] }}
        @if (empty($store_original) === false AND $store_application['map_address'] !== $store_original['map_address'])
            {{ '(currently '.$store_original['map_address'].')' }}
        @endif
    </span>
</p>
<p class="mb-1">
    <span class="fw-bold">Map Coordinates:</span>
    <span>
        {{ $store_application['map_coordinates'] }}
        @if (empty($store_original) === false AND $store_application['map_coordinates'] !== $store_original['map_coordinates'])
            {{ '(currently '.$store_original['map_coordinates'].')' }}
        @endif
    </span>
</p>
<p class="mb-1">
    <span class="fw-bold">Attachment:</span>
    <a href="#" data-bs-toggle="modal" data-bs-target="#attachment-modal">View Attachment</a>
</p>
<p class="mb-3">
    <span class="fw-bold">Open Until:</span>
    <span>
        {{ date('Y-m-d', strtotime($store_application['open_until'])) }}
        @if (empty($store_original) === false AND $store_application['open_until'] !== $store_original['open_until'])
            {{ '(currently '.date('Y-m-d', strtotime($store_original['open_until'])).')' }}
        @endif
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
                <embed src="{{ asset('storage/attachments/'.$request['store_application']['attachment']) }}" frameborder="0" width="100%" height="400px">
            </div>
        </div>
    </div>
</div>

<script>
    function initMap() {
        var coordinates = "{{ $store_application['map_coordinates'] }}";
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