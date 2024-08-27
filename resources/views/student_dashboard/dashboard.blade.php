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
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xl-8 col-12">
                        <div class="box bg-primary-light">
                            <div class="box-body d-flex px-0">
                                <div class="flex-grow-1 p-30 flex-grow-1 bg-img dask-bg bg-none-md"
                                    style="background-position: right bottom; background-size: auto 100%; background-image: url(https://eduadmin-template.multipurposethemes.com/bs5/images/svg-icon/color-svg/custom-1.svg)">
                                    <div class="row">
                                        <div class="col-12 col-xl-7">
                                            <h2>Welcome back, <strong> {{ Auth::user()->first_name }} !</strong></h2>

                                            <p class="text-dark my-10 fs-16">
                                                Your students complated <strong class="text-warning">80%</strong> of the
                                                tasks.
                                            </p>
                                            <p class="text-dark my-10 fs-16">
                                                Progress is <strong class="text-warning">very good!</strong>
                                            </p>
                                        </div>
                                        <div class="col-12 col-xl-5"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12 col-xl-6">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Notice board</h4>
                                    </div>
                                    <div class="box-body p-0">
                                        <div class="media-list media-list-hover">
                                            <div class="media bar-0">
                                                <span class="avatar avatar-lg bg-primary-light rounded"><i
                                                        class="fa fa-user"></i></span>
                                                <div class="media-body fw-500">
                                                    <p class="d-flex align-items-center justify-content-between">
                                                        <a class="hover-success" href="#"><strong>New
                                                                Teacher</strong></a>
                                                        <span class="text-fade fw-500 fs-12">Just Now</span>
                                                    </p>
                                                    <p class="text-fade">It is a long established fact that a reader will
                                                        be<span class="d-xxxl-inline-block d-none"> distracted by the
                                                            readable</span>...</p>
                                                </div>
                                            </div>

                                            <div class="media bar-0">
                                                <span class="avatar avatar-lg bg-danger-light rounded"><i
                                                        class="fa fa-money"></i></span>
                                                <div class="media-body">
                                                    <p class="d-flex align-items-center justify-content-between">
                                                        <a class="hover-success" href="#"><strong> New Fees
                                                                Structure</strong></a>
                                                        <span class="text-fade fw-500 fs-12">Today</span>
                                                    </p>
                                                    <p class="text-fade">It is a long established fact that a reader will
                                                        be<span class="d-xxxl-inline-block d-none"> distracted by the
                                                            readable</span>...</p>
                                                </div>
                                            </div>

                                            <div class="media bar-0">
                                                <span class="avatar avatar-lg bg-success-light rounded"><i
                                                        class="fa fa-book"></i></span>
                                                <div class="media-body">
                                                    <p class="d-flex align-items-center justify-content-between">
                                                        <a class="hover-success" href="#"><strong>Updated
                                                                Syllabus</strong></a>
                                                        <span class="text-fade fw-500 fs-12">17 Dec 2020</span>
                                                    </p>
                                                    <p class="text-fade">It is a long established fact that a reader will
                                                        be<span class="d-xxxl-inline-block d-none"> distracted by the
                                                            readable</span>...</p>
                                                </div>
                                            </div>

                                            <div class="media bar-0">
                                                <span class="avatar avatar-lg bg-info-light rounded"><i
                                                        class="fa fa-graduation-cap"></i></span>
                                                <div class="media-body">
                                                    <p class="d-flex align-items-center justify-content-between">
                                                        <a class="hover-success" href="#"><strong>New
                                                                Course</strong></a>
                                                        <span class="text-fade fw-500 fs-12">27 Oct 2020</span>
                                                    </p>
                                                    <p class="text-fade">It is a long established fact that a reader will
                                                        be<span class="d-xxxl-inline-block d-none"> distracted by the
                                                            readable</span>...</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="box-footer text-center p-10">
                                        <a href="#" class="btn w-p100 btn-primary-light p-5">View all</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-6">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Daily Offlie Class's</h4>
                                    </div>
                                    <div class="box-body px-0 pt-0 pb-10">
                                        <div class="media-list media-list-hover">
                                            <a class="media media-single" href="#">
                                                <h4 class="w-50 text-gray fw-500">10:10</h4>
                                                <div class="media-body ps-15 bs-5 rounded border-primary">
                                                    <p>Morbi quis ex eu arcu.</p>
                                                    <span class="text-fade">by Johne</span>
                                                </div>
                                            </a>

                                            <a class="media media-single" href="#">
                                                <h4 class="w-50 text-gray fw-500">08:40</h4>
                                                <div class="media-body ps-15 bs-5 rounded border-success">
                                                    <p>Proin iacul eros no odi.</p>
                                                    <span class="text-fade">by Amla</span>
                                                </div>
                                            </a>

                                            <a class="media media-single" href="#">
                                                <h4 class="w-50 text-gray fw-500">07:10</h4>
                                                <div class="media-body ps-15 bs-5 rounded border-info">
                                                    <p>In mattis mi posuere.</p>
                                                    <span class="text-fade">by Josef</span>
                                                </div>
                                            </a>

                                            <a class="media media-single" href="#">
                                                <h4 class="w-50 text-gray fw-500">01:15</h4>
                                                <div class="media-body ps-15 bs-5 rounded border-danger">
                                                    <p>Morbi quis ex eu arcu.</p>
                                                    <span class="text-fade">by Rima</span>
                                                </div>
                                            </a>

                                            <a class="media media-single" href="#">
                                                <h4 class="w-50 text-gray fw-500">23:12</h4>
                                                <div class="media-body ps-15 bs-5 rounded border-warning">
                                                    <p>Morbi quis ex eu arcu.</p>
                                                    <span class="text-fade">by Alaxa</span>
                                                </div>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                     <div class="col-12" >
                        <div class="box bg-transparent no-shadow mb-20">
                            <div class="box-header no-border pb-0 ps-0">
                                <h4 class="box-title">Incoming Live Class</h4>
                                <ul class="box-controls pull-right d-md-flex d-none">
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
                                </ul>
                            </div>
                        </div>
                        <p class="mb-5">Monday</p>

                        <div class="box mb-15 pull-up">
                            <div>
                                <div class="table-responsive">
                                    <table class="table no-border mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="min-w-50">
                                                    <div class="bg-primary-light h-50 w-50 l-h-50 rounded text-center">
                                                        <span class="icon-Book-open fs-24"><i class="fa fa-calendar-minus-o fs-16" aria-hidden="true"></i>
                                                    </div>
                                                </td>
                                                <td class="fw-600 min-w-170">
                                                    <div class="d-flex flex-column fw-600">
                                                        <a href="#" class="text-dark hover-primary mb-1 fs-16">2:00 PM - 3:00 PM</a>
                                                        <span class="text-fade">4.1 A Gllileo</span>
                                                    </div>
                                                </td>
                                                <td class="fw-600 fs-16 min-w-150 text-center"><i class="fa fa-fw fa-circle text-primary fs-12"></i> Lesson 30</td>
                                                <td class="fw-400 fs-16 min-w-150 text-center">Online Classes</td>
                                                <td class="text-primary fw-600 fs-16 min-w-150 text-end">Zoom <i class="fa fa-fw fa-angle-right fs-24"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="box mb-15 pull-up">
                            <div>
                                <div class="table-responsive">
                                    <table class="table no-border mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="min-w-50">
                                                    <div class="bg-warning-light h-50 w-50 l-h-50 rounded text-center">
                                                        <span class="icon-Book-open fs-24"><i class="fa fa-calendar-minus-o fs-16" aria-hidden="true"></i>
                                                    </div>
                                                </td>
                                                <td class="fw-600 min-w-170">
                                                    <div class="d-flex flex-column fw-600">
                                                        <a href="#" class="text-dark hover-primary mb-1 fs-16">4:00 PM - 5:00 PM</a>
                                                        <span class="text-fade">3.1 A Gllileo</span>
                                                    </div>
                                                </td>
                                                <td class="fw-600 fs-16 min-w-150 text-center"><i class="fa fa-fw fa-circle text-light fs-12"></i> Lesson 31</td>
                                                <td class="fw-400 fs-16 min-w-150 text-center">Online Classes</td>
                                                <td class="text-primary fw-600 fs-16 min-w-150 text-end">Zoom <i class="fa fa-fw fa-angle-right fs-24"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <p class="mb-5">Wednesday</p>
                        <div class="box mb-30 pull-up">
                            <div>
                                <div class="table-responsive">
                                    <table class="table no-border mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="min-w-50">
                                                    <div class="bg-primary-light h-50 w-50 l-h-50 rounded text-center">
                                                        <span class="icon-Book-open fs-24"><i class="fa fa-calendar-minus-o fs-16" aria-hidden="true"></i>
                                                    </div>
                                                </td>
                                                <td class="fw-600 min-w-170">
                                                    <div class="d-flex flex-column fw-600">
                                                        <a href="#" class="text-dark hover-primary mb-1 fs-16">2:00 PM - 3:00 PM</a>
                                                        <span class="text-fade">4.2 A Gllileo</span>
                                                    </div>
                                                </td>
                                                <td class="fw-600 fs-16 min-w-150 text-center"><i class="fa fa-fw fa-circle text-light fs-12"></i> Lesson 31</td>
                                                <td class="fw-400 fs-16 min-w-150 text-center">Online Classes</td>
                                                <td class="text-primary fw-600 fs-16 min-w-150 text-end">Zoom <i class="fa fa-fw fa-angle-right fs-24"></i></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                     </div>
                  </div> --}}

                    </div>
                    {{-- <div class="col-xl-4 col-12">
                        <div class="box">
                            <div class="box-body">
                                <div class="box no-shadow mb-0">
                                    <div class="box-body px-0 pt-0">
                                        <div id="calendar" class="dask evt-cal min-h-400"></div>
                                    </div>
                                </div>

                                <h3 class="box-title mb-20 fw-500"> Latest Subjects </h3>
                                <div class="box-body bg-primary-light mb-30 px-xl-5 px-xxl-20 pull-up">
                                    <div class="d-flex align-items-center ps-xl-20">
                                        <div class="me-20">
                                            <svg class="circle-chart" viewBox="0 0 33.83098862 33.83098862"
                                                width="80" height="80">
                                                <defs>
                                                    <pattern id="img" patternUnits="userSpaceOnUse" height="80"
                                                        width="80">
                                                        <image x="5" y="5" height="70%" width="70%"
                                                            xlink:href="{{ url('student/images/pro-1.png') }}"></image>
                                                    </pattern>
                                                </defs>
                                                <circle class="circle-chart__background" stroke="#efefef"
                                                    stroke-width="2" cx="16.91549431" cy="16.91549431" r="15.91549431"
                                                    fill="url(#img)"></circle>
                                                <circle class="circle-chart__circle" stroke="#68CA77" stroke-width="2"
                                                    stroke-dasharray="79" stroke-linecap="round" fill="none"
                                                    cx="16.91549431" cy="16.91549431" r="15.91549431"></circle>
                                            </svg>
                                        </div>
                                        <div class="d-flex flex-column w-75">
                                            <a href="#" class="text-dark hover-primary mb-5 fw-600 fs-18">Biology
                                                Molecular</a>
                                            <div class="row">
                                                <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                                    <p class="my-10"><i class="si-book-open si"></i> 21 lesson</p>
                                                    <p><i class="si-note si"></i> 5 Proceed</p>
                                                </div>
                                                <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                                    <p class="my-10"><i class="si-clock si"></i> 50 Exams</p>
                                                    <p><i class="si-docs si"></i> 5 docs </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between  mx-lg-10 mt-10">
                                        <h2 class="my-5 c-green">79%</h2>
                                        <div class="text-center">

                                            <button type="button"
                                                class="waves-effect waves-light btn bg-green mb-5 px-25 py-8">Proceed</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-body bg-primary-light mb-30 px-xl-5 px-xxl-20 pull-up">
                                    <div class="d-flex align-items-center ps-xl-20">
                                        <div class="me-20">
                                            <svg class="circle-chart" viewBox="0 0 33.83098862 33.83098862"
                                                width="80" height="80">
                                                <defs>
                                                    <pattern id="img-1" patternUnits="userSpaceOnUse" height="80"
                                                        width="80">
                                                        <image x="5" y="5" height="70%" width="70%"
                                                            xlink:href="{{ url('student/images/pro-2.png') }}"></image>
                                                    </pattern>
                                                </defs>
                                                <circle class="circle-chart__background" stroke="#efefef"
                                                    stroke-width="2" cx="16.91549431" cy="16.91549431" r="15.91549431"
                                                    fill="url(#img-1)"></circle>
                                                <circle class="circle-chart__circle" stroke="#68CA77" stroke-width="2"
                                                    stroke-dasharray="64" stroke-linecap="round" fill="none"
                                                    cx="16.91549431" cy="16.91549431" r="15.91549431"></circle>
                                            </svg>
                                        </div>
                                        <div class="d-flex flex-column w-75">
                                            <a href="#" class="text-dark hover-primary fw-600 mb-5 fs-18">Color
                                                Theory</a>
                                            <div class="row">
                                                <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                                    <p class="my-10"><i class="si-book-open si"></i> 10 lesson</p>
                                                    <p><i class="si-note si"></i> 2 Lectures</p>
                                                </div>
                                                <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                                    <p class="my-10"><i class="si-clock si"></i> 40 Exams</p>
                                                    <p><i class="si-docs si"></i> 13 docs</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between   mx-lg-10 mt-10">
                                        <h2 class="my-5 c-green">64%</h2>
                                        <div class="text-center">

                                            <button type="button"
                                                class="waves-effect waves-light btn  bg-green mb-5 px-25 py-8">Lectures</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer text-center p-10">
                                    <a href="#" class="btn w-p100 btn-primary-light p-5">View all</a>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                </div>
            </section>
            <!-- /.content -->
        </div>
    </div>
    <!-- /.content-wrapper -->
@endsection
