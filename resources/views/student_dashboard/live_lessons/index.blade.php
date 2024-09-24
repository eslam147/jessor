@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        {{-- <div class="box no-shadow mb-0 bg-transparent">
                            <div class="box-header no-border px-0">
                                <h4 class="box-title">Live Lessons</h4>
                            </div>
                        </div> --}}
                        <div class="box bg-transparent no-shadow mb-20">
                            <div class="box-header no-border pb-0 ps-0">
                                <h4 class="box-title">Incoming Lessons</h4>
                                {{-- <ul class="box-controls pull-right d-md-flex d-none">
                                    <li>
                                        <div class="dropdown p-10 px-15  bg-primary-light rounded ms-10">
                                             <a data-bs-toggle="dropdown" href="#" aria-expanded="false" class=""><i class="ti-more-alt rotate-90 text-muted"></i></a>
                                              <div class="dropdown-menu dropdown-menu-end" style="">
                                                <a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a>
                                                <a class="dropdown-item" href="#"><i class="fa fa-picture-o"></i> Shots</a>
                                                <a class="dropdown-item" href="#"><i class="ti-check"></i> Follow</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#"><i class="fa fa-ban"></i> Block</a>
                                              </div>
                                        </div>
                                    </li>
                                    <li>
                                        <button class="btn btn-primary-light me-10 p-10">Show All</button>
                                    </li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-xxl-6 col-xl-8 col-lg-12 col-12">
                    

                    </div> --}}
                    @foreach ($liveSessions as $day => $sessions)
                        {{-- @dd(
                        $day->,$sessions
                    ) --}}
                        <p class="mb-5">{{ now()->parse($day)->dayName }}</p>
                        @foreach ($sessions as $session)
                            <div class="box mb-30 pull-up">
                                <div>
                                    <div class="table-responsive">
                                        <table class="table no-border mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="min-w-50">
                                                        <div class="bg-primary-light h-50 w-50 l-h-50 rounded text-center">
                                                            <span class="icon-Book-open fs-24"><i
                                                                    class="fa fa-calendar-minus-o fs-16"
                                                                    aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="fw-600 min-w-170">
                                                        <div class="d-flex flex-column fw-600">
                                                            <a href="#"
                                                                class="text-dark hover-primary mb-1 fs-16">{{ $session->session_date->format('h:i A') }}
                                                                -
                                                                {{ $session->session_date->addMinutes($session->duration)->format('h:i A') }}</a>
                                                            <span class="text-fade">{{ $session->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td class="fw-600 fs-16 min-w-150 text-center"><i
                                                            class="fa fa-fw fa-circle text-light fs-12"></i>{{ $session->name }}
                                                    </td>
                                                    <td class="fw-400 fs-16 min-w-150 text-center">
                                                        {{ $session->subject->name }}</td>
                                                    <td class="text-primary fw-600 fs-16 min-w-150 text-end">
                                                        @if ($session->status->isStarted())
                                                            <a href="{{ $session->join_url ?? '#' }}" target="_blank">
                                                                <span class="badge badge-primary">Join Now</span>
                                                            </a>
                                                        @elseif ($session->status->isScheduled())
                                                            <span class="badge badge-warning">Scheduled</span>
                                                        @elseif ($session->status->isFinished())
                                                            <span class="badge badge-success">Finished</span>
                                                        @endif

                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach

            </section>
        @endsection
