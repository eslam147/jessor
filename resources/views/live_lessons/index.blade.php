@extends('layouts.master')

@section('title', __('live_lessons'))
@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('live_lessons') }}
            </h3>
        </div>
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card search-container">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('create_live_lessons') }}
                        </h4>
                        <form class="pt-3 add-lesson-form" id="create-form" action="{{ route('live_lessons.store') }}"
                            method="POST" novalidate="novalidate" enctype="multipart/form-data">
                            <div class="row">

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('class') . ' ' . __('section') }} <span
                                            class="text-danger">*</span></label>
                                    <select name="class_section_id" id="class_section_id"
                                        class="class_section_id form-control">
                                        <option value="">--{{ __('select') }}--</option>
                                        @foreach ($class_section as $section)
                                            <option value="{{ $section->id }}" data-class="{{ $section->class->id }}">
                                                {{ $section->class->name . ' ' . $section->section->name . ' - ' . $section->class->medium->name }}
                                                {{ $section->class->streams->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('subject') }} <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="subject_id form-control">
                                        <option value="">--{{ __('select') }}--</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-6 lesson_date ">
                                    <label>
                                        {{ __('date') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" name="session_date" id="session_date" class="form-control"
                                        required placeholder="{{ __('session_date') }}">

                                    @error('session_date')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="form-group col-sm-6 lesson_session_time ">
                                    <label>
                                        {{ __('session_time') }}<span class="text-danger">*</span>
                                        <small class="text-info">({{ trans('in_minutes') }})</small>
                                    </label>

                                    {!! Form::number('session_duration', 0, [
                                        'required',
                                        'min' => 1,
                                        'step' => '1',
                                        'placeholder' => __('session_duration'),
                                        'class' => 'form-control',
                                    ]) !!}
                                    @error('session_time')
                                        <p class="text-danger" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-sm-12 col-md-6">
                                    <label>{{ __('lesson_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                        placeholder="{{ __('lesson_name') }}" class="form-control" />
                                </div>

                                <div class="form-group col-sm-12 col-md-8">
                                    <label>{{ __('lesson_description') }} <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" placeholder="{{ __('lesson_description') }}" class="form-control"></textarea>
                                </div>
                                <hr>

                            </div>
                            <hr>
                            <div class="text-center">
                                <button class="btn btn-theme" id="create-btn" type="submit">{{ __('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('live_lessons') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="col">
                                    <select name="filter_subject_id" id="filter_subject_id" class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <select name="filter_class_section_id" id="filter_class_section_id"
                                        class="form-control">
                                        <option value="">{{ __('all') }}</option>
                                        @foreach ($class_section as $class)
                                            <option value="{{ $class->id }}">
                                                {{ $class->class->name . '-' . $class->section->name . ' ' . $class->class->medium->name }}
                                                {{ $class->class->streams->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                            data-url="{{ route('live_lessons.list') }}" data-click-to-select="true"
                            data-side-pagination="server" data-pagination="true"
                            data-page-list="[5, 10, 20, 50, 100, 200, All]" data-search="true" data-toolbar="#toolbar"
                            data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                            data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                            data-query-params="createLiveLessonQueryParams"
                            data-export-options='{ "fileName": "lesson-list-{{ date('d-m-y') }}" ,"ignoreColumn":
                            ["operate"]}'
                            data-show-export="true">
                            <thead>
                                <tr>
                                    <th scope="col" data-field="id" data-sortable="true" data-visible="false">
                                        {{ __('id') }}</th>
                                    <th scope="col" data-field="no" data-sortable="false">{{ __('no.') }}</th>
                                    <th scope="col" data-field="name" data-sortable="true">{{ __('name') }}</th>

                                    <th scope="col" data-field="description" data-sortable="true">
                                        {{ __('description') }}
                                    </th>

                                    <th scope="col" data-field="class_section_name" data-sortable="true">
                                        {{ __('class_section') }}
                                    </th>

                                    <th scope="col" data-field="subject_name" data-sortable="true">
                                        {{ __('subject') }}
                                    </th>
                                    <th scope="col" data-field="password" data-sortable="true">
                                        {{ __('password') }}
                                    </th>

                                    <th scope="col" data-field="status_name" data-sortable="false">
                                        {{ __('status') }}
                                    </th>

                                    <th scope="col" data-field="started_at" data-sortable="false">
                                        {{ __('started_at') }}
                                    </th>
                                    <th scope="col" data-field="session_date" data-sortable="false">
                                        {{ __('session_date') }}
                                    </th>
                                    <th scope="col" data-field="meeting_url" data-sortable="false">
                                        {{ __('meeting_url') }}
                                    </th>
                                    <th scope="col" data-field="duration_readable" data-sortable="false">
                                        {{ __('duration') }}
                                    </th>


                                    <th scope="col" data-field="created_at" data-sortable="true"
                                        data-visible="false"> {{ __('created_at') }}</th>
                                    <th scope="col" data-field="updated_at" data-sortable="true"
                                        data-visible="false"> {{ __('updated_at') }}</th>
                                    <th scope="col" data-field="operate" data-sortable="false"
                                        data-events="actionEvents">{{ __('action') }}</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            @include('live_lessons.modals.create_meeting')
            @include('live_lessons.modals.show_participants')
        </div>
    </div>
@endsection
@section('js')
    <script>
        function createLiveLessonQueryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
                'class_section_id': $('#student_class_section').val(),
                'session_year_id': $('#session_year_id').val(),
            };
        }
        window.actionEvents = {
            'click .start_meeting': function(e, value, row, index) {
                e.preventDefault();

                // SweetAlert confirmation for starting a meeting
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to start the meeting?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, start it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('live_lessons.start', ':live_lesson') }}".replace(':live_lesson',
                            row.id), {
                            _token: "{{ csrf_token() }}",
                            id: row.id
                        }, function(response) {
                            if (!response.errors) {
                                showSuccessToast(response.message);

                                $("#table_list").bootstrapTable("refresh");
                                // Open Tab with meeting link
                                if (response.meeting_url) {
                                    window.open(response.meeting_url, '_blank');
                                }
                            }
                        });
                    }
                });
            },

            'click .stop_meeting': function(e, value, row, index) {
                e.preventDefault();

                Swal.fire({
                    title: 'Stop Meeting',
                    text: 'Do you want to stop the meeting? You can add a Notes before proceeding.',
                    input: 'textarea', // Textarea input for comments
                    inputPlaceholder: 'Enter your notes here...',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, stop it!',
                    cancelButtonText: 'Cancel',
                    preConfirm: (comment) => {
                        return comment;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Extract comment from SweetAlert result
                        let notes = result.value;

                        // Send stop meeting request with the comment
                        $.post("{{ route('live_lessons.stop', ':live_lesson') }}".replace(':live_lesson',
                            row.id), {
                            _token: "{{ csrf_token() }}",
                            id: row.id,
                            notes: notes // Pass the comment along with the request
                        }, function(response) {
                            if (!response.errors) {
                                showSuccessToast(response.message);
                                $("#table_list").bootstrapTable("refresh");
                            }
                        });
                    }
                });

            },
            'click .connect_meeting': function(e, value, row, index) {
                e.preventDefault();
                let modal = $("#connectMeetingModal");
                $("#connectMeetingModal form")[0].reset();
                // ----------------------------------------------------------- \\
                let url = `{{ route('live_lessons.schedule_meeting', ':live_lesson') }}`.replace(':live_lesson',
                    row
                    .id);
                modal.find('form').attr('action', url);
                // ----------------------------------------------------------- \\
                modal.find('#live_lesson_id').val(row.id);
                modal.find('#meeting_start_date').val(row.session_date);
                modal.find('#meeting_duration').val(row.duration);
                // ----------------------------------------------------------- \\
                modal.modal('show');
                // ----------------------------------------------------------- \\
            },
            'click .show_users': function(e, value, row, index) {
                e.preventDefault();
                $("#show_participants_table").empty(); // Clear the table before appending new data
                $.ajax({
                    url: "{{ route('live_lessons.participants', ':live_lesson') }}".replace(':live_lesson',
                        row.id),
                    type: 'GET',
                    success: function(response) {
                        let students = response.data.students;
                        (Array.from(students)).forEach(student => {
                            let joinStatus = student.is_joined ? '✅ Joined' : '❌ Not Joined';

                            let studentRow = `<tr>
                        <td>${student.id}</td>
                        <td>${student.name}</td>
                        <td>${student.email}</td>
                        <td>${student.enrolled_at}</td>
                        <td>${joinStatus}</td>
                        <td>
                            <button class="btn btn-secondary send-message" data-id="${row.id}">Send Message</button>
                            <button class="btn btn-danger remove-student" data-id="${row.id}">Remove</button>
                        </td>
                    </tr>`;
                            $("#show_participants_table").append(studentRow);
                        });
                        // Show the modal after appending the data
                        $("#show_participants").modal('show');
                    }
                });
            }
        };
        $("#connectMeetingModal form").submit(function(e) {
            e.preventDefault();
            var modal = $("#connectMeetingModal");

            let formElement = $(this);

            let data = new FormData(this);
            let url = $(this).attr('action')
            let submitButtonElement = $(this).find("#assign_meeting_btn");

            function beforeSendCallback() {
                submitButtonElement.attr('disabled', true);
            }

            function successCallback(response) {
                modal.modal('hide');
                showSuccessToast(response.message);
                $("#table_list").bootstrapTable("refresh");
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
        });
    </script>
@endsection
