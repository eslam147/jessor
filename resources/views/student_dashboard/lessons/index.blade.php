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
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    @foreach ($lessons as $row)
                        <div class="col-12 col-lg-4">
                            <div class="box pull-up">
                                <img @class([
                                    "box-img-top",
                                    'no_image_available' => empty($row->thumbnail)
                                ])
                                    src="{{ $row->thumbnail ? tenant_asset($row->thumbnail) : global_asset('images/no_image_available.jpg') }}"
                                    alt="Card image cap">
                                <div class="box-body">

                                    <p class="mb-0 fs-18"> {{ $row->name }} </p>
                                    <div class="d-flex justify-content-between mt-30">
                                        <div>
                                            <p class="mb-0 text-fade">Description</p>
                                            <p class="mb-0">{{ $row->description }}</p>
                                    </div>
                                        <div>
                                            @if ($row->is_lesson_free)
                                                <span class="text-success">Lesson Is Free</span>
                                            @else
                                                {{ $row->price }}
                                            @endif
                                        </div>
                                    </div>
                                    @if ($row->enrollments_count > 0)
                                        <div class="bg-primary mt-5 rounded">
                                            <a href="{{ route('topics.show', $row->id) }}">
                                                <h5 class="text-white text-center p-10"> Start Now </h5>
                                            </a>
                                        </div>
                                    @else
                                        @if ($row->is_lesson_free)
                                            <div class="bg-success mt-5 rounded">
                                                <a href="{{ route('topics.show', $row->id) }}">
                                                    <h5 class="text-white text-center p-10"> Enroll Now For Free </h5>
                                                </a>
                                            </div>
                                        @else
                                            <div class="bg-primary mt-5 rounded">
                                                <a href="javascript:void(0)" data-bs-toggle="modal"
                                                    data-bs-target="#modal-center" data-animation="shake">
                                                    <h5 class="text-white text-center p-10"> Enroll Lesson </h5>
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
                    <p>This lesson is locked, you must purchase it through the mobile app.</p>
                </div>
                <div class="modal-footer modal-footer-uniform">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->
@endsection
