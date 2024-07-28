@extends('student_dashboard.layout.app')
@section('style')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        .plyr__video-wrapper {
            width: 100%;
            /* Adjust width as needed */
            height: 500px;
            /* Adjust height as needed */
        }
    </style>
@endsection
@section('content')
    @php
        function getYouTubeVideoId($url)
        {
            // Parse the URL to get its components
            $urlComponents = parse_url($url);
            // Check if the host is YouTube
            if (
                isset($urlComponents['host']) &&
                (strpos($urlComponents['host'], 'youtube.com') !== false ||
                    strpos($urlComponents['host'], 'youtu.be') !== false)
            ) {
                // Check if the URL contains a 'v' query parameter (standard YouTube URL)
                if (isset($urlComponents['query'])) {
                    parse_str($urlComponents['query'], $queryParams);
                    if (isset($queryParams['v'])) {
                        return $queryParams['v'];
                    }
                }

                // Check if the URL is a shortened YouTube URL (youtu.be)
                if (isset($urlComponents['path']) && strpos($urlComponents['host'], 'youtu.be') !== false) {
                    return ltrim($urlComponents['path'], '/');
                }
            }

            // Return null if the URL is not a valid YouTube URL or does not contain a video ID
            return null;
        }
    @endphp
    <style>
        .vtabs .tab-content iframe {
            height: 400px;
        }
    </style>
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
                                <div id="navpills2-2" class="tab-pane active">
                                    <div class="row">
                                        <div class="">
                                            <div class="col-12 vtabs">
                                                <ul class="nav nav-tabs tabs-vertical" role="tablist">
                                                    <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab"
                                                            href="#home4" role="tab"><span class="hidden-sm-up"><i
                                                                    class="ion-home"></i></span> <span
                                                                class="hidden-xs-down"> Videos </span> </a> </li>
                                                    <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab"
                                                            href="#profile4" role="tab"><span class="hidden-sm-up"><i
                                                                    class="ion-person"></i></span> <span
                                                                class="hidden-xs-down">Files</span></a> </li>
                                                </ul>
                                                <!-- Tab panes -->
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="home4" role="tabpanel">
                                                        @foreach ($videos as $row)
                                                            <div class="row"style="width: -webkit-fill-available;">
                                                                <div class="col-12"style="width: -webkit-fill-available;">
                                                                    @if ($row->isYoutubeVideo())
                                                                        <div id="player" data-plyr-provider="youtube"
                                                                            data-plyr-embed-id="{{ getYouTubeVideoId($row->file_url) }}">
                                                                        </div>
                                                                    @elseif($row->isVideoCorner())
                                                                        <iframe src="{{ $row->file_url }}" class="" style="width: -webkit-fill-available;"frameborder="0"></iframe>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="tab-pane" id="profile4" role="tabpanel">
                                                        <div class="p-15">
                                                            <h4>Fusce porta eros a nisl varius, non molestie metus mollis.
                                                                Pellentesque tincidunt ante sit amet ornare lacinia.</h4>
                                                            <p>Duis cursus eros lorem, pretium ornare purus tincidunt
                                                                eleifend. Etiam quis justo vitae erat faucibus pharetra.
                                                                Morbi in ullamcorper diam. Morbi lacinia, sem vitae
                                                                dignissim cursus, massa nibh semper magna, nec pellentesque
                                                                lorem nisl quis ex.</p>
                                                            <h3>Donec vitae laoreet neque, id convallis ante.</h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="navpills2-2" class="tab-pane">
                                    Files
                                </div>
                                <div id="navpills2-3" class="tab-pane">
                                    Videos
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script src="path/to/plyr.js"></script>
    <script src="path/to/plyr.js"></script>
    <script>
        const player = new Plyr('#player');
    </script>
@endsection
