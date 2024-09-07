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
            /* height: 500px; */
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
                                            @foreach ($videos as $key => $row)
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
                                        @endif
                                        @if ($files->count() > 0)
                                            @foreach ($files as $row)
                                                @if (is_null($row->online_exam_id) && is_null($row->assignment_id))
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
                                                @endif
                                                @if (!empty($row->online_exam_id))
                                                    <div class="media media-single px-0">
                                                        <div
                                                            class="ms-0 me-15 bg-warning-light h-50 w-50 l-h-50 rounded text-center">
                                                            <span class="fs-24 text-primary"><i
                                                                    class="fa fa-file-text-o"></i></span>
                                                        </div>
                                                        <span class="title fw-500 fs-16">{{ $row->exam->title }}</span>
                                                        @if (!$row->exam->student_attempt)
                                                        <a href="#" data-show-modal="{{ !empty($row->exam->exam_key) ? 'true' : 'false' }}"
                                                            class="btn btn-icon btn-success-light btn-sm fs-18 hover-info m-0 rounded-5 text-gray start_exam"
                                                            data-exam-id ="{{ $row->online_exam_id }}">
                                                            <i class="fa fa-link"></i></a>
                                                        @else
                                                            <a href="{{ route('student_dashboard.exams.online.result', $row->online_exam_id) }}"
                                                                class="btn btn-icon btn-success-light btn-sm fs-18 hover-info m-0 rounded-5 text-gray">
                                                                <i class="fa fa-link"></i>
                                                                Show Degree
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if (!empty($row->assignment_id))
                                                    <div class="media media-single px-0">
                                                        <div
                                                            class="ms-0 me-15 bg-warning-light h-50 w-50 l-h-50 rounded text-center">
                                                            <span class="fs-24 text-primary"><i
                                                                    class="fa fa-file-text-o"></i></span>
                                                        </div>
                                                        <span
                                                            class="title fw-500 fs-16">{{ $row->assignment->name }}</span>
                                                        @if (!$row->assignment->submission)
                                                            <a data-href="{{ route('student_dashboard.assignments.submit', $row->assignment_id) }}"
                                                                class="btn btn-icon btn-success-light btn-sm fs-18 hover-info m-0 rounded-5 text-gray submit-assignment">
                                                                <i class="fa fa-eye"></i></a>
                                                        @else
                                                            @if ($row->assignment->submission->isApproved())
                                                                <span class="badge badge-success">Approved</span>
                                                            @elseif($row->assignment->submission->isRejected())
                                                                <span class="badge badge-danger">Rejected</span>
                                                            @else
                                                                <span class="badge badge-warning">Pending</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
                                            @endforeach
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
    <!-- Modal -->
    <div class="modal fade" id="start_exam_modal" tabindex="-1" aria-labelledby="start_exam_modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="examTermsModalLabel">Exam Terms</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="terms">
                        <p>{!! html_entity_decode(settingByType('online_exam_terms_condition')) !!}</p>
                    </div>
                    <form id="examForm" method="POST" autocomplete="off"
                        action="{{ route('student_dashboard.exams.online.start') }}">
                        @csrf
                        <input type="hidden" name="exam_id" id="exam_id">
                        <div class="mb-3">
                            <label for="examKey" class="form-label">Exam Key</label>
                            <input type="text" class="form-control" aut id="examKey" name="exam_key"
                                placeholder="Enter Exam Key" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" form="examForm" class="btn btn-primary">Start</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="submitAssignmentModal" tabindex="-1" role="dialog"
        aria-labelledby="submitAssignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Submit Assignment</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="files">Upload Files</label>
                            <input type="file" name="files[]" class="form-control" multiple required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Assignment</button>
                    </div>
                </form>
            </div>
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
    <script>
        $('.start_exam').click(function() {
            let exam = $(this);
            $('#exam_id').val(exam.data('exam-id'));
            if(exam.data('show-modal')) {
                $('#start_exam_modal').modal('show');
            }else{
                $("#examForm")[0].submit();
            }
        });
    </script>
    <script>
        $('.submit-assignment').click(function(e) {
            e.preventDefault()
            let url = $(this).data('href')
            $('#submitAssignmentModal form').attr('action', url)
            $('#submitAssignmentModal').modal('show');
        })
    </script>
@endsection
