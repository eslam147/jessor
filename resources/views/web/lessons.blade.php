@extends('web.master')
@section('css')
    <link rel="stylesheet" href="{{ global_asset('student/css/custom.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/frontend.css') }}">
@endsection
@section('content')
    <div class="main">
        <div class="container">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title">{{ $lesson->name }}</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fa fa-home"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('/teachers') }}">{{ __('teachers') }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('instructor.profile', $lesson->teacher->id) }}">{{ $lesson->teacher->user->full_name }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('instructor.profile', $lesson->subject->id) }}">{{ $lesson->subject->name }}</a>
                                    </li>
                                    <li class="breadcrumb-item">{{ $lesson->name }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="theme-primary row">
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
                                                            data-class-section-id ="{{ $lesson->class_section_id }}"
                                                            class="btn btn-success locked-btn"
                                                            <i class="fa fa-gift mx-2"></i>Enroll Lesson For Free!</button>

                                                        @endif
                                                        <button data-id="{{ $lesson->id }}"
                                                            class="btn btn-success locked-btn"
                                                            data-price="{{ $lesson->price }}" data-bs-toggle="modal"
                                                            data-class-section-id ="{{ $lesson->class_section_id }}"
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
                                                        <a @if(auth()->check() && auth()->user()->student && !empty($lesson->studentActiveEnrollment)  && !in_array($topic->id,$result)) href="{{ route('topics.files', $topic->id) }}" @else href="#" @endif>
                                                            @if (auth()->check() && auth()->user()->student && !empty($lesson->studentActiveEnrollment)  && !in_array($topic->id,$result))
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
                                        <div class="box box-widget widget-user">
                                            <div class="widget-user-header bg-img bbsr-0 bber-0" style="background: url({{ global_asset('student/images/gallery/full/10.jpg') }}) center center;" data-overlay="5">
                                                <h3 class="widget-user-username text-white">{{ $lesson->teacher->user->full_name }}</h3>
                                            </div>
                                            <div class="widget-user-image">
                                                <img class="rounded-circle {{ $classes[array_rand($classes)] }}" src="{{ !empty($lesson->teacher->user->getRawOriginal('image')) ? $lesson->teacher->user->image : global_asset('student/images/avatar/avatar-12.png') }}" alt="{{ $lesson->teacher->user->full_name }}">
                                            </div>
                                            <div class="box-footer">
                                                <div class="row">
                                                    <div class="text-center mt-3 mb-3">
                                                        <span>
                                                            <i class="fa fa-star text-warning"></i>
                                                            <i class="fa fa-star text-warning"></i>
                                                            <i class="fa fa-star text-warning"></i>
                                                            <i class="fa fa-star text-warning"></i>
                                                            <i class="fa fa-star-half text-warning"></i>
                                                            <span class="text-muted ms-2">(12)</span>
                                                        </span>                        
                                                    </div>
                                                    <div class="col-12 text-center">
                                                        @if(auth()->check())
                                                            <button data-id="{{ $lesson->teacher->id }}" class="btn btn-info-light follow-btn @if($follow) active @endif">
                                                                @if($follow)
                                                                    <i class="ti-minus"></i> {{ __('unfollow') }}
                                                                @else
                                                                    <i class="ti-plus"></i> {{ __('follow') }}
                                                                @endif
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="description-block">
                                                            <h5 class="description-header">{{ $lesson->teacher->students_count }}</h5>
                                                            <span class="description-text">{{ __('students') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 be-1 bs-1">
                                                        <div class="description-block">
                                                            <h5 class="description-header">{{ $lesson->teacher->subjects_count }}</h5>
                                                            <span class="description-text">{{ __('subjects') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="description-block">
                                                            <h5 class="description-header">{{ $lesson->teacher->lessons_teacher_count }}</h5>
                                                            <span class="description-text">{{ __('lessons') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="description-block">
                                                            <h5 class="description-header">{{ $lesson->teacher->questions_count }}</h5>
                                                            <span class="description-text">{{ __('questions') }}</span>
                                                        </div>
                                                    </div>
                                                    <!-- /.col -->
                                                </div>
                                                <!-- /.row -->
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