@extends('web.master')
@section('css')
    <link rel="stylesheet" href="{{ url('/assets/css/frontend.css') }}">
@endsection
<!-- navbar ends here  -->
@section('content')
    <div class="main">
        <div class="container">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title">{{ $subject->name }} - {{ $subject->type }}</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}"><i class="fa fa-home"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('/teachers') }}">{{ __('teachers') }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a
                                            href="{{ route('instructor.profile', $teacher->id) }}">{{ $teacher->user->full_name }}</a>
                                    </li>
                                    <li class="breadcrumb-item">{{ $subject->name }} - {{ $subject->type }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <section subject="{{ $id }}" class="content subject">
                <div class="theme-primary container">
                    <div class="row">
                        <div class="col-12 col-lg-12 col-xl-12">
                            <div class="box box-inverse bg-img" style="background-image: url({{ global_asset('student/images/gallery/full/1.jpg') }});"
                                data-overlay="2">
                                @if(auth()->check())
                                    <div class="flexbox px-20 pt-20">
                                        <label class="toggler @if($follow) text-danger @endif text-white">
                                            <input type="checkbox" class="follow" data-id="{{ $teacher->id }}">
                                            <i class="fa fa-heart"></i>
                                        </label>
                                    </div>
                                @endif
                                <div class="box-body text-center pb-50">
                                    <a href="{{ route('instructor.profile', $teacher->id) }}">
                                        <img class="avatar avatar-xxl avatar-bordered {{ $classes[array_rand($classes)] }}" src="{{ !empty($teacher->user->getRawOriginal('image')) ? $teacher->user->image : global_asset('student/images/avatar/avatar-12.png') }}"
                                            alt="{{ $teacher->user->full_name }}">
                                    </a>
                                    <h4 class="mt-2 mb-0">
                                        <a class="hover-primary text-white" href="{{ route('instructor.profile', $teacher->id) }}">{{ $teacher->user->full_name }}</a><br>
                                        @if(auth()->check())
                                            <button class="btn btn-info-light follow-btn @if($follow) active @endif">
                                                @if($follow)
                                                    <i class="ti-minus"></i> {{ __('unfollow') }}
                                                @else
                                                    <i class="ti-plus"></i> {{ __('follow') }}
                                                @endif
                                            </button>
                                        @endif
                                    </h4>
                                </div>
                                <ul class="box-body flexbox flex-justified text-center" data-overlay="4">
                                    <li>
                                        <span class="opacity-60">{{ __('students') }}</span><br>
                                        <span class="fs-20">{{ $teacher->students_count }}</span>
                                    </li>
                                    <li>
                                        <span class="opacity-60">{{ __('subjects') }}</span><br>
                                        <span class="fs-20">{{ $teacher->subjects_count }}</span>
                                    </li>
                                    <li>
                                        <span class="opacity-60">{{ __('lessons') }}</span><br>
                                        <span class="fs-20">{{ $teacher->lessons_teacher_count }}</span><br>
                                    </li>
                                    <li>
                                        <span class="opacity-60">{{ __('questions') }}</span><br>
                                        <span class="fs-20">{{ $teacher->questions_count }}</span><br>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-lg-12 col-xl-12">
                            <div class="box">
                                <div class="box-header no-border">
                                    <h4 class="box-title">{{ __('related_lessons') }}</h4>
                                </div>
                                <div class="box-body no-border">
                                    <div class="row get_lessons mt-3">
                                        @include('web.get_lessons')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @include('sweetalert::alert')
    <script src="{{ url('assets/js/vendor.bundle.base.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    @include('student_dashboard.lessons.partials.purchase_lessons_modal')
@endsection
@section('js')
    <script src="{{ url('/assets/js/subject.js') }}"></script>
@endsection
