@extends('student_dashboard.layout.app')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title"> {{ $lesson_name }} </h4>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-pills mb-20">
                                <li class=" nav-item"> <a href="#navpills2-1" class="nav-link active" data-bs-toggle="tab"
                                        aria-expanded="false">Topics</a> </li>
                                <li class="nav-item"> <a href="#navpills2-2" class="nav-link" data-bs-toggle="tab"
                                        aria-expanded="false">Files</a> </li>
                                <li class="nav-item"> <a href="#navpills2-3" class="nav-link" data-bs-toggle="tab"
                                        aria-expanded="true">Videos</a> </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div id="navpills2-1" class="tab-pane active">
                                    <div class="row">
                                        @foreach ($topics as $row)
                                            <div class="col-4">
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
                                                                <h5 class="text-white text-center p-10"> start now </h5>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div id="navpills2-2" class="tab-pane">
                                    Files
                                </div>
                                <div id="navpills2-3" class="tab-pane">
                                    @foreach ($lesson->file as $row)
                                        <div class="row"style="width: -webkit-fill-available;">
                                            <div class="col-12"style="width: -webkit-fill-available;">
                                                @if ($row->isYoutubeVideo())
                                                    <div id="player" data-plyr-provider="youtube"
                                                        data-plyr-embed-id="{{ getYouTubeVideoId($row->file_url) }}">
                                                    </div>
                                                @elseif($row->isVideoCorner())
                                                    <input type="hidden" id="title_for_browser"
                                                        value="{{ $row->file_name }}">
                                                    <input type="hidden" id="download_for_browser"
                                                        value="{{ $row->download_link }}">
                                                    <iframe src="{{ $row->file_url }}" class=""
                                                        style="width: -webkit-fill-available;"frameborder="0"></iframe>
                                                @elseif($row->isVideoCornerDownload())
                                                    <input type="hidden" id="title_for_browser"
                                                        value="{{ $row->file_name }}">
                                                    <input type="hidden" id="download_for_browser"
                                                        value="{{ $row->download_link }}">
                                                    <iframe src="{{ $row->file_url }}" class=""
                                                        style="width: -webkit-fill-available;" frameborder="0"></iframe>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </section>
        @endsection
