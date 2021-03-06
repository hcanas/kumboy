@extends('layouts.app')
@section('page-title', 'Forgot Password')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card mt-5">
                    <div class="card-body">
                        <h4 class="card-title text-center py-3">Account Recovery</h4>

                        <hr>

                        @if (session('message_content'))
                            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
                        @endif

                        <form method="POST">
                            @csrf

                            <div class="my-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons fs-16">email</i></span>
                                    <input type="email" class="form-control form-control-sm" name="email" id="email" value="{{ old('email') }}" placeholder="Email">
                                </div>
                                <div class="text-danger" id="email_error">
                                    @error('email')
                                    {{ $message }}
                                    @enderror
                                </div>
                            </div>

                            <div class="my-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons fs-16">dialpad</i></span>
                                    <input type="text" class="form-control form-control-sm" name="verification_code" value="{{ old('verification_code') }}" placeholder="Verification Code">
                                    <button class="btn btn-outline-dark btn-sm" type="button" id="send-code">Send Code</button>
                                </div>
                                @error('verification_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="my-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons fs-16">lock</i></span>
                                    <input type="password" class="form-control form-control-sm" name="password" placeholder="Password">
                                </div>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="my-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons fs-16">check</i></span>
                                    <input type="password" class="form-control form-control-sm" name="password_confirmation" placeholder="Confirm Password">
                                </div>
                                @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid d-block my-3">
                                <button type="submit" class="btn btn-primary btn-sm">CONTINUE</button>
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