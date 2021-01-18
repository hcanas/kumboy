@extends('layouts.app')
@section('page-title', 'Registration')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card mt-5">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title text-center">Register</h4>
                    </div>
                    <div class="card-body">
                        @if (session('message_content'))
                            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
                        @endif

                        <form method="POST">
                            @csrf
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons">person</i></span>
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Name">
                                </div>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons">email</i></span>
                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" placeholder="Email">
                                </div>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons">dialpad</i></span>
                                    <input type="text" class="form-control" name="verification_code" value="{{ old('verification_code') }}" placeholder="Verification Code">
                                    <button class="btn btn-primary" type="button" id="send-code">Send</button>
                                </div>
                                @error('verification_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons">lock</i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Password">
                                </div>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons">check</i></span>
                                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                                </div>
                                @error('password_confirmation')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid d-block">
                                <button type="submit" class="btn btn-primary">REGISTER</button>
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

            axios.post('http://localhost:8080/users/send-email-verification-code', {
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
                        var errorMessage = '<div class="text-danger" id="email_error">' + error.response.data + '</div>';
                        document.getElementById('email').parentNode.insertAdjacentHTML('afterend', errorMessage);
                    } else {
                        alert('Server error.');
                    }
                });
        });
    </script>
@endsection