@extends('student_dashboard.layout.app')

@section('style')
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        .plyr--video.plyr--stopped .plyr__controls,
        .plyr--video.plyr--paused .plyr__controls {
            opacity: 0;
        }

        .plyr__video-embed iframe {
            top: -50%;
            height: 200%;
            pointer-events: none;
        }

        .plyr__video-wrapper {
            width: 100%;
            height: 500px;
        }

        .vtabs .tab-content iframe {
            height: 400px;
        }

        .rotate {
            transform: rotate(0deg);
            transition: transform 0.2s ease;
        }

        .rotate.down {
            transform: rotate(180deg);
        }

        .list-group {
            width: 100%;
            padding: unset;
        }

        .list-group-item {
            border-right: unset;
            border-left: unset;
        }

        .no-right-click {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: transparent;
            cursor: none;
        }
    </style>
    <link rel="stylesheet" href="{{ global_asset('assets/fonts/font-awesome.min.css') }}">
    @unless (config('app.debug'))
        <script src="{{ global_asset('js/app.js') }}"></script>
        <script type="module">
            if (window.devtools.isOpen) {
                document.location.reload();
            }
            window.addEventListener('devtoolschange', event => {
                if (event.detail.isOpen) {
                    document.location.reload();
                }
            });

            document.onkeydown = (e) => false;
            document.addEventListener('keydown', function(event) {
                if (event.ctrlKey && event.key === 's') {
                    event.preventDefault();
                }

                if (event.keyCode === 123) {
                    return false;
                }
                if (event.button === 2) {
                    return false;
                }
                if (event.ctrlKey && event.shiftKey && event.keyCode == 'I'.charCodeAt(0)) {
                    return false;
                }
                if (event.ctrlKey && event.shiftKey && event.keyCode == 'J'.charCodeAt(0)) {
                    return false;
                }
                if (event.ctrlKey && event.keyCode == 'U'.charCodeAt(0)) {
                    return false;
                }
            });

            window.addEventListener('contextmenu', event => event.preventDefault());
        </script>
    @endunless
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Topic - {{ $topic->name }}</h4>
                        </div>
                        <!-- /.box-header -->

                        <div class="box-body">
                            <div class="row">
                                <div class="col-12 col-lg-3 col-md-4">
                                    <div class="bg-light h-550 media-list media-list-divided p-10 rounded-3">
                                        @if ($videos->count() > 0)
                                            @foreach ($videos as $row)
                                                <div class="media media-single px-0">
                                                    <div
                                                        class="ms-0 me-15 bg-success-light h-50 w-50 l-h-50 rounded text-center">
                                                        <span class="fs-24 text-success"><i
                                                                class="fa fa-file-video-o"></i></span>
                                                    </div>
                                                    <span class="title fw-500 fs-16">{{ $row->file_name }}</span>
                                                    <a class="btn btn-icon btn-success-light btn-sm fs-18 hover-info m-0 rounded-5 text-gray video-link preview_btn"
                                                        href="javascript:void(0)" data-id="{{ $row->id }}"><i
                                                            class="fa fa-play"></i></a>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="media media-single px-0">
                                                <div
                                                    class="ms-0 me-15 bg-success-light h-50 w-50 l-h-50 rounded text-center">
                                                    <span class="fs-24 text-success"><i class="fa fa-notice"></i></span>
                                                </div>
                                                <span class="title fw-500 fs-16">No Videos Found</span>
                                            </div>
                                        @endif
                                        @if ($files->count() > 0)
                                            @foreach ($files as $row)
                                                <div class="media media-single px-0">
                                                    <div
                                                        class="ms-0 me-15 bg-primary-light h-50 w-50 l-h-50 rounded text-center">
                                                        <span class="fs-24 text-primary"><i
                                                                class="fa fa-file-text-o"></i></span>
                                                    </div>
                                                    <span class="title fw-500 fs-16">{{ $row->file_name }}</span>
                                                    <a class="btn btn-icon btn-success-light btn-sm fs-18 hover-info m-0 rounded-5 text-gray file-link preview_btn"
                                                        href="#" data-id="{{ $row->id }}">
                                                        <i class="fa fa-eye"></i></a>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="media media-single px-0">
                                                <div
                                                    class="ms-0 me-15 bg-success-light h-50 w-50 l-h-50 rounded text-center">
                                                    <span class="fs-24 text-success"><i class="fa fa-notice"></i></span>
                                                </div>
                                                <span class="title fw-500 fs-16">No Files Found</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-9 col-md-8 col-12 d-flex justify-content-center align-items-center ">
                                    <div class=" h-p100 w-p100" id="content">

                                        <input type="hidden" id="download_for_browser" value="">
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
        function showLoader() {
            $("#content").html(
                '<div class="w-p100 h-100 d-flex align-items-center justify-content-center"><i class="fa-solid fa-circle-notch fa-spin" style="font-size: 50px;"></i></div>'
            );
        }
        document.addEventListener('DOMContentLoaded', () => {
            const player = new Plyr('#player', {
                youtube: {
                    noCookie: true,
                    modestbranding: 1,
                    showinfo: 0,
                    rel: 0,
                    iv_load_policy: 3,
                }
            });
            player.on('ready', () => {
                console.log('Player Is Reade');

                const plyrContainer = document.querySelector('.plyr__video-wrapper');
                console.log(plyrContainer);

                if (plyrContainer) {
                    const noRightClickDiv = document.createElement('div');
                    noRightClickDiv.classList.add('no-right-click');
                    plyrContainer.appendChild(noRightClickDiv);
                    noRightClickDiv.addEventListener('contextmenu', (event) => event.preventDefault());
                }

            })

        });
        $(document).on('click', '.preview_btn', function(event) {
            $('.preview_btn').removeClass('active');
            $(this).addClass('active')
        });
        $(document).on('click', '.file-link', function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            showLoader();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: `{{ route('topics.getfile', ':id') }}`.replace(':id', id),
                success: function(data) {
                    $("#content").html(data);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
        $(document).on('click', '.video-link', function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            showLoader();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: `{{ route('topics.getvideo', ':id') }}`.replace(':id', id),
                success: function(data) {
                    $("#content").html(data);
                    const player = new Plyr('#player', {
                        youtube: {
                            noCookie: true,
                            modestbranding: 1,
                            showinfo: 0,
                            rel: 0,
                            iv_load_policy: 3,
                        }
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>
@endsection
