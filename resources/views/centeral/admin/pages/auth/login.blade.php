@extends('centeral.admin.layouts.base_master')
@push('css')
    <link rel="stylesheet" href="{{ asset('dashboard-admin-assets/src/assets/css/light/authentication/auth-cover.css') }}">
@endpush
@section('main_content')
    <div class="auth-container d-flex">
        <div class="container mx-auto align-self-center">
            <div class="row">
                <div
                    class="col-6 d-lg-flex d-none h-100 my-auto top-0 start-0 text-center justify-content-center flex-column">
                    <div class="auth-cover-bg-image"></div>
                    <div class="auth-overlay"></div>
                    <div class="auth-cover">
                        <div class="position-relative">
                            <img src="{{ asset('dashboard-admin-assets/src/assets/img/auth-cover.svg') }}" alt="auth-img">
                        </div>
                    </div>
                </div>
                <div
                    class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center ms-lg-auto me-lg-0 mx-auto">
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <h2 class="text-center border-bottom pb-4 mb-0 border-light">Login</h2>
                                </div>
                                <form action="{{ route('central.login') }}" method="POST">
                                    @csrf
                                    @if (session()->has('error'))
                                        <p class="text-danger">{{ session()->get('error') }}</p>
                                    @endif
                                    {{-- @session('error')
                                        <p class="text-danger">{{ $message }}</p>
                                    @endsession --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" value="{{ old('username') }}" name="username" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-4">
                                            <button class="btn btn-primary w-100">Login</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
    {{-- <div class="auth-container d-flex">

        <div class="container mx-auto align-self-center">

            <div class="row">

                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center mx-auto">
                    <div class="card mt-3 mb-3">
                        <div class="card-body">
                            @session('error')
                                <div class="alert alert-danger" role="alert">
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endsession
                            <form action="{{ route('login') }}" method="post">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12 mb-3">

                                        <h2>Login</h2>
                                        <p>Enter your email and password to login</p>

                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="username" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <div class="form-check form-check-primary form-check-inline">
                                                <input class="form-check-input me-3" type="checkbox"
                                                    id="form-check-default">
                                                <label class="form-check-label" for="form-check-default">
                                                    Remember me
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-4">
                                            <button class="btn btn-secondary w-100">Login</button>
                                        </div>
                                    </div>




                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div> --}}
@endsection
