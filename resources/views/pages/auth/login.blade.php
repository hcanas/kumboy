@extends('layouts.app')
@section('page-title', 'Login')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card mt-5">
                    <div class="card-body">
                        <h3 class="card-title text-center py-3">Login</h3>

                        <hr>

                        @if (session('message_type'))
                            <div class="alert alert-{{ session('message_type') }}">{{ session('message_content') }}</div>
                        @endif

                        <form method="POST">
                            @csrf
                            <div class="my-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="material-icons fs-16">email</i></span>
                                    <input type="email" class="form-control form-control-sm" name="email" id="email" value="{{ old('email') }}" placeholder="Email">
                                </div>
                                @error('email')
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

                            <div class="d-flex justify-content-between my-3">
                                <a href="{{ route('user.register') }}">Register</a>
                                <a href="{{ route('user.password-reset') }}">Forgot Password</a>
                            </div>

                            <div class="d-grid d-block my-3">
                                <button type="submit" class="btn btn-primary btn-sm">LOGIN</button>
                            </div>
                        </form>

                        <hr>

                        <div class="py-3">
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('google.login') }}">
                                    <img src="{{ asset('google/1x/btn_google_signin_dark_normal_web.png') }}">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection