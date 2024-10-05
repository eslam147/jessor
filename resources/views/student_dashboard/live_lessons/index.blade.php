@extends('student_dashboard.layout.app')
@section('style')
    <style>
        .box-body {
            transition: .4s;
            cursor: pointer;
        }

        .box-body:hover {
            box-shadow: 0 0 10px #bcbcbc;
            transform: translateY(-10px);
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box bg-transparent no-shadow mb-20">
                            <div class="box-header no-border pb-0 ps-0">
                                <h4 class="box-title">upcoming Live Lessons</h4>
                            </div>
                        </div>
                    </div>
                    @foreach ($liveSessions as $day => $sessions)
                        <div class="col-12">
                            <h4 class="text-bold mb-5">{{ now()->parse($day)->dayName }}</h4>
                        </div>
                        @foreach ($sessions as $session)
                            <div class="col-md-4">
                                <div class="box">
                                    <div class="box-body ribbon-box p-0">
                                        <div class="ribbon-two ribbon-two-{{ $session->status->color() }}">
                                            <span>
                                                @if ($session->status->isStarted())
                                                    Started
                                                @elseif ($session->status->isScheduled())
                                                    Scheduled
                                                @elseif ($session->status->isFinished())
                                                    Finished
                                                @endif
                                            </span>
                                        </div>
                                        <div class="border border-gray rounded-1 session_content">
                                            <div class="align-items-center py-10 row">
                                                <div class="col-8">
                                                    <!-- Title and Date -->
                                                    <h4 class="media-heading mt-15 mb-0 px-30">
                                                        <a href="#">
                                                            {{ $session->name }}
                                                        </a>
                                                    </h4>
                                                    <!-- Media Content -->
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <!-- Description -->
                                                            <p class="text-muted">
                                                                {{ $session->subject->description ?? 'No description available.' }}
                                                            </p>

                                                            @if (!$session->status->isFinished())
                                                                @if ($session->participants_count > 0)
                                                                    @if ($session->status->isStarted())
                                                                        <a class="btn btn-sm btn-bold btn-primary mt-15"
                                                                            target="_blank"
                                                                            href="{{ $session->meeting->join_url ?? '#' }}">
                                                                            Join Now
                                                                        </a>
                                                                    @else
                                                                        <p>
                                                                            Left Time To Start
                                                                            <span
                                                                                class="text-primary">{{ $session->session_start_at->diffForHumans() }}</span>
                                                                        </p>
                                                                        {{-- <div class="progress">
                                                                            <div class="progress-bar" role="progressbar"
                                                                                aria-valuenow="0" aria-valuemin="0"
                                                                                aria-valuemax="{{ $session->left_time_as_percent }}">
                                                                            </div>
                                                                        </div> --}}
                                                                    @endif
                                                                @else
                                                                    <button data-id="{{ $session->id }}"
                                                                        class="btn btn-sm btn-bold btn-primary mt-15 locked-btn"
                                                                        data-price="{{ $session->price }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#payment-methods">
                                                                        <i class="fa fa-shopping-cart mx-2"></i>
                                                                        Subscribe!
                                                                    </button>
                                                                @endif
                                                            @endif
                                                            {{-- @if ($session->is_lesson_free)
                                                    <button data-id="{{ $lesson->id }}"
                                                        class="btn btn-success free_enrollment_btn">
                                                        <i class="fa fa-gift mx-2"></i>
                                                        Enroll Lesson For Free!
                                                    </button>
                                                @else
                                                    <button data-id="{{ $lesson->id }}"
                                                        class="btn btn-success locked-btn"
                                                        data-price="{{ $lesson->price }}" data-bs-toggle="modal"
                                                        data-bs-target="#payment-methods">
                                                        <i class="fa fa-shopping-cart mx-2"></i>
                                                        Enroll Lesson!
                                                    </button>
                                                @endif --}}
                                                            <!-- Button to Join or Read More -->
                                                            {{-- <a class="btn btn-sm btn-bold btn-primary mt-15" href="#">
                                                        Purchase Now
                                                    </a> --}}
                                                            {{-- @if ($session->meeting)
                                                                @if ($session->status->isStarted())
                                                                    <a class="btn btn-sm btn-bold btn-primary mt-15"
                                                                        href="{{ $session->meeting->join_url ?? '#' }}">
                                                                        Join Now
                                                                    </a>
                                                                @endif
                                                            @endif --}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="text-center">
                                                        <img src="{{ $session->teacher->user->image }}"
                                                            class="avatar-xxxl rounded-circle" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Additional Info (Author, Date, Comments, Shares) -->
                                            <p class="bg-gray-200 border-top bt-1 mb-0 mt-0 pb-10 pt-10 px-30">
                                                <span>
                                                    <i class="fa fa-user"></i> by <a
                                                        href="#">{{ $session->teacher->user->full_name ?? 'Unknown' }}</a>
                                                </span>
                                                |
                                                <span>
                                                    <i class="fa fa-calendar"></i>
                                                    {{ $session->session_start_at->format('M d, Y h:i A') }}
                                                </span>
                                                |
                                                <span>{{ $session->price }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
            </section>
        </div>
    </div>
    @include('student_dashboard.live_lessons.partials.purchase_lessons_modal')
@endsection
