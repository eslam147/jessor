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
                                <h4 class="box-title">Incoming Lessons</h4>
                            </div>
                        </div>
                    </div>
                    @foreach ($liveSessions as $day => $sessions)
                        <div class="col-12">
                            <h4 class="text-bold mb-5">{{ now()->parse($day)->dayName }}</h4>
                        </div>
                        @foreach ($sessions as $session)
                            <div class="col-md-6">
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
                                        <div class="session_content pt-20">
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
                                                    <button data-id="{{ $lesson->id }}" class="btn btn-success locked-btn"
                                                        data-price="{{ $lesson->price }}" data-bs-toggle="modal"
                                                        data-bs-target="#payment-methods">
                                                        <i class="fa fa-shopping-cart mx-2"></i>
                                                        Enroll Lesson!
                                                    </button>
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
                                                    <a class="btn btn-sm btn-bold btn-primary mt-15" href="#">
                                                        Purchase Now
                                                    </a>
                                                    @if ($session->meeting)
                                                        @if ($session->status->isStarted())
                                                            <a class="btn btn-sm btn-bold btn-primary mt-15"
                                                                href="{{ $session->meeting->join_url ?? '#' }}">
                                                                Join Now
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>

                                            </div>

                                            <!-- Additional Info (Author, Date, Comments, Shares) -->
                                            <p class="mt-0 mb-25 bt-1 px-30 pt-10">
                                                <i class="fa fa-user"></i> by <a
                                                    href="#">{{ $session->teacher->user->full_name ?? 'Unknown' }}</a>
                                                | <i class="fa fa-calendar"></i>
                                                {{ $session->session_date->format('M d, Y h:i A') }}
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
