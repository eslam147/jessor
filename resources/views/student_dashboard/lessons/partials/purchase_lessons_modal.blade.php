<div class="modal center-modal fade" id="payment-methods" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Methods <i class="si-lock si"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="payment_methods">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group validate">
                                <div class="controls">
                                    <fieldset>
                                        <input name="payment_method" type="radio" id="coupon_code" value="coupon_code"
                                            required="" aria-invalid="false">
                                        <label for="coupon_code">Coupon Code</label>
                                    </fieldset>
                                    @if(auth()->check() && auth()->user()->student)
                                        <fieldset>
                                            <input name="payment_method" type="radio" id="wallet" value="wallet"
                                                aria-invalid="false">
                                            <label for="wallet">My Wallet</label>
                                        </fieldset>
                                    @endif
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <hr>
                            <button class="btn btn-primary" id="payment-btn" type="submit">Complete</button>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal center-modal fade" id="modal-center" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lesson Is Locked <i class="si-lock si"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="purchaseForm" method="POST" action="{{ route('enroll.store', 'coupon_code') }}">
                    @csrf
                    <div class="row">
                        @if(!auth()->check())
                            <div class="form-group col-6">
                                <label>{{ __('first_name') }}</label>
                                <input id="fisrt_name" type="text" class="form-control form-control-lg"
                                    name="first_name" value="{{ old('first_name') }}" autofocus>
                            </div>
                            <div class="form-group col-6">
                                <label>{{ __('last_name') }}</label>
                                <input id="last_name" type="text" class="form-control form-control-lg"
                                    name="last_name" value="{{ old('last_name') }}" autofocus>
                            </div>
                            <div class="form-group col-12">
                                <label> {{ __('mobile') }} <span class="text-danger">*</span></label>
                                <input type="tel" value="{{ old('mobile') }}" name="mobile" class="form-control">
                            </div>
                            <div class="form-group col-12">
                                <label>{{ __('email') }}</label>
                                <input id="email" type="email" class="form-control form-control-lg"
                                    name="email" value="{{ old('email') }}" autocomplete="email"
                                    autofocus placeholder="{{ __('email') }}">
                            </div>
                            <div class="form-group col-12">
                                <label>{{ __('password') }}</label>
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control form-control-lg"
                                        name="password" autocomplete="current-password"
                                        placeholder="{{ __('password') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-eye-slash" id="togglePassword"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group col-12">
                            <label>Purchase Code</label>
                            <input type="text" name="purchase_code" class="form-control">
                        </div>
                    </div>
                    <input type="hidden" id="LessonId" name="lesson_id" value="">
                    <input type="hidden" id="price_amount" name="price_amount" value="">
                    <input type="hidden" id="class_section_id" name="class_section_id" value="">
                </form>
            </div>
            <div class="modal-footer modal-footer-uniform">
                <button type="submit" form="purchaseForm" class="btn btn-success" style="width: 100%;">Unlock</button>
            </div>
        </div>
    </div>
</div>
@section('script')
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
    <script src="{{ global_asset('student/assets/icons/feather-icons/feather.min.js') }}"></script>
    <script
        src="{{ global_asset('student/assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup.min.js') }}">
    </script>
    <script
        src="{{ global_asset('student/assets/vendor_components/Magnific-Popup-master/dist/jquery.magnific-popup-init.js') }}">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#payment-btn').on('click', function(e) {
                e.preventDefault();

                let paymentSelect = $('input[name="payment_method"]:checked').val();
                let lessonPrice = $('#price_amount').val();
                let class_section_id = $('#class_section_id').val();
                switch (paymentSelect) {
                    case 'coupon_code':
                        $('#payment-methods').modal('hide');
                        $('#modal-center').modal('show');
                        break;
                    case 'wallet':
                        $('#payment-methods').modal('hide');
                        Swal.fire({
                            title: "Are you sure?",
                            text: `The amount will be Decrase ${lessonPrice} from your wallet.`,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, Pay it!",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#payment-methods').modal('hide');
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `{{ route('enroll.store', 'wallet') }}`;
                                const lessonId = document.createElement('input');
                                lessonId.type = 'hidden';
                                lessonId.name = 'lesson_id';
                                lessonId.value = $('#LessonId').val();
                                const token = document.createElement('input');
                                token.type = 'hidden';
                                token.name = '_token';
                                token.value = "{{ csrf_token() }}";
                                form.appendChild(token);
                                form.appendChild(lessonId);
                                document.body.appendChild(form);
                                form.submit();
                            }
                        })
                        break;
                }
            })
            $('.free_enrollment_btn').on('click', function(e) {
                e.preventDefault();
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('enroll.store', 'free') }}`;
                const lessonId = document.createElement('input');
                lessonId.type = 'hidden';
                lessonId.name = 'lesson_id';
                lessonId.value = $(this).data('id');

                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = "{{ csrf_token() }}";
                form.appendChild(token);
                form.appendChild(lessonId);

                document.body.appendChild(form);
                form.submit();
            })
            $('body').on('click','.locked-btn', function(e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                $('#LessonId').val(id);
                $('#price_amount').val($(this).attr('data-price'));
                $('#class_section_id').val($(this).attr('data-class-section-id'));
            });
        });
    </script>
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
@endsection
