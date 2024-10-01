<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('login') }} || {{ config('app.name') }}</title>

    @include('layouts.include')

</head>

<body class="bg-image">
    <div class="container-scroller">
        {{-- <div class="bg-image"> --}}
        <div class="overlay">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-4 col-lg-7"></div>
                    <div class="col-md-8 col-lg-4">
                        <div class="auth-content-wrapper auth">
                            <div class="auth-form-light text-left p-5">

                                <div class="brand-logo text-center">
                                    <img src="{{ loadTenantMainAsset('logo2', url('assets/logo.svg')) }}"
                                        alt="logo">
                                </div>
                                <form action="{{ route('auth.login') }}" id="frmLogin" method="POST" class="pt-3">
                                    @csrf
                                    <input type="hidden" name="visitor_id" id="visitor_id" value="">
                                    <div class="form-group">
                                        <label>{{ __('email') }}</label>
                                        {{-- <input type="text" name="username" required class="form-control form-control-lg" placeholder="{{__('username')}}"> --}}
                                        <input id="email" type="email" class="form-control form-control-lg"
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            autofocus placeholder="{{ __('email') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('password') }}</label>
                                        {{-- <input type="password" name="password" required class="form-control form-control-lg" placeholder="{{__('password')}}"> --}}

                                        <div class="input-group">
                                            <input id="password" type="password" class="form-control form-control-lg"
                                                name="password" required autocomplete="current-password"
                                                placeholder="{{ __('password') }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fa fa-eye-slash" id="togglePassword"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4">
                                        <div class="form-group form-check">
                                            <input type="checkbox" class="form-check-input" id="remember"
                                                name="remember">
                                            <label class="form-check-label" for="remember">Remember Me</label>
                                        </div>
                                    </div>

                                    @if (Route::has('auth.password.request'))
                                        <div class="my-2 d-flex justify-content-end align-items-center">
                                            <a class="auth-link text-black"
                                                href="{{ route('auth.password.request') }}">
                                                {{ __('forgot_password') }}
                                            </a>
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        <button type="submit" name="btnlogin" id="login_btn"
                                            class="btn btn-block btn-theme btn-lg font-weight-medium auth-form-btn">{{ __('login') }}</button>

                                    </div>
                                </form>


                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>

                <!-- content-wrapper ends -->
            </div>
            <!-- page-body-wrapper ends -->
        </div>
        {{-- </div> --}}
    </div>

    <script src="{{ url('assets/js/vendor.bundle.base.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>

    <script type='text/javascript'>
        const fpPromise = import('https://openfpcdn.io/fingerprintjs/v4')
            .then(FingerprintJS => FingerprintJS.load());
        $("#frmLogin").validate({
            rules: {
                username: "required",
                password: "required",
            },
            success: function(label, element) {
                $(element).parent().removeClass('has-danger')
                $(element).removeClass('form-control-danger')
            },
            errorPlacement: function(label, element) {
                if (label.text()) {
                    if ($(element).attr("name") == "password") {
                        label.insertAfter(element.parent()).addClass('text-danger mt-2');
                    } else {
                        label.addClass('mt-2 text-danger');
                        label.insertAfter(element);
                    }
                }
            },
            highlight: function(element, errorClass) {
                $(element).parent().addClass('has-danger')
                $(element).addClass('form-control-danger')
            }
        });
        $('#frmLogin').submit(function(e) {
            if ($(this).valid()) {
                fpPromise
                    .then(fp => fp.get())
                    .then(result => $('#visitor_id').val(result.visitorId));
                $('#login_btn').html(`<i class="fa fa-spinner fa-spin"></i> {{ __('login') }}`);
                $('#login_btn').attr('disabled', 'disabled');
            }
        })
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // this.classList.toggle("fa-eye");
            if (password.getAttribute("type") === 'password') {
                $('#togglePassword').addClass('fa-eye-slash');
                $('#togglePassword').removeClass('fa-eye');
            } else {
                $('#togglePassword').removeClass('fa-eye-slash');
                $('#togglePassword').addClass('fa-eye');
            }
        });


    </script>
</body>

@if (Session::has('error'))
    <script type='text/javascript'>
        $.toast({
            text: "{{ Session::get('error') }}",
            showHideTransition: 'slide',
            icon: 'error',
            loaderBg: '#f2a654',
            position: 'top-right'
        });
    </script>
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <script type='text/javascript'>
            $.toast({
                text: '{{ $error }}',
                showHideTransition: 'slide',
                icon: 'error',
                loaderBg: '#f2a654',
                position: 'top-right'
            });
        </script>
    @endforeach
@endif

</html>
