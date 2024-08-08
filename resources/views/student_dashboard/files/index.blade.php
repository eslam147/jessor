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
        .rotate {
            transform: rotate(0deg);
            transition: transform 0.2s ease;
        }

        .rotate.down {
            transform: rotate(180deg);
        }

        .list-group{
            width: 100%;
            padding: unset;
        }

        .list-group-item{
            border-right: unset;
            border-left: unset;
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
                                        <div class="col-3" >
                                            <div class="accordion" id="accordionExample">
                                                    {{-- videos --}}
                                                    <h2 class="accordion-header" id="headingOne">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVideo" aria-expanded="true" aria-controls="collapseOne">
                                                            <span> Videos </span>
                                                            <i class="bi bi-chevron-right ms-auto rotate"></i>
                                                        </button>
                                                    </h2>
                                                    <div id="collapseVideo" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                        <ul class="accordion-body list-group">
                                                            @if($videos->count() > 0)
                                                                @foreach ($videos as $row)
                                                                    <li class="list-group-item video-link" data-id="{{ $row->id }}" ><a href="javascript:void(0)">{{ $row->file_name }}</a></li>
                                                                @endforeach
                                                            @else
                                                                <li class="list-group-item"><a href="javascript:void(0)"> No Videos Found </a></li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                    {{-- files --}}
                                                    <h2 class="accordion-header" id="headingrow">
                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiles" aria-expanded="true" aria-controls="collapseOne">
                                                            <span> Files </span>
                                                            <i class="bi bi-chevron-right ms-auto rotate"></i>
                                                        </button>
                                                    </h2>
                                                    <div id="collapseFiles" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                        <ul class="accordion-body list-group">
                                                            @foreach ($files as $row)
                                                                <li class="list-group-item file-link" data-id="{{ $row->id }}">
                                                                    <a href="javascript:void(0)" >
                                                                        {{ $row->file_name }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                            </div>
                                        </div>
                                        <div class="col-9" id="content" >

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

        $(document).on('click', '.file-link', function(event) {
            event.preventDefault();
            var id = $(this).data('id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '{{ route("topics.getfile", ":id") }}'.replace(':id', id),
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
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '{{ route("topics.getvideo", ":id") }}'.replace(':id', id),
                success: function(data) {
                    $("#content").html(data);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>

@endsection
