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
                                    {{-- <fieldset>
                                        <input name="payment_method" type="radio" id="coupon_code" value="coupon_code"
                                            required="" aria-invalid="false">
                                        <label for="coupon_code">Coupon Code</label>
                                    </fieldset> --}}
                                    <fieldset>
                                        <input name="payment_method" type="radio" id="wallet" value="wallet"
                                            aria-invalid="false">
                                        <label for="wallet">My Wallet</label>
                                    </fieldset>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="text-center">
                                <button class="btn btn-primary" id="payment-btn" type="submit">Continue</button>
                            </div>
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
                <form id="purchaseForm" method="POST"
                    action="{{ route('student_dashboard.live_lessons.enroll.store', 'coupon_code') }}">
                    @csrf
                    <div class="form-group">
                        <label>Purchase Code</label>
                        <input type="text" name="purchase_code" class="form-control mt-5">
                    </div>
                    <input type="hidden" id="LessonId" name="lesson_id" value="">
                    <input type="hidden" id="price_amount" name="price_amount" value="">
                </form>
            </div>
            <div class="modal-footer modal-footer-uniform">
                <button type="submit" form="purchaseForm" class="btn btn-success" style="width: 100%;">Unlock</button>
            </div>
        </div>
    </div>
</div>
@section('script')
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
                                form.action =
                                    `{{ route('student_dashboard.live_lessons.enroll.store', ':lesson') }}`
                                    .replace(
                                        ':lesson', $('#LessonId').val()
                                    );
                                const lessonId = document.createElement('input');
                                lessonId.type = 'hidden';
                                lessonId.name = 'lesson_id';
                                lessonId.value = $('#LessonId').val();
                                let paymentMethod = document.createElement('input');
                                paymentMethod.type = 'hidden';
                                paymentMethod.name = 'payment_method';
                                paymentMethod.value = 'wallet';

                                const token = document.createElement('input');
                                token.type = 'hidden';
                                token.name = '_token';
                                token.value = "{{ csrf_token() }}";
                                form.appendChild(lessonId);
                                form.appendChild(token);
                                form.appendChild(paymentMethod);

                                document.body.appendChild(form);
                                form.submit();
                            }
                        })
                        break;

                }
            })
            // $('.free_enrollment_btn').on('click', function(e) {
            //     e.preventDefault();
            //     const form = document.createElement('form');
            //     form.method = 'POST';
            //     form.action = `{{ route('student_dashboard.live_lessons.enroll.store', 'free') }}`;

            //     let paymentMethod = document.createElement('input');
            //     paymentMethod.type = 'hidden';
            //     paymentMethod.name = 'payment_method';
            //     paymentMethod.value = 'free';

            //     const token = document.createElement('input');
            //     token.type = 'hidden';
            //     token.name = '_token';
            //     token.value = "{{ csrf_token() }}";

            //     form.appendChild(token, paymentMethod, lessonId);

            //     document.body.appendChild(form);
            //     form.submit();
            // })
            $('.locked-btn').on('click', function() {
                var id = $(this).data('id');

                // Pass the id to the hidden input field with the specified ID
                $('#LessonId').val(id);
                $('#price_amount').val($(this).data('price'));
            });
        });
    </script>
@endsection
