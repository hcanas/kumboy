@extends('pages.user.profile')
@section('page-title', $user->name.' - '.$request->ref_no.' Request Details')

@section('profile-content')
    <div class="d-flex flex-column align-items-center flex-lg-row justify-content-lg-between my-3">
        <h4 class="text-black-50 my-0">
            {{ ucwords(str_replace('_', ' ', $request->category)).' Application' }}
        </h4>
        <span class="text-black-50">
            {{ $request->ref_no }} &ndash; {{ date('M j, Y h:iA', strtotime($request->created_at)) }}
        </span>
    </div>

    <div id="system_message"></div>

    @php
        $badge = [
            'pending' => 'secondary',
            'accepted' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'dark',
        ];
    @endphp
    <div class="d-flex align-items-center">
        <span class="badge bg-{{ $badge[$request->status] }} p-2" id="status_badge">
            {{ ucwords($request->status) }}
        </span>
        @if (in_array(Auth::user()->role, ['superadmin', 'admin']) AND $request->evaluated_by !== null AND $request->status !== 'cancelled')
            <span class="ms-1 small">
                by
                <a href="{{ route('user.activity-log', $request->evaluated_by) }}">{{ $request->evaluator_name }}</a>
            </span>
        @endif
    </div>

    @if ($request->category === 'store_transfer')
        <p class="d-flex flex-column flex-lg-row align-items-lg-center my-2">
            <span class="d-flex align-items-center">
                <i class="material-icons fs-16">double_arrow</i>
                <a href="{{ route('user.activity-log', $request->recipient->id) }}" class="ms-1">{{ $request->recipient->name }}</a>
            </span>
        </p>
    @endif
    <p class="d-flex flex-column flex-lg-row align-items-lg-center my-2">
        <span class="d-flex align-items-center">
            <i class="material-icons fs-16">store</i>
            <span class="ms-1" id="store_name">{{ $request->store->name }}</span>
        </span>
        @if ($request->category === 'update_store')
            <span class="text-muted ms-1 small">(currently {{ $request->latest->name }})</span>
        @endif
    </p>
    <p class="d-flex flex-column flex-lg-row align-items-lg-center my-2">
        <span class="d-flex align-items-center">
            <i class="material-icons fs-16">textsms</i>
            <span class="ms-1">{{ $request->store->contact_number }}</span>
        </span>
        @if ($request->category === 'update_store')
            <span class="text-muted ms-1 small">(currently {{ $request->latest->contact_number }})</span>
        @endif
    </p>
    <p class="d-flex flex-column flex-lg-row align-items-lg-center my-2">
        <span class="d-flex align-items-center">
            <i class="material-icons fs-16">house</i>
            <span class="ms-1">{{ $request->store->address_line }}</span>
        </span>
        @if ($request->category === 'update_store')
            <span class="text-muted ms-1 small">(currently {{ $request->latest->address_line }})</span>
        @endif
    </p>

    <p class="d-flex flex-column flex-lg-row align-items-lg-center my-2 mt-2">
        <span class="d-flex align-items-center">
            <i class="material-icons fs-16">map</i>
            <span class="ms-1">{{ $request->store->map_address }}</span>
        </span>
        @if ($request->category === 'update_store')
            <span class="text-muted ms-1 small">(currently {{ $request->latest->map_address }})</span>
        @endif
    </p>
    <p class="d-flex flex-column flex-lg-row align-items-lg-center my-2">
        <span class="d-flex align-items-center">
            <i class="material-icons fs-16">place</i>
            <span class="ms-1">{{ $request->store->map_coordinates }}</span>
        </span>
        @if ($request->category === 'update_store')
            <span class="text-muted ms-1 small">(currently {{ $request->latest->map_coordinates }})</span>
        @endif
    </p>

    <div style="width: 100%; height: 300px;" id="gmap_container"></div>

    <p class="d-flex flex-column flex-lg-row align-items-lg-center my-2">
        <span class="d-flex align-items-center">
            <i class="material-icons fs-16">calendar_today</i>
            <span class="ms-1">{{ date('Y-m-d', strtotime($request->store->open_until)) }}</span>
        </span>
        @if ($request->category === 'update_store')
            <span class="text-muted ms-1 small">(currently {{ date('Y-m-d', strtotime($request->latest->open_until)) }})</span>
        @endif
    </p>
    <p class="d-flex align-items-center mt-2 mb-3">
        <i class="material-icons fs-16">file_present</i>
        <a href="{{ asset('storage/stores/attachments/'.$request->store->attachment) }}" class="ms-1" target="_blank">View Attachment</a>
    </p>

    @if ($request->status === 'pending')
        <div class="mb-3">
            @can('evaluate', $request)
                <button type="button" class="btn btn-primary btn-sm" id="accept_application">
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">check</i>
                        <span class="ms-1">ACCEPT</span>
                    </div>
                </button>
                <button type="button" class="btn btn-outline-dark btn-sm" id="reject_application">
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">cancel</i>
                        <span class="ms-1">REJECT</span>
                    </div>
                </button>
            @endcan
            @can('cancel', $request)
                <button type="button" class="btn btn-outline-dark btn-sm" id="cancel_application">
                    <div class="d-flex align-items-center">
                        <i class="material-icons fs-16">cancel</i>
                        <span class="ms-1">CANCEL</span>
                    </div>
                </button>
            @endcan
        </div>
    @endif

    <div class="modal fade" id="modal_confirmation" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="attachment-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="d-flex justify-content-center align-items-center w-100 h-100 position-absolute bg-light opacity-80 frontmost d-none loading_spinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="modal-header">
                    <h5 class="modal-title" id="attachment-modal-label">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="modal_error"></div>
                    <div id="modal_question"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary btn-sm" id="confirm">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GMAP_API_KEY') }}&callback=initMap" defer></script>
    <script defer>
        function initMap() {
            const coordinates = ("{{ $request->store->map_coordinates }}").split(',');

            const gmap_lat = parseFloat(coordinates[0]);
            const gmap_lng = parseFloat(coordinates[1]);

            const gmap = new google.maps.Map(document.getElementById("gmap_container"), {
                zoom: 18,
                center: { lat: gmap_lat, lng: gmap_lng },
                mapTypeId: google.maps.MapTypeId.HYBRID,
            });

            new google.maps.Marker({
                position: { lat: gmap_lat, lng: gmap_lng },
                map: gmap,
            });
        }
    </script>

    <!-- Button Actions -->
    <script defer>
        const el_status_badge = document.getElementById('status_badge');
        const el_system_message = document.getElementById('system_message');
        const store_name = document.getElementById('store_name').textContent;
        const el_accept_application = document.getElementById('accept_application');
        const el_reject_application = document.getElementById('reject_application');
        const el_cancel_application = document.getElementById('cancel_application');
        const el_modal_confirmation = document.getElementById('modal_confirmation');

        if (el_accept_application) {
            el_accept_application.addEventListener('click', e => {
                e.preventDefault();

                el_modal_confirmation.querySelector('#modal_error').textContent = '';
                el_modal_confirmation.querySelector('#modal_error').removeAttribute('class');
                el_modal_confirmation.querySelector('#modal_question').textContent = 'Accept application for "'
                    + store_name + '" store?';

                const modal = new bootstrap.Modal(el_modal_confirmation);
                modal.show();

                el_modal_confirmation.querySelector('#confirm').addEventListener('click', e => {
                    e.preventDefault();
                    el_modal_confirmation.querySelector('.loading_spinner').classList.remove('d-none');

                    axios.post('{{ route('user.accept-store-request', [$request->user_id, $request->ref_no]) }}', {})
                        .then(response => {
                            el_system_message.textContent = response.data;
                            el_system_message.setAttribute('class', 'alert alert-success small');
                            el_status_badge.classList.replace('bg-secondary', 'bg-success');
                            el_status_badge.textContent = 'Accepted';
                            el_status_badge.parentElement.insertAdjacentHTML('beforeend', '<span class="ms-1 small">'
                                + 'by '
                                + '<a href="{{ route('user.activity-log', Auth::id()) }}">'
                                + '{{ Auth::user()->name }}</a>'
                                +'</span>');
                            el_accept_application.classList.add('d-none');
                            el_reject_application.classList.add('d-none');
                            el_modal_confirmation.querySelector('.loading_spinner').classList.add('d-none');
                            modal.hide();
                        })
                        .catch(error => {
                            console.log(error.response);
                            el_modal_confirmation.querySelector('#modal_error').textContent = error.response.data;
                            el_modal_confirmation.querySelector('#modal_error').setAttribute('class', 'alert alert-danger small');

                            el_modal_confirmation.querySelector('.loading_spinner').classList.add('d-none');
                        });
                });
            });
        }

        if (el_reject_application) {
            el_reject_application.addEventListener('click', e => {
                e.preventDefault();

                el_modal_confirmation.querySelector('#modal_error').textContent = '';
                el_modal_confirmation.querySelector('#modal_error').removeAttribute('class');
                el_modal_confirmation.querySelector('#modal_question').textContent = 'Reject application for "'
                    + store_name + '" store?';

                const modal = new bootstrap.Modal(el_modal_confirmation);
                modal.show();

                el_modal_confirmation.querySelector('#confirm').addEventListener('click', e => {
                    e.preventDefault();
                    el_modal_confirmation.querySelector('.loading_spinner').classList.remove('d-none');

                    axios.post('{{ route('user.reject-store-request', [$request->user_id, $request->ref_no]) }}', {})
                        .then(response => {
                            el_system_message.textContent = response.data;
                            el_system_message.setAttribute('class', 'alert alert-success small');
                            el_status_badge.classList.replace('bg-secondary', 'bg-danger');
                            el_status_badge.textContent = 'Rejected';
                            el_status_badge.parentElement.insertAdjacentHTML('beforeend', '<span class="ms-1 small">'
                                + 'by '
                                + '<a href="{{ route('user.activity-log', Auth::id()) }}">'
                                + '{{ Auth::user()->name }}</a>'
                                +'</span>');
                            el_accept_application.classList.add('d-none');
                            el_reject_application.classList.add('d-none');
                            el_modal_confirmation.querySelector('.loading_spinner').classList.add('d-none');
                            modal.hide();
                        })
                        .catch(error => {
                            el_modal_confirmation.querySelector('#modal_error').textContent = error.response.data;
                            el_modal_confirmation.querySelector('#modal_error').setAttribute('class', 'alert alert-danger small');

                            el_modal_confirmation.querySelector('.loading_spinner').classList.add('d-none');
                        });
                });
            });
        }

        if (el_cancel_application) {
            el_cancel_application.addEventListener('click', e => {
                e.preventDefault();

                el_modal_confirmation.querySelector('#modal_error').textContent = '';
                el_modal_confirmation.querySelector('#modal_error').removeAttribute('class');
                el_modal_confirmation.querySelector('#modal_question').textContent = 'Cancel application for "'
                    + store_name + '" store?';

                const modal = new bootstrap.Modal(el_modal_confirmation);
                modal.show();

                el_modal_confirmation.querySelector('#confirm').addEventListener('click', e => {
                    e.preventDefault();
                    el_modal_confirmation.querySelector('.loading_spinner').classList.remove('d-none');

                    axios.post('{{ route('user.cancel-store-request', [$request->user_id, $request->ref_no]) }}', {})
                        .then(response => {
                            el_system_message.textContent = response.data;
                            el_system_message.setAttribute('class', 'alert alert-success small');
                            el_status_badge.classList.replace('bg-secondary', 'bg-dark');
                            el_status_badge.textContent = 'Cancelled';
                            el_cancel_application.classList.add('d-none');
                            el_modal_confirmation.querySelector('.loading_spinner').classList.add('d-none');
                            modal.hide();
                        })
                        .catch(error => {
                            console.log(error);
                            el_modal_confirmation.querySelector('#modal_error').textContent = error.response.data;
                            el_modal_confirmation.querySelector('#modal_error').setAttribute('class', 'alert alert-danger small');

                            el_modal_confirmation.querySelector('.loading_spinner').classList.add('d-none');
                        });
                });
            });
        }
    </script>
@endsection