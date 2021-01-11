@extends('layouts.app')
@section('page-title', 'Login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card mt-2 mt-md-5">
                    <div class="card-title">
                        <h3 class="text-center py-3">Login</h3>
                    </div>
                    <hr class="my-0">
                    <div class="card-body">
                        @if (session('message_type'))
                            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
                        @endif

                        <form method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
                                @error('email')
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

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('user.register') }}">Register</a>
                                <a href="{{ route('user.password-reset') }}">Forgot Password</a>
                            </div>

                            <div class="d-grid d-block mt-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>

                        <hr>

                        <div class="d-flex justify-content-center mt-3">
                            <a href="{{ route('google.login') }}">
                                <img src="{{ asset('google/1x/btn_google_signin_dark_normal_web.png') }}">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection