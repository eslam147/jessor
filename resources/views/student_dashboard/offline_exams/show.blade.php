@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <h4 class="box-title">Subjects</h4>

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-12">
                        <div class="box bg-transparent no-shadow mb-20">
                            {{-- <div class="box mb-0 pull-up"> --}}

                                @foreach ($exam->timetable as $examTimeTable)
                                    <div class="box-body bg-primary-light mb-30 px-xl-5 px-xxl-20 pull-up">
                                        <div class="d-flex align-items-center ps-xl-20">
                                            <div class="me-20">
                                                <svg class="circle-chart" viewBox="0 0 33.83098862 33.83098862" width="80"
                                                    height="80">
                                                    <defs>
                                                        <pattern id="img" patternUnits="userSpaceOnUse" height="80"
                                                            width="80">
                                                            <image x="5" y="5" height="70%" width="70%"
                                                                xlink:href="{{ $examTimeTable->subject->image }}"></image>
                                                        </pattern>
                                                    </defs>
                                                    <circle class="circle-chart__background" stroke="#efefef" stroke-width="2"
                                                        cx="16.91549431" cy="16.91549431" r="15.91549431" fill="url(#img)">
                                                    </circle>
                                                    <circle class="circle-chart__circle" stroke="#68CA77" stroke-width="2"
                                                        stroke-dasharray="79" stroke-linecap="round" fill="none"
                                                        cx="16.91549431" cy="16.91549431" r="15.91549431"></circle>
                                                </svg>
                                            </div>
                                            <div class="d-flex flex-column w-75">
                                                <a href="#"
                                                    class="text-dark hover-primary mb-5 fw-600 fs-18">{{ $examTimeTable->subject->name }}</a>
                                                <div class="row">
                                                    <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                                        <p class="my-10"><i
                                                                class="si-book-open si"></i>{{ $examTimeTable->start_time }}</p>
                                                        <p><i class="si-note si"></i> {{ $examTimeTable->end_time }}</p>
                                                    </div>
                                                    <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                                        <p class="my-10"><i class="si-clock si"></i> {{ $examTimeTable->date }}
                                                        </p>
                                                        <p><i class="si-docs si"></i> {{ $examTimeTable->total_marks }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                        </div>
                

                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
