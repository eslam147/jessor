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
                                    <img src="{{ settingByType('logo2') ? url(Storage::url(settingByType('logo2'))) : url('assets/logo.svg') }}"
                                        alt="logo">
                                </div>
                                <form action="{{ route('signup.store') }}" method="POST" class="pt-3 row">
                                    @csrf
                                    <div class="form-group col-6">
                                        <label>{{ __('class') . ' ' . __('section') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="class_section_id" id="class_section" class="form-control ">
                                            <option value="">
                                                {{ __('select') . ' ' . __('class') . ' ' . __('section') }}</option>
                                            @foreach ($class_section as $section)
                                                <option value="{{ $section->id }}">{{ $section->class->name }} -
                                                    {{ $section->section->name }} {{ $section->class->medium->name }}
                                                    {{ $section->class->streams->name ?? ' ' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label> {{ __('category') }} <span class="text-danger">*</span></label>
                                        <select name="category_id" class="form-control">
                                            <option value="">{{ __('select') . ' ' . __('category') }}</option>
                                            @foreach ($category as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{ __('first_name') }}</label>
                                        <input id="fisrt_name" type="text" class="form-control form-control-lg"
                                            name="first_name" required autofocus>
                                        @error('first_name')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{ __('last_name') }}</label>
                                        <input id="last_name" type="text" class="form-control form-control-lg"
                                            name="last_name" required autofocus>
                                        @error('last_name')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{ __('email') }}</label>
                                        <input id="email" type="email" class="form-control form-control-lg"
                                            name="email" value="{{ old('email') }}" required autocomplete="email"
                                            autofocus placeholder="{{ __('email') }}">
                                        @error('email')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-12">
                                        <label>{{ __('password') }}</label>
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
                                        @error('password')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    @if (Route::has('login'))
                                        <div class="my-2 d-flex justify-content-end align-items-center col-12">
                                            <a class="auth-link text-info" href="{{ route('login') }}">
                                                {{ __('already_have_account') }}
                                            </a>
                                        </div>
                                    @endif
                                    <div class="mt-3 col-12">
                                        <input type="submit" name="btnlogin" id="login_btn"
                                            value="{{ __('register') }}"
                                            class="btn btn-block btn-theme btn-lg font-weight-medium auth-form-btn" />
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
    @include('sweetalert::alert')
    <script src="{{ asset('/assets/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.js') }}"></script>

    <script type='text/javascript'>
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
            text: '{{ Session::get('error') }}',
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
