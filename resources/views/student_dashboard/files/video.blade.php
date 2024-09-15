@extends('student_dashboard.files.inc.use_private_browser')
<div class="row" style="width: -webkit-fill-available;">
    <div class="col-12" style="width: -webkit-fill-available;">
        @section('lesson_content')
            @if ($video->isYoutubeVideo())
                <div id="player" data-plyr-provider="youtube"
                    data-plyr-embed-id="{{ getYouTubeVideoId($video->file_url) }}"></div>
            @elseif($video->isVideoCorner() || $video->isVideoCornerDownload())
                <input type="hidden" id="title_for_browser" value="{{ $video->file_name }}">
                <input type="hidden" id="download_for_browser" value="{{ $video->download_link }}">
                <iframe src="{{ $video->file_url }}" style="width: -webkit-fill-available;height: 500px;" frameborder="0"
                    allowfullscreen></iframe>
            @endif
        @endsection
    </div>
</div>
