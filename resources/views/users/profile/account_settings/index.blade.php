@section('page-title', $user->name.' - Account Settings')

@can('viewAccountSettings', $user)
    <div class="row">
        <div class="col-12">
            <h4 class="border-bottom mt-3 mb-0 pb-2">Account Settings</h4>

            @if (session('message_type'))
                <div class="alert alert-{{ session('message_type') }} mt-2">{{ session('message_content') }}</div>
            @endif

            <h6 class="border-bottom mt-3 pb-2">Name</h6>
            <form id="change-name-form" action="{{ route('user.change-name', [$user->id]) }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                    <button type="submit" class="btn btn-primary" id="save-name">Change Name</button>
                </div>
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </form>

            <h6 class="border-bottom mt-3 pb-2">Email</h6>
            <p>{{ $user->email }}</p>

            <h6 class="border-bottom mt-3 pb-2">Password</h6>
            <form id="change-password-form" action="{{ route('user.change-password', [$user->id]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" name="password">
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" name="password_confirmation">
                    @error('password_confirmation')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="verification_code" class="form-label">Verification Code</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="verification_code" value="{{ old('verification_code') }}">
                        <button class="btn btn-primary" type="button" id="send-code">Send Code</button>
                    </div>
                    @error('verification_code')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>

            <h6 class="border-bottom mt-3 pb-2">Roles</h6>
            <p><span class="badge rounded-pill bg-secondary p-2">{{ ucfirst(str_replace('-', ' ', $user->role)) }}</span></p>
        </div>
    </div>

    <script>
        var btnSendCode = document.getElementById('send-code');

        btnSendCode.addEventListener('click', function (e) {
            e.preventDefault();

            axios.post('http://localhost:8080/users/{{ $user->id }}/send-password-reset-code', {})
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