@extends('student_dashboard.layout.app')

@section('style')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        .plyr__video-wrapper {
            width: 100%;
            height: 500px;
        }

        .vtabs .tab-content iframe {
            height: 400px;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Lesson</h4>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div id="home4" class="tab-pane active">
                                    <div class="row">
                                        <div class="col-12 vtabs">
                                            <ul class="nav nav-tabs tabs-vertical" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#videos"
                                                        role="tab">
                                                        <span class="hidden-sm-up"><i class="ion-home"></i></span>
                                                        <span class="hidden-xs-down"> Videos </span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#files" role="tab">
                                                        <span class="hidden-sm-up"><i class="ion-person"></i></span>
                                                        <span class="hidden-xs-down"> Files </span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content">
                                                <div id="videos" class="tab-pane active">
                                                    {{-- @foreach ($files as $row)
                                                        <div class="row" style="width: -webkit-fill-available;">
                                                            <div class="col-12" style="width: -webkit-fill-available;">
                                                                @if ($row->isYoutubeVideo())
                                                                    <div id="player" data-plyr-provider="youtube"
                                                                        data-plyr-embed-id="{{ getYouTubeVideoId($row->file_url) }}">
                                                                    </div>
                                                                @elseif($row->isVideoCorner() || $row->isVideoCornerDownload())
                                                                    @if (trim(request()->userAgent()) == 'semi_browser_by_adel')
                                                                        @if ($row->isVideoCornerDownload())
                                                                            <input type="hidden" id="title_for_browser"
                                                                                value="{{ $row->file_name }}">
                                                                            <input type="hidden" id="download_for_browser"
                                                                                value="{{ $row->download_link }}">
                                                                        @endif
                                                                        <iframe src="{{ $row->file_url }}"
                                                                            style="width: -webkit-fill-available;"
                                                                            frameborder="0"></iframe>
                                                                    @else
                                                                        <div class="bg-bubbles-white p-100"
                                                                            style="text-align: center; margin-top: 30px; margin-bottom: 130px;">
                                                                            <p
                                                                                style="direction: rtl; font-family: cairo; font-size: 30px; margin-bottom: 13px;">
                                                                                هذا المتصفح غير مصرح به. يرجى استخدام
                                                                                المتصفح المسموح.
                                                                            </p>
                                                                            <a href="https://infinityschool.net/tenancy/assets/browser/InfinitySchoolV1.exe"
                                                                                target="_blank" rel="noopener noreferrer"
                                                                                class="btn btn-primary"
                                                                                style="margin: auto; display: inline-block; border: 0; padding: 0;">
                                                                                <button type="button"
                                                                                    class="btn btn-primary"
                                                                                    style="border: solid 5px; padding: 10px 20px; font-size: 18px;">
                                                                                    <i class="fas fa-download"></i> Download
                                                                                </button>
                                                                            </a>
                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach --}}
                                                </div>
                                                <div id="files" class="tab-pane">
                                                    <div class="box-body">
                                                        <!-- Nav tabs -->
                                                        <ul class="nav nav-tabs customtab2" role="tablist">
                                                            @foreach ($files as $row)
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" data-bs-toggle="tab" href="javascript:void(0)" data-id="{{ $row->id }}" role="tab">
                                                                        <span class="hidden-sm-up"><i class="ion-home"></i></span>
                                                                        <span class="hidden-xs-down"> {{ $row->file_name }} </span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        <!-- Tab panes -->
                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="home7" role="tabpanel">
                                                                <div class="p-15">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </section>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const player = new Plyr('#player');
        });

        $(document).ready(function() {
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.ajax({
                    url: '{{ route("topics.getfile") }}',
                    method: 'POST',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#home7 .p-15').html(response);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>

@endsection
