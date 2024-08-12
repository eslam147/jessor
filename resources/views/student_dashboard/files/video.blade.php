@php
    $settings = getSettings('custom_browser');
@endphp
<div class="row" style="width: -webkit-fill-available;">
    <div class="col-12" style="width: -webkit-fill-available;">
        @if(isset($settings['custom_browser']) && $settings['custom_browser'] == 'enabled')
            @if (trim(request()->userAgent()) == 'semi_browser_by_adel')
                @if ($video->isYoutubeVideo())
                    <div id="player" data-plyr-provider="youtube" data-plyr-embed-id="{{ getYouTubeVideoId($video->file_url) }}"></div>
                @elseif($video->isVideoCorner() || $video->isVideoCornerDownload())
                    <input type="hidden" id="title_for_browser"  value="{{ $video->file_name }}">
                    <input type="hidden" id="download_for_browser" value="{{ $video->download_link }}">
                    <iframe src="{{ $video->file_url }}" style="width: -webkit-fill-available;height: 500px;" frameborder="0" allowfullscreen></iframe>
                @endif
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
        @else
            @if ($video->isYoutubeVideo())
                <div id="player" data-plyr-provider="youtube" data-plyr-embed-id="{{ getYouTubeVideoId($video->file_url) }}"></div>
            @elseif($video->isVideoCorner() || $video->isVideoCornerDownload())
                <input type="hidden" id="title_for_browser"  value="{{ $video->file_name }}">
                <input type="hidden" id="download_for_browser" value="{{ $video->download_link }}">
                <iframe src="{{ $video->file_url }}" style="width: -webkit-fill-available;height: 500px;" frameborder="0" allowfullscreen></iframe>
            @endif
        @endif
    </div>
</div>
