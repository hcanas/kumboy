@section('page-title', $user->name.' - Address Book')

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center border-bottom mt-3 pb-2">
            <h4 class="my-0">Address Book</h4>
            @if (Auth::user()->id === $user->id)
                <a href="{{ route('user.add-address', $user->id) }}" class="btn btn-primary btn-sm">Add Address</a>
            @endif
        </div>

        @if (session('message_type'))
            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
        @endif

        @if ($addressBook->isEmpty())
            <div class="alert alert-danger mt-2">No records found.</div>
        @else
            @foreach ($addressBook AS $address)
                <div class="border-bottom py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="my-0">{{ $address->label }}</h5>
                        @if (Auth::user()->id === $user->id)
                            <div class="btn-group p-1">
                                <a href="{{ route('user.edit-address', [$user->id, $address->id]) }}" class="btn btn-primary btn-sm">Edit</a>
                                <a href="{{ route('user.delete-address', [$user->id, $address->id]) }}" class="btn btn-danger btn-sm">Delete</a>
                            </div>
                        @endif
                    </div>
                        <p class="small mb-1">
                            <span class="fst-italic">Contact Person :</span>
                            <span>{{ $address['contact_person'] }}</span>
                        </p>
                        <p class="small mb-1">
                            <span class="fst-italic">Contact Number :</span>
                            <span>{{ $address['contact_number'] }}</span>
                        </p>
                        <p class="small mb-1">
                            <span class="fst-italic">Address :</span>
                            <span>{{ $address['address'] }}</span>
                        </p>
                        <p class="small mb-1">
                            <span class="fst-italic">Map Coordinates :</span>
                            <span>{{ $address['map_coordinates'] }}</span>
                        </p>
                        <p class="small mb-1">
                            <span class="fst-italic">Map Address :</span>
                            <span>{{ $address['map_address'] }}</span>
                        </p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>