@extends('layouts.app')
@section('page-title', 'Forgot Password')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card mt-2 mt-md-5">
                    <div class="card-title py-3 mb-0">
                        <h4 class="text-center">Password Reset</h4>
                    </div>
                    <hr class="my-0">
                    <div class="card-body">
                        @if (session('message_content'))
                            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
                        @endif

                        <form method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
                                <div class="text-danger" id="email_error"></div>
                                @error('email')
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

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation">
                                @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid d-block">
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </div>
                        </form>

                        <hr>

                        <div class="text-center">
                            <a href="{{ route('login') }}">Return to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('send-code').addEventListener('click', function (e) {
            e.preventDefault();

            document.getElementById('email_error').innerText = '';

            axios.post('http://localhost:8080/users/send-password-reset-code', {
                email: document.getElementById('email').value
            })
                .then(function (response) {
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
                    if (error.response.status === 400) {
                        document.getElementById('email_error').innerText = error.response.data.toString();
                    } else {
                        alert('Server error.');
                    }
                });
        });
    </script>
@endsection