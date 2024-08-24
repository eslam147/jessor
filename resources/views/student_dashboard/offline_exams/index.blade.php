@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box no-shadow mb-0 bg-transparent">
                            <div class="box-header no-border px-0">
                                <h4 class="box-title">Your Offline Exams</h4>
                                {{-- <ul class="box-controls pull-right d-md-flex d-none">
                                  <li>
                                    <button class="btn btn-primary-light px-10">View All</button>
                                  </li>
                                  <li class="dropdown">
                                    <button class="dropdown-toggle btn btn-primary-light px-10" data-bs-toggle="dropdown" href="#" aria-expanded="false">Most Popular</button>										  
                                    <div class="dropdown-menu dropdown-menu-end" style="">
                                      <a class="dropdown-item active" href="#">Today</a>
                                      <a class="dropdown-item" href="#">Yesterday</a>
                                      <a class="dropdown-item" href="#">Last week</a>
                                      <a class="dropdown-item" href="#">Last month</a>
                                    </div>
                                  </li>
                                </ul> --}}
                            </div>
                        </div>
                    </div>
                    @foreach ($exams as $exam)
                    <div class="col-xl-3 col-md-6 col-12">
                       <a href="{{  route('student_dashboard.exams.offline.show',$exam->id) }}">
                        <div class="box bg-secondary-light pull-up"
                        style="background-image: url(../images/svg-icon/color-svg/st-1.svg); background-position: right bottom; background-repeat: no-repeat;">
                        <div class="box-body">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center pe-2 justify-content-between">
                                    <div class="d-flex">
                                        <span class="badge badge-primary me-15">
                                            @if (now()->parse($exam->starting_date)->isPast() &&
                                                    now()->parse($exam->ending_date)->isPast())
                                                OnGoinng
                                            @elseif (now()->parse($exam->starting_date)->isFuture())
                                                UpComing
                                            @else
                                                Completed
                                            @endif

                                        </span>
                                        {{-- <span class="badge badge-primary me-5"><i class="fa fa-lock"></i></span> --}}
                                        {{-- <span class="badge badge-primary"><i class="fa fa-clock-o"></i></span> --}}
                                    </div>
                                    {{-- <div class="dropdown">
                                        <a data-bs-toggle="dropdown" href="#" class="px-10 pt-5"><i
                                                class="ti-more-alt"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
                                            <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
                                            <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#"><i class="ti-settings"></i>
                                                Settings</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <h4 class="mt-25 mb-5">{{ $exam->name }}</h4>
                                {{-- <h4 class="mt-25 mb-5"></h4> --}}
                                <p class="text-fade mb-0 fs-12">{{ $exam->description }}</p>
                            </div>
                        </div>
                    </div>
                       </a>
                    </div>
                    @endforeach
                    {{-- <div class="col-xl-3 col-md-6 col-12">
                        <div class="box bg-secondary-light pull-up" style="background-image: url(../images/svg-icon/color-svg/st-2.svg); background-position: right bottom; background-repeat: no-repeat;">
                            <div class="box-body">	
                                <div class="flex-grow-1">	
                                    <div class="d-flex align-items-center pe-2 justify-content-between">
                                        <div class="d-flex">									
                                            <span class="badge badge-dark me-15">Finished</span>
                                        </div>
                                        <div class="dropdown">
                                            <a data-bs-toggle="dropdown" href="#" class="px-10 pt-5"><i class="ti-more-alt"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                              <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
                                              <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
                                              <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
                                              <div class="dropdown-divider"></div>
                                              <a class="dropdown-item" href="#"><i class="ti-settings"></i> Settings</a>
                                            </div>
                                        </div>						
                                    </div>
                                    <h4 class="mt-25 mb-5">Programming</h4>
                                    <p class="text-fade mb-0 fs-12">1 Days Left</p>
                                </div>	
                            </div>					
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="box bg-secondary-light pull-up" style="background-image: url(../images/svg-icon/color-svg/st-3.svg); background-position: right bottom; background-repeat: no-repeat;">
                            <div class="box-body">	
                                <div class="flex-grow-1">	
                                    <div class="d-flex align-items-center pe-2 justify-content-between">
                                        <div class="d-flex">									
                                            <span class="badge badge-primary me-15">Active</span>
                                            <span class="badge badge-primary me-5"><i class="fa fa-lock"></i></span>
                                        </div>
                                        <div class="dropdown">
                                            <a data-bs-toggle="dropdown" href="#" class="px-10 pt-5"><i class="ti-more-alt"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                              <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
                                              <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
                                              <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
                                              <div class="dropdown-divider"></div>
                                              <a class="dropdown-item" href="#"><i class="ti-settings"></i> Settings</a>
                                            </div>
                                        </div>						
                                    </div>
                                    <h4 class="mt-25 mb-5">Networking</h4>
                                    <p class="text-fade mb-0 fs-12">15 days Left</p>
                                </div>	
                            </div>					
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="box bg-secondary-light pull-up" style="background-image: url(../images/svg-icon/color-svg/st-4.svg); background-position: right bottom; background-repeat: no-repeat;">
                            <div class="box-body">	
                                <div class="flex-grow-1">	
                                    <div class="d-flex align-items-center pe-2 justify-content-between">
                                        <div class="d-flex">									
                                            <span class="badge badge-warning-light me-15">Paused</span>
                                            <span class="badge badge-warning-light me-5"><i class="fa fa-lock"></i></span>
                                        </div>
                                        <div class="dropdown">
                                            <a data-bs-toggle="dropdown" href="#" class="px-10 pt-5"><i class="ti-more-alt"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                              <a class="dropdown-item" href="#"><i class="ti-import"></i> Import</a>
                                              <a class="dropdown-item" href="#"><i class="ti-export"></i> Export</a>
                                              <a class="dropdown-item" href="#"><i class="ti-printer"></i> Print</a>
                                              <div class="dropdown-divider"></div>
                                              <a class="dropdown-item" href="#"><i class="ti-settings"></i> Settings</a>
                                            </div>
                                        </div>						
                                    </div>
                                    <h4 class="mt-25 mb-5">Network Security</h4>
                                    <p class="text-fade mb-0 fs-12">21 Days Left</p>
                                </div>	
                            </div>					
                        </div>
                    </div> --}}
                </div>
                {{-- <div class="row">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title"> Offline Exams </h4>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table no-border mb-0">
                                    <thead>
                                        <tr>
                                            <th>
                                                #
                                            </th>
                                            <th>
                                                Name
                                            </th>
                                             <th>
                                                Description
                                            </th> 
                                            <th>
                                                Min Date
                                            </th>
                                            <th>
                                                Max Date
                                            </th>
                                            {{-- <th>
                                                session_year
                                            </th> 
                                            <th>
                                                status
                                            </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($exams as $exam)
                                            <tr>
                                                <td>
                                                    {{ $exam->id }}
                                                </td>
                                                {{-- min_timetable,
                                                <td class="fw-600">{{ $exam->name }}</td>
                                                {{-- <td class="text-fade">{{ $exam->description }}</td> 
                                                <td class="text-fade">{{ $exam->starting_date }}</td>
                                                <td class="text-fade">{{ $exam->ending_date }}</td>
                                                {{-- <td class="text-fade">{{ $exam->session_year->name }}</td>
                                                <td class="text-fade">
                                                    @if (now()->parse($exam->starting_date)->isPast() &&
                                                            now()->parse($exam->ending_date)->isPast())
                                                        OnGoinng
                                                    @elseif (now()->parse($exam->starting_date)->isFuture())
                                                        UpComing
                                                    @else
                                                        Completed
                                                    @endif
                                                </td>
                                                <td>

                                                    <a
                                                        href="{{ route('student_dashboard.exams.offline.show', $exam->id) }}">Show</a>
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div> --}}
            </section>
        @endsection
