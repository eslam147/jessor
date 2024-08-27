@extends('student_dashboard.layout.app')
@section('style')
    <link rel="stylesheet" href="{{ global_asset('student/css/custom.css') }}">
    {{-- <style>
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
    </style> --}}
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="box">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-5 col-sm-6">
                                                <div class="box box-body b-1 text-center no-shadow">
                                                    <img src="{{ $lesson->thumbnail }}" id="product-image" class="img-fluid"
                                                        alt="">
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <h2 class="box-title my-0 text-bold">{{ $lesson->name }}</h2>
                                                <h1 class="pro-price mb-0 mt-20">
                                                    {{ number_format($lesson->price, 2) }}
                                                </h1>
                                                <hr>
                                                <p>{{ $lesson->description }}</p>
                                                <hr>
                                                <div class="gap-items">
                                                    @if (!empty($lesson->studentActiveEnrollment))
                                                        <div class="bg-success mt-5 rounded">
                                                            <a href="{{ route('topics.show', $lesson->id) }}">
                                                                <h5 class="text-white text-center p-10"> View Topics </h5>
                                                            </a>
                                                        </div>
                                                    @else
                                                        @if ($lesson->is_lesson_free)
                                                        <button data-id="{{ $lesson->id }}"
                                                            class="btn btn-success locked-btn"
                                                            <i class="fa fa-gift mx-2"></i>Enroll Lesson For Free!</button>

                                                        @endif
                                                        <button data-id="{{ $lesson->id }}"
                                                            class="btn btn-success locked-btn"
                                                            data-price="{{ $lesson->price }}" data-bs-toggle="modal"
                                                            data-bs-target="#payment-methods">
                                                            <i class="fa fa-shopping-cart mx-2"></i>Enroll Lesson!</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-7 col-lg-6 col-12">
                                        <div class="box bg-transparent no-shadow mb-20">
                                            <div class="box-header no-border pb-0">
                                                <h4 class="box-title">Topics</h4>
                                            </div>
                                        </div>
                                        {{-- @dd($lesson->topic) --}}
                                        @foreach ($lesson->topic as $topic)
                                            <div class="box mb-15 pull-up {{ $loop->odd ? 'bg-primary-light' : '' }}">
                                                <div class="box-body">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="me-15 bg-warning h-50 w-50 l-h-60 rounded text-center">
                                                                <img src="{{ tenant_asset($topic->thumbnail) }}"
                                                                    alt="">
                                                            </div>
                                                            <div class="d-flex flex-column fw-500">
                                                                <a href="#"
                                                                    class="text-dark hover-primary mb-1 fs-16">{{ $topic->name }}</a>
                                                                <span class="text-fade">{{ $topic->description }}</span>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('topics.files', $topic->id) }}">
                                                            @if (!empty($lesson->studentActiveEnrollment))
                                                                <span class="icon-Arrow-right fs-24"><span
                                                                        class="path1"></span><span
                                                                        class="path2"></span></span>
                                                            @else
                                                                <i class="fas fa-lock   fa-fw"></i>
                                                            @endif
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-lg-6 col-12 col-xl-5">
                                        <div class="bg-primary-light rounded20  big-side-section">
                                            <div class="box">
                                                <div class="text-white box-body bg-img text-center m-20 py-65"
                                                    style="background-image: url({{ global_asset('student/images/gallery/creative/vector.jpg') }});">
                                                </div>
                                                <div class="box-body up-mar100 pb-0">
                                                    <div class=" justify-content-center">
                                                        <div>
                                                            <div
                                                                class="bg-white px-10 text-center pt-15 w-120 ms-20 mb-0 rounded20 mb-20">
                                                                <a href="#" class="w-80">
                                                                    <img class="avatar avatar-xxl rounded20 bg-light img-fluid"
                                                                        src="{{ $lesson->teacher->user->image }}"
                                                                        alt="{{ $lesson->teacher->user->full_name }}">
                                                                </a>
                                                            </div>
                                                            <div class="ms-30 mb-15">
                                                                <h5 class="my-10 mb-0 fw-500 fs-18"><a class="text-dark"
                                                                        href="#">{{ $lesson->teacher->user->full_name }}</a>
                                                                </h5>
                                                                <span class="text-fade mt-5"><i class="fa fa-info"
                                                                        aria-hidden="true"></i>
                                                                    {{ $lesson->teacher->qualification }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-20 side-block">
                                                            <div class="col-4 ">
                                                                <div
                                                                    class="bg-primary-light side-block-left text-center rounded20 pull-up">
                                                                    <i class="si-book-open si bg-white p-10 rounded10"></i>
                                                                    <h3 class="mb-0">
                                                                        {{ $lesson->teacher->lessons_count }}
                                                                    </h3>
                                                                    <p class="m-0 text-fade">Lessons</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 ">
                                                                <div
                                                                    class="bg-warning-light side-block-left text-center rounded20 pull-up">
                                                                    <i class="si-people si bg-white p-10 rounded10"></i>
                                                                    <h3 class="mb-0">
                                                                        {{ $lesson->teacher->students_count }}
                                                                    </h3>
                                                                    <p class="m-0 text-fade">Students </p>
                                                                </div>
                                                            </div>
                                                            <div class="col-4 ">
                                                                <div
                                                                    class="bg-danger-light side-block-left text-center rounded20 pull-up">
                                                                    <i class="si-question si bg-white p-10 rounded10"></i>
                                                                    <h3 class="mb-0">
                                                                        {{ $lesson->teacher->questions_count }}
                                                                    </h3>
                                                                    <p class="m-0 text-fade">Questions</p>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @include('student_dashboard.lessons.partials.purchase_lessons_modal')
@endsection