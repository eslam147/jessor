@extends('student_dashboard.layout.app')
@section('style')
    <style>
        /* From Uiverse.io by Na3ar-17 */
        .radio-input {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .radio-input * {
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        .radio-input label {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 0px 20px;
            width: 220px;
            cursor: pointer;
            height: 50px;
            position: relative;
        }

        .radio-input label::before {
            position: absolute;
            content: "";
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            height: 45px;
            z-index: -1;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-radius: 10px;
            border: 2px solid transparent;
        }

        .radio-input label:hover::before {
            transition: all 0.2s ease;
            background-color: #2a2e3c;
        }

        .radio-input .label:has(input:checked)::before {
            background-color: #2d3750;
            border-color: #435dd8;
            height: 50px;
        }

        .radio-input .label .text {
            color: #black;
        }

        .radio-input .label input[type="radio"] {
            background-color: #202030;
            appearance: none;
            width: 17px;
            height: 17px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .radio-input .label input[type="radio"]:checked {
            background-color: #435dd8;
            -webkit-animation: puls 0.7s forwards;
            animation: pulse 0.7s forwards;
        }

        .radio-input .label input[type="radio"]:before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 50%;
            transition: all 0.1s cubic-bezier(0.165, 0.84, 0.44, 1);
            background-color: black;
            transform: scale(0);
        }

        .radio-input .label input[type="radio"]:checked::before {
            transform: scale(1);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        .bg-green {
            background-color: #6AC977;
            color: #ffffff;
            border-radius: 15px;
        }

        .bg-green-br {
            border-color: #6ac977;
            color: #7E8FC1;
            background-color: transparent;
            border-radius: 15px;
        }

        .lesson_image {
            width: 100%;
            height: 18rem;
            position: relative;
        }

        .no_image_available::before {
            content: "";
            position: absolute;
            background: url("{{ settingByType('logo1') ? tenant_asset(settingByType('logo1')) : global_asset('assets/logo.svg') }}");
            z-index: 99;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 65%;
            width: 100%;
            background-size: cover;
            background-position: center;
            backdrop-filter: grayscale(4);
        }

        .no_image_available::after {
            content: "";
            position: absolute;
            width: 100%;
            background: #efefef;
            z-index: 9;
            top: 0;
            left: 0;
            height: 100%;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    @foreach ($lessons as $row)
                        <div class="col-xs-12 col-md-6 col-lg-3">
                            <div class="box pull-up">
                                <div
                                    class="box-img-top position-relative {{ empty($row->thumbnail) ? 'no_image_available' : '' }}">
                                    <img class="lesson_image object-fit-cover"
                                        src="{{ $row->thumbnail ? $row->thumbnail : global_asset('images/no_image_available.jpg') }}"
                                        alt="{{ $row->name }}">
                                </div>
                                <div class="box-body">
                                    <p class="mb-0 fs-18 text-bold"> {{ $row->name }} </p>
                                    <div class="d-flex justify-content-between mt-30">
                                        <div>
                                            <p class="mb-0 text-fade">Lesson Description</p>
                                            <p class="mb-0">{{ $row->description }}</p>
                                        </div>
                                        <div>
                                            @if ($row->is_lesson_free)
                                                <span class="text-success">Lesson Is Free</span>
                                            @else
                                                <h6>Price :
                                                    <span class="text-primary">{{ $row->price }}</span>
                                                </h6>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($row->is_lesson_free)
                                        <div class="bg-primary mt-5 rounded">
                                            <a href="javascript:void(0)">
                                                <h5 class="text-white text-center p-10"> Enroll Now For Free </h5>
                                            </a>
                                        </div>
                                    @else
                                        @if ($row->enrollments_count > 0)
                                            <div class="bg-success mt-5 rounded">
                                                <a href="{{ route('topics.show', $row->id) }}">
                                                    <h5 class="text-white text-center p-10"> View Lesson </h5>
                                                </a>
                                            </div>
                                        @else
                                            <div class="bg-primary mt-5 rounded">
                                                <a href="javascript:void(0)" class="locked-btn"
                                                    data-id="{{ $row->id }}" data-price="{{ $row->price }}"
                                                    data-bs-toggle="modal" data-bs-target="#payment-methods"
                                                    data-animation="shake">
                                                    <h5 class="text-white text-center  p-10"> Locked Lesson </h5>
                                                </a>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
    <!-- Modal -->
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
                                        <fieldset>
                                            <input name="payment_method" type="radio" id="wallet" value="wallet"
                                                aria-invalid="false">
                                            <label for="wallet">My Wallet</label>
                                        </fieldset>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <hr>
                                <button class="btn btn-primary" id="payment-btn" type="submit">Complete</button>
                            </div>

                        </div>
                        {{-- <div class="radio-input">
                            <label class="label">
                                <input type="radio" id="value-1" checked="" name="value-radio" value="value-1" />
                                <p class="text">Designer</p>
                            </label>
                            <label class="label">
                                <input type="radio" id="value-2" name="value-radio" value="value-2" />
                                <p class="text">Student</p>
                            </label>
                            <label class="label">
                                <input type="radio" id="value-3" name="value-radio" value="value-3" />
                                <p class="text">Teacher</p>
                            </label>
                        </div> --}}
                    </div>

                    {{-- <div class="form-group">
                        <label>Purchase Code</label>
                        <input type="text" name="purchase_code" class="form-control mt-5">
                    </div> --}}
                    {{-- <form id="purchaseForm" method="POST" action="{{ route('enroll.store') }}">
                        @csrf
                     
                        <input type="hidden" id="LessonId" name="lesson_id" value="">
                    </form> --}}
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
                    <form id="purchaseForm" method="POST" action="{{ route('enroll.store','coupon_code') }}">
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
                    <button type="submit" form="purchaseForm" class="btn btn-success"
                        style="width: 100%;">Unlock</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Attach click event listener to buttons with class 'locked-btn'
            $('#payment-btn').on('click', function(e) {
                e.preventDefault();
                let paymentSelect = $('input[name="payment_method"]:checked').val();
                let lessonPrice = $('#price_amount').val();
                // if()
                switch (paymentSelect) {
                    case 'coupon_code':
                        $('#payment-methods').modal('hide');
                        $('#modal-center').modal('show');
                        // $('#LessonId').val(id);
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
                                form.action = `{{ route('enroll.store','wallet') }}`;
                                const lessonId = document.createElement('input');
                                lessonId.type = 'hidden';
                                lessonId.name = 'lesson_id';
                                // lessonId.name = 'lesson_id';
                                lessonId.value = $('#LessonId').val();

                                const token = document.createElement('input');
                                token.type = 'hidden';
                                token.name = '_token';
                                token.value = "{{ csrf_token() }}";
                                
                                form.appendChild(token);
                                form.appendChild(lessonId);
                                // form.appendChild(submitButton);
                                document.body.appendChild(form);
                                form.submit();
                            }
                        })
                        break;

                }

            })
            $('.locked-btn').on('click', function() {
                // Get the data-id attribute from the clicked button
                var id = $(this).data('id');

                // Pass the id to the hidden input field with the specified ID
                $('#LessonId').val(id);
                $('#price_amount').val($(this).data('price'));
            });
        });
    </script>
@endsection
