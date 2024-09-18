<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('register') }} || {{ config('app.name') }}</title>

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
                                    <img src="{{ loadTenantMainAsset('logo2', global_asset('assets/logo.svg')) }}"
                                        alt="logo">
                                </div>
                                <form action="{{ route('signup.store') }}" method="POST" id="signup-form"
                                    class="pt-3 row">
                                    @csrf
                                    <div class="form-group col-6">
                                        <label>{{ __('class') . ' ' . __('section') }} <span
                                                class="text-danger">*</span></label>
                                        <select name="class_section_id" id="class_section" class="form-control ">
                                            <option value="">
                                                {{ __('select') . ' ' . __('class') . ' ' . __('section') }}</option>
                                            @isset($classSections)
                                                @foreach ($classSections as $section)
                                                    <option @selected(old('class_section_id') == $section->id) value="{{ $section->id }}">
                                                        {{ optional($section->class)->name }} -
                                                        {{ optional($section->class->medium)->name }}
                                                        {{ optional($section->section)->name }}
                                                        {{ optional($section->class->streams)->name ?? ' ' }}
                                                    </option>
                                                @endforeach
                                            @endisset
                                        </select>
                                    </div>
                                    <div class="form-group col-6">
                                        <label> {{ __('mobile') }} <span class="text-danger">*</span></label>
                                        <input type="tel" value="{{ old('mobile') }}" maxlength="11" minlength="11"
                                            id="mobile" name="mobile" class="form-control">
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{ __('first_name') }}</label>
                                        <input id="fisrt_name" type="text" class="form-control form-control-lg"
                                            name="first_name" value="{{ old('first_name') }}" required autofocus>
                                        @error('first_name')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{ __('last_name') }}</label>
                                        <input id="last_name" type="text" class="form-control form-control-lg"
                                            name="last_name" value="{{ old('last_name') }}" required autofocus>
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
                                        <button type="submit" name="btnlogin" id="login_btn"
                                            class="btn btn-block btn-theme btn-lg font-weight-medium auth-form-btn">
                                            {{ __('register') }}
                                        </button>
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
    <script src="{{ url('assets/js/vendor.bundle.base.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>

    <script type='text/javascript'>
        $(document).ready(function() {
            $.validator.addMethod("egyptPhone", function(value, element) {
                return this.optional(element) || /^01[0-2,5]{1}[0-9]{8}$/.test(value);
            }, `{{ __('validation.phone', ['attribute' => 'mobile number']) }}`);

            $('#signup-form').validate({
                rules: {
                    class_section_id: {
                        required: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        egyptPhone: true,
                        minlength: 11,
                        maxlength: 11

                    },
                    first_name: {
                        required: true,
                        minlength: 2
                    },
                    last_name: {
                        required: true,
                        minlength: 2
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    class_section_id: {
                        required: `{{ __('validation.required', ['attribute' => 'Class Section']) }}`
                    },
                    mobile: {
                        required: `{{ __('validation.required', ['attribute' => 'mobile number']) }}`,
                        digits: `{{ __('validation.digits', ['attribute' => 'mobile number']) }}`,
                        egyptPhone: `{{ __('validation.custom.mobile.egyptPhone', ['attribute' => 'mobile number']) }}`,
                        minlength: `{{ __('validation.min.string', ['attribute' => 'mobile number', 'min' => 11]) }}`,
                        maxlength: `{{ __('validation.max.string', ['attribute' => 'mobile number', 'max' => 11]) }}`
                    },
                    first_name: {
                        required: `{{ __('validation.required', ['attribute' => 'first name']) }}`,
                        minlength: `{{ __('validation.min.string', ['attribute' => 'first name', 'min' => 2]) }}`
                    },
                    last_name: {
                        required: `{{ __('validation.required', ['attribute' => 'last name']) }}`,
                        minlength: `{{ __('validation.min.string', ['attribute' => 'last name', 'min' => 2]) }}`
                    },
                    email: {
                        required: `{{ __('validation.required', ['attribute' => 'email']) }}`,
                        email: `{{ __('validation.email', ['attribute' => 'email']) }}`
                    },
                    password: {
                        required: `{{ __('validation.required', ['attribute' => 'password']) }}`,
                        minlength: `{{ __('validation.min.string', ['attribute' => 'password', 'min' => 6]) }}`
                    }
                },
                errorElement: 'p',
                errorClass: 'text-danger',
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
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#mobile').val(convertToArabicNumerals($('#mobile').val()));
                    form.submit();
                }

            });
        });

        $("#signup-form").submit(function() {
            if ($(this).valid()) {
                $('#login_btn').html(`<i class="fa fa-spinner fa-spin"></i> {{ __('send') }}`);
                $('#login_btn').attr('disabled', 'disabled');
            }
            return true;
        });

        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
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
            text: `{{ Session::get('error') }}`,
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
