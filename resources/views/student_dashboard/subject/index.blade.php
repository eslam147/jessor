@extends('student_dashboard.layout.app')
@section('style')
    <style>
        /* .bg-green {
                        background-color: #6AC977;
                        color: #ffffff;
                        border-radius: 15px;
                    }

                    .bg-green-br {
                        border-color: #6ac977;
                        color: #7E8FC1;
                        background-color: transparent;
                        border-radius: 15px;
                    } */

        /* .no_image_available::before {
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
                    } */
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    @foreach ($subjects as $row)
                        <div class="col-lg-6 col-12">
                            <div class="box pull-up">
                                <div class="box-body bg-img"
                                    style="background-image: url({{ global_asset('student/images/bg-5.png') }});"
                                    data-overlay-light="9">
                                    <div class="d-lg-flex align-items-center justify-content-between">
                                        <div class="d-md-flex align-items-center mb-30 mb-lg-0 w-p100">
                                            <img src="{{ !empty($row->image) ? $row->image : loadTenantMainAsset('logo1', global_asset('images/no_image_available.jpg')) }}"
                                                class="h-100 img-fluid object-fit-cover w-150" alt="">
                                            <div class="ms-30 w-75">
                                                <h4 class="mb-10 text-bold">{{ $row->name }}</h4>
                                                @if ($row->teachers->count())
                                                    <div class="d-flex flex-column mt-10 w-75">
                                                        <div class="d-flex">
                                                            @foreach ($row->teachers->take(4) as $teacher)
                                                                <a href="#"
                                                                    class="h-50 l-h-50 overflow-hidden rounded text-center w-50">
                                                                    <img src="{{ $teacher->user->image }}"
                                                                        class="align-self-end h-50 object-fit-cover"
                                                                        alt="">
                                                                </a>
                                                                @endforeach
                                                                @if ($row->teachers->count() > 4)
                                                                <a href="#"
                                                                    class="h-50 l-h-50 overflow-hidden rounded text-center bg-body-secondary rounded-5 w-50">
                                                                    {{ $row->teachers->count() - 4 }}+
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('subjects.show', $row->id) }}"
                                                class="waves-effect waves-light w-p100 btn btn-primary"
                                                style="white-space: nowrap;">View All!</a>
                                        </div>
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
