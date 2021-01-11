@section('page-title', $user->name.' - Delete Address')

<div class="row">
    <div class="col-12">
        <h5 class="mt-3">Are you sure you want to delete this address?</h5>
        <p class="mb-1">
            <span class="fw-bold">Label:</span>
            <span>{{ $address['label'] }}</span>
        </p>
        <p class="mb-1">
            <span class="fw-bold">Contact Person:</span>
            <span>{{ $address['contact_person'] }}</span>
        </p>
        <p class="mb-1">
            <span class="fw-bold">Contact Number:</span>
            <span>{{ $address['contact_number'] }}</span>
        </p>
        <p class="mb-1">
            <span class="fw-bold">Address:</span>
            <span>{{ $address['address'] }}</span>
        </p>
        <p class="mb-1">
            <span class="fw-bold">Map Coordinates:</span>
            <span>{{ $address['map_coordinates'] }}</span>
        </p>
        <p class="mb-1">
            <span class="fw-bold">Map Address:</span>
            <span>{{ $address['map_address'] }}</span>
        </p>

        <div class="row">
            <div class="col-12 text-end">
                <form method="POST">
                    @csrf
                    <a href="{{ route('user.address-book', $user->id) }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>