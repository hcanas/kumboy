@section('page-title', $user->name.' - Request Details')

<div class="row">
    <div class="col-12">
        <h4 class="border-bottom mt-3 pb-2">Request Details</h4>

        @if (session('message_type'))
            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
        @endif

        <p class="mb-1">
            <span class="fw-bold">Ref#:</span>
            <span>{{ $request['code'] }}</span>
        </p>
        <p class="mb-1">
            <span class="fw-bold">Date:</span>
            <span>{{ $request['created_at'] }}</span>
        </p>
        <p class="mb-1">
            <span class="fw-bold">Type:</span>
            <span>{{ ucwords(str_replace('_', ' ', $request['type'])) }}</span>
        </p>
        <p class="mb-3">
            <span class="fw-bold">Status:</span>
            <span>
                {{ ucwords($request['status']) }}

                @if (in_array($request['status'], ['approved', 'rejected']) AND preg_match('/admin/i', Auth::user()->role))
                    by
                    <a href="{{ route('user.activity-log', $request['evaluated_by']) }}">{{ $request['evaluator_name'] }}</a>
                @endif
            </span>
        </p>

        @includeWhen(in_array($request['type'], ['store creation', 'store update']), 'users.profile.stores.requests.application.details', [
            'store_application' => $request['store_application'],
            'store_original' => $request['type'] === 'store update' ? $request['store_original'] : [],
        ])

        @includeWhen($request['type'] === 'store transfer', 'users.profile.stores.requests.transfer.details', [
            'store_transfer' => $request['store_transfer'],
            'store' => $request['store'],
        ])

        <div class="mb-3 d-flex justify-content-between">
            @if (Auth::user()->id === $request['user_id'] AND strtolower($request['status']) === 'pending')
                <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancel-dialog">Cancel Request</a>
            @elseif (in_array(strtolower(Auth::user()->role), ['superadmin', 'admin']) AND strtolower($request['status']) === 'pending')
                <a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#reject-dialog">Reject Request</a>
                <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approve-dialog">Approve Request</a>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="cancel-dialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Are you sure you want to cancel this request?</h5>
                <form method="POST" action="{{ route('user.cancel-store-request', [$user->id, $request['code']]) }}">
                    @csrf
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Go Back</button>
                        <button type="submit" class="btn btn-danger btn-sm">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reject-dialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Are you sure you want to reject this request?</h5>
                <form method="POST" action="{{ route('user.reject-store-request', [$user->id, $request['code']]) }}">
                    @csrf
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Go Back</button>
                        <button type="submit" class="btn btn-danger btn-sm">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approve-dialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5>Are you sure you want to approve this request?</h5>
                <form method="POST" action="{{ route('user.approve-store-request', [$user->id, $request['code']]) }}">
                    @csrf
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Go Back</button>
                        <button type="submit" class="btn btn-success btn-sm">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>