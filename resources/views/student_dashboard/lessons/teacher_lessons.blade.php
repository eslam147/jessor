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
            height: 18rem !important;
            position: relative;
        }

        .no_image_available::before {
            content: "";
            position: absolute;
            background: url("{{ loadTenantMainAsset('logo1', global_asset('assets/logo.svg')) }}");
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
                <div class="row fx-element-overlay">
                    @foreach ($lessons as $row)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="border border-info box overflow-hidden">
                                <div class="fx-card-item">
                                    <div class="fx-card-avatar fx-overlay-1">
                                        <img src="{{ !empty($row->getRawOriginal('thumbnail')) ? $row->thumbnail : global_asset('images/no_image_available.jpg') }}"
                                            alt="{{ $row->name }}"
                                            class="bbsr-0 {{ empty($row->getRawOriginal('thumbnail')) ? 'no_image_available' : '' }} bber-0 lesson_image object-fit-cover">
                                        <div class="fx-overlay scrl-up">
                                            <ul class="fx-info">
                                                <li>
                                                    <a class="btn btn-outline"
                                                        href="{{ route('student_dashboard.lesson.show', $row->id) }}">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    @if (!empty($row->studentActiveEnrollment))
                                                        <a class="btn btn-outline"
                                                            href="{{ route('topics.show', $row->id) }}">
                                                            <i class="fa fa-folder-open"></i>
                                                        </a>
                                                    @else
                                                        <a href="javascript:void(0)" class="locked-btn btn btn-outline"
                                                            data-id="{{ $row->id }}" data-price="{{ $row->price }}"
                                                            data-bs-toggle="modal" data-bs-target="#payment-methods"
                                                            data-animation="shake">
                                                            <i class="fa fa-shopping-cart"></i>
                                                        </a>
                                                    @endif
                                                </li>
                                                {{-- <li>
                                                    <a class="btn btn-outline" href="javascript:void(0);">
                                                        <i class="mdi mdi-settings"></i>
                                                    </a>
                                                </li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="fx-card-content text-start">
                                        <div class="product-text">
                                      
                                            <h4 class="box-title mb-0">
                                                <a
                                                    href="{{ route('student_dashboard.lesson.show', $row->id) }}">{{ $row->name }}</a>
                                            </h4>
                                            <small class="text-muted db">{{ str($row->description)->limit(30) }}</small>
                                        </div>
                                        <hr>
                                        <div>
                                            <h3 class="pro-price px-10 text-blue text-end">
                                                {{ $row->is_lesson_free ? 'Free' : number_format($row->price, 2) }}
                                            </h3>
                                        </div>
                                        <!-- Enrollment and Actions -->
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
    <!-- Modal -->
    @include('student_dashboard.lessons.partials.purchase_lessons_modal')
    <!-- /.modal -->
@endsection
