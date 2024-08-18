@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title"> {{ $lesson->name }} </h4>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-pills mb-20">
                                <li class=" nav-item">
                                    <a href="#navpills2-1" class="nav-link active" data-bs-toggle="tab" aria-expanded="false">Topics</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div id="navpills2-1" class="tab-pane active">
                                    <div class="row">
                                        @foreach ($topics as $row)
                                            <div class="col-xs-12 col-lg-4">
                                                <div class="box pull-up">
                                                    <div class="box-body">
                                                        <p class="mb-0 fs-18"> {{ $row->name }} </p>
                                                        <div class="d-flex justify-content-between mt-30">
                                                            <div>
                                                                <p class="mb-0 text-fade">lesson description</p>
                                                                <p class="mb-0">{{ $row->description }}</p>
                                                            </div>
                                                            <div>
                                                                <p class="mb-5 fw-600">55%</p>
                                                                <div class="progress progress-sm mb-0 w-100">
                                                                    <div class="progress-bar progress-bar-primary"
                                                                        role="progressbar" aria-valuenow="40"
                                                                        aria-valuemin="0" aria-valuemax="100"
                                                                        style="width: 55%">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="bg-primary mt-5 rounded">
                                                            <a href="{{ route('topics.files', $row->id) }}">
                                                                <h5 class="text-white text-center p-10"> Start Now </h5>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </section>
        @endsection
