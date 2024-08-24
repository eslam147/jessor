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
        
        .no_image_available::before {
            content: "";
            position: absolute;
            background: url("{{ settingByType('logo1') ? tenant_asset(settingByType('logo1')) : global_asset('assets/logo.svg') }}");
            z-index: 99;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            height: 65%;
            width: 100%;
            background-size: cover;
            background-position: center;
            backdrop-filter: grayscale(4);
        }

        .no_image_available::after {
            content: "";
            position: absolute;
            width: 100%;
            background: #efefef;
            z-index: 9;
            top: 0;
            left: 0;
            height: 100%;
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    @foreach ($subjects as $row)
                        <div class="col-xs-12 col-lg-4">
                            <div class="box-body bg-primary-light mb-30 px-xl-5 px-xxl-20 pull-up">
                                <div class="d-flex align-items-center ps-xl-20">
                                    <div class="me-20">
                                        <svg class="circle-chart" viewBox="0 0 33.83098862 33.83098862" width="80"
                                            height="80">
                                            <defs>
                                                {{-- {{ empty($row->thumbnail) ? 'no_image_available' : '' }} --}}
                                                <pattern id="img{{ $row->id }}" patternUnits="userSpaceOnUse" height="80"
                                                    width="80">
                                                    <image x="5" y="5" height="70%" width="70%"
                                                        xlink:href="{{ empty($row->getRawOriginal('image')) ? $row->image : global_asset('images/no_image_available.jpg') }}"></image>
                                                </pattern>
                                            </defs>
                                            <circle class="circle-chart__background" stroke="#efefef" stroke-width="2"
                                                cx="16.91549431" cy="16.91549431" r="15.91549431" fill="url(#img{{ $row->id }})"></circle>
                                            <circle class="circle-chart__circle" stroke="#68CA77" stroke-width="2"
                                                stroke-dasharray="79" stroke-linecap="round" fill="none" cx="16.91549431"
                                                cy="16.91549431" r="15.91549431"></circle>
                                        </svg>
                                    </div>
                                    <div class="d-flex flex-column w-75">
                                        <a href="#"
                                            class="text-dark hover-primary mb-5 fw-600 fs-18">{{ $row->name }}</a>
                                        <div class="row">
                                            <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                                <p class="my-10"><i class="si-book-open si"></i> {{ $row->lessons_count }} Lesson</p>
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

                                        <a href="{{ route('subjects.show', $row->id) }}"
                                            class="waves-effect waves-light btn bg-green mb-5 px-25 py-8">Proceed</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection
