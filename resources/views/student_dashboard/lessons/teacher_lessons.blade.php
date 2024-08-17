@extends('student_dashboard.layout.app')
@section('style')
    <style>
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
                        <div class="col-xs-12 col-lg-3">
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
                                                    data-id="{{ $row->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#modal-center" data-animation="shake">
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
    <div class="modal center-modal fade" id="modal-center" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lesson Is Locked <i class="si-lock si"></i></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="purchaseForm" method="POST" action="{{ route('enroll.store') }}">
                        @csrf
                        <div class="form-group">
                            <label>Purchase Code</label>
                            <input type="text" name="purchase_code" class="form-control mt-5">
                        </div>
                        <input type="hidden" id="LessonId" name="lesson_id" value="">
                    </form>
                </div>
                <div class="modal-footer modal-footer-uniform">
                    <button type="submit" form="purchaseForm" class="btn btn-success" style="width: 100%;">Unlock</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->
    @include('sweetalert::alert')
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            // Attach click event listener to buttons with class 'locked-btn'
            $('.locked-btn').on('click', function() {
                // Get the data-id attribute from the clicked button
                var id = $(this).data('id');

                // Pass the id to the hidden input field with the specified ID
                $('#LessonId').val(id);
            });
        });
    </script>
@endsection
