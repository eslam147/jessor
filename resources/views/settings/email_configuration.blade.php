@extends('layouts.master')

@section('title')
    {{ __('email_configuration') }}
@endsection


@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('email_configuration') }}
            </h3>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-5">
                            {{ __('add_email_configuration') }}
                        </h4>
                        <form id="formdata" class="general-setting" action="{{ url('email-settings') }}" method="POST"
                            novalidate="novalidate">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('mail_mailer') }}</label>
                                    <select required name="mail_mailer" value="{{ $settings['mail_mailer'] }}"
                                        class="form-control select2" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">--- Select Mailer ---</option>
                                        <option @selected($settings['mail_mailer'] == 'smtp') value="smtp">SMTP</option>
                                        <option @selected($settings['mail_mailer'] == 'mailgun') value="mailgun">Mailgun</option>
                                        <option @selected($settings['mail_mailer'] == 'sendmail') value="sendmail">sendmail</option>
                                        <option @selected($settings['mail_mailer'] == 'postmark') value="postmark">Postmark</option>
                                        <option @selected($settings['mail_mailer'] == 'amazon_ses') value="amazon_ses">Amazon SES</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('mail_host') }}</label>
                                    <input name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}"
                                        type="text" required placeholder="{{ __('mail_host') }}" class="form-control" />
                                </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('mail_port') }}</label>
                                    <input name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}"
                                        type="text" required placeholder="{{ __('mail_port') }}" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('mail_username') }}</label>
                                    <input name="mail_username"
                                        value="{{ old('mail_username', $settings['mail_username']) }}" type="text"
                                        required placeholder="{{ __('mail_username') }}" class="form-control" />
                                </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('mail_password') }}</label>
                                    <div class="input-group">
                                        <input id="password" name="mail_password"
                                            value="{{ old('mail_password', $settings['mail_password']) }}" type="password"
                                            required placeholder="{{ __('mail_password') }}" class="form-control" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa fa-eye-slash" id="togglePassword"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('mail_encryption') }}</label>
                                    <input name="mail_encryption"
                                        value="{{ old('mail_encryption', $settings['mail_encryption']) }}" type="text"
                                        required placeholder="{{ __('mail_encryption') }}" class="form-control" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('mail_send_from') }}</label>
                                    <input name="mail_send_from"
                                        value="{{ old('mail_send_from', $settings['mail_send_from']) }}" type="text"
                                        required placeholder="{{ __('mail_send_from') }}" class="form-control" />
                                </div>
                            </div>

                            <input class="btn btn-theme" type="submit" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row grid-margin">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-5">
                            {{ __('email_configuration_verification') }}
                        </h4>
                        <form class="verify_email" action="{{ route('setting.varify-email-config') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>{{ __('email') }}</label>
                                    <input name="verify_email" type="email" required placeholder="{{ __('email') }}"
                                        class="form-control" />
                                </div>
                                <div class="form-group col-px-md-5">
                                    <input class="btn btn-theme m-4" type="submit" value="Verify">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
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
@endsection
