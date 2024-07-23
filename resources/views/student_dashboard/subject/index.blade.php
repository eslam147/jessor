@extends('student_dashboard.layout.app')
@section('style')

<style>
    .bg-green{
        background-color: #6AC977;
        color: #ffffff;
        border-radius: 15px;
    }

    .bg-green-br{
        border-color: #6ac977;
        color: #7E8FC1;
        background-color: transparent;
        border-radius: 15px;
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
                <div class="col-4" >
                    <div class="box-body bg-primary-light mb-30 px-xl-5 px-xxl-20 pull-up">
                        <div class="d-flex align-items-center ps-xl-20">
                            <div class="me-20">
                                <svg class="circle-chart" viewBox="0 0 33.83098862 33.83098862" width="80" height="80">
                                    <defs>
                                        <pattern id="img" patternUnits="userSpaceOnUse" height="80" width="80">
                                            <image x="5" y="5" height="70%" width="70%" xlink:href="{{ asset('student/images/pro-1.png') }}"></image>
                                        </pattern>
                                    </defs>
                                    <circle class="circle-chart__background" stroke="#efefef" stroke-width="2" cx="16.91549431" cy="16.91549431" r="15.91549431" fill="url(#img)"></circle>
                                    <circle class="circle-chart__circle" stroke="#68CA77" stroke-width="2" stroke-dasharray="79" stroke-linecap="round" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431"></circle>
                                </svg>
                            </div>
                            <div class="d-flex flex-column w-75">
                                <a href="#" class="text-dark hover-primary mb-5 fw-600 fs-18">{{ $row->name }}</a>
                                <div class="row">
                                    <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                        <p class="my-10"  ><i class="si-book-open si"></i> 21 lesson</p>
                                        <p><i class="si-note si"></i> 5 Proceed</p>
                                    </div>
                                    <div class="col-xxl-6 col-xl-12 col-lg-6 col-md-6 text-fade">
                                        <p class="my-10" ><i class="si-clock si"></i> 50 Exams</p>
                                        <p><i class="si-docs si"></i> 5 docs </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between  mx-lg-10 mt-10">
                            <h2 class="my-5 c-green">79%</h2>
                            <div class="text-center">

                                <a href="{{ route('subjects.show',$row->id) }}" class="waves-effect waves-light btn bg-green mb-5 px-25 py-8">Proceed</a>
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
