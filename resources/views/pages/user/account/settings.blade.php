@extends('pages.user.profile')
@section('page-title', $user->name.' - Account Settings')

@section('profile-content')
    @can('update', $user)
        <div class="row">
            <div class="col-12">
                <h4 class="border-bottom mt-3 mb-0 pb-2">Account Settings</h4>

                @if (session('message_type'))
                    <div class="alert alert-{{ session('message_type') }} small mt-2">{{ session('message_content') }}</div>
                @endif

                <h6 class="border-bottom mt-3 pb-2">Name</h6>
                <form id="change-name-form" action="{{ route('user.change-name', [$user->id]) }}" method="POST">
                    @csrf
                    <div class="my-2">
                        <div class="input-group">
                            <span class="input-group-text"><i class="material-icons fs-16">person</i></span>
                            <input type="text" class="form-control form-control-sm" name="name" value="{{ $user->name }}">
                        </div>
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="my-2">
                        <button type="submit" class="btn btn-primary btn-sm" id="save-name">Change Name</button>
                    </div>
                </form>

                <h6 class="border-bottom mt-3 pb-2">Email</h6>
                <p>
                    @php $pos = strpos($user->email, '@'); @endphp
                    {{ substr_replace($user->email, str_repeat('*', $pos - 4), 4, $pos - 4) }}
                </p>

                <h6 class="border-bottom mt-3 pb-2">Password</h6>
                <form id="change-password-form" action="{{ route('user.change-password', [$user->id]) }}" method="POST">
                    @csrf

                    <div class="my-2">
                        <div class="input-group">
                            <span class="input-group-text"><i class="material-icons fs-16">lock</i></span>
                            <input type="password" class="form-control form-control-sm" name="password" placeholder="New Password">
                        </div>
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="my-2">
                        <div class="input-group">
                            <span class="input-group-text"><i class="material-icons fs-16">check</i></span>
                            <input type="password" class="form-control form-control-sm" name="password_confirmation" placeholder="Confirm New Password">
                        </div>
                        @error('password_confirmation')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="material-icons fs-16">dialpad</i></span>
                            <input type="text" class="form-control form-control-sm" name="verification_code" value="{{ old('verification_code') }}" placeholder="Verification Code">
                            <button class="btn btn-outline-dark btn-sm" type="button" id="send-code">Send Code</button>
                        </div>
                        @error('verification_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm">Change Password</button>
                </form>

                <h6 class="border-bottom mt-3 pb-2">Roles</h6>
                <p><span class="badge rounded-pill bg-dark text-white p-2">{{ ucfirst(str_replace('-', ' ', $user->role)) }}</span></p>
            </div>
        </div>

        <script>
            var btnSendCode = document.getElementById('send-code');

            btnSendCode.addEventListener('click', function (e) {
                e.preventDefault();

                axios.post('{{ route('user.request-password-reset-code', $user->id) }}', {})
                    .then(function (response) {
                        console.log(response.data);
                        // disable button for 60 seconds
                        var btn = document.getElementById('send-code');
                        btn.disabled = true;

                        var ctr = 60;
                        timer();

                        function timer() {
                            setTimeout(function () {
                                if (ctr === 1) {
                                    btn.disabled = false;
                                    btn.innerHTML = 'Send Code';
                                } else if (ctr > 1) {
                                    ctr--;
                                    btn.innerHTML = 'Resend in ' + ctr;
                                    timer();
                                }
                            }, 1000);
                        }
                    })
                    .catch(function (error) {
                        var errorMessage = '<div class="alert alert-danger" id="email_error">' + error.response.data + '</div>';
                        document.getElementById('change-password-form').insertAdjacentHTML('afterbegin', errorMessage);
                    });
            });
        </script>
    @endcan
@endsection