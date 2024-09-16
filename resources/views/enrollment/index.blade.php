@extends('layouts.master')

@section('title', __('enrollments'))

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_enrollments') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list_enrollments') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="col">
                                    <select name="lesson_id" id="filter_lesson" class="form-control">
                                        <option value="">{{ __('select_lesson') }}</option>
                                        @foreach ($lessons as $lesson)
                                            <option value="{{ $lesson->id }}" @selected(request('lesson_id') == $lesson->id)>
                                                {{ $lesson->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @hasrole('Super Admin')
                                    <div class="col">
                                        <select name="teacher_id" id="filter_teacher" class="form-control">
                                            <option value="">{{ __('select_teacher') }}</option>
                                            @foreach ($lessons->pluck('teacher.user.full_name', 'teacher_id') as $id => $name)
                                                <option value="{{ $id }}">
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endhasrole
                                @can('enrollments-create')
                                    <div class="col">
                                        <button type="button" class="btn btn-primary" id="add_enrollment">Add New
                                            Enrollment</button>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                    data-url="{{ route('enrollment.list') }}" data-click-to-select="true"
                                    data-side-pagination="server" data-pagination="true"
                                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                                    data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                    data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                    data-maintain-selected="true" data-export-types='["txt","excel"]'
                                    data-export-options='{ "fileName": "purchased-list-{{ now()->format('d-m-y') }}","ignoreColumn":
                                    ["operate"]}'
                                    data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th scope="col" data-field="id" data-sortable="true" data-visible="false">
                                                {{ __('id') }}</th>

                                            <th scope="col" data-field="no" data-sortable="false">{{ __('no.') }}
                                            </th>

                                            <th scope="col" data-field="student" data-sortable="false">
                                                {{ __('student_name') }}
                                            </th>
                                            @hasrole('Super Admin')
                                                <th scope="col" data-field="teacher" data-sortable="false">
                                                    {{ __('teacher_name') }}
                                                </th>
                                            @endhasrole
                                            <th scope="col" data-field="lesson" data-sortable="true">
                                                {{ __('lesson_title') }}
                                            </th>

                                            <th scope="col" data-field="purchase_date" data-sortable="true">
                                                {{ __('purchase_date') }}
                                            </th>

                                            <th scope="col" data-field="expiration_at" data-sortable="true">
                                                {{ __('expires_at') }}
                                            </th>

                                            <th data-events="actionEvents" scope="col" data-field="operate"
                                                data-sortable="false">{{ __('action') }}</th>

                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog m" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ __('edit') . ' ' . __('enrollment') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="pt-3 edit-enrollment-form" id="edit-form" method="POST"
                    action="{{ route('enrollment.update', '') }}" novalidate="novalidate">
                    <div class="modal-body">
                        <input type="hidden" name="edit_id" id="edit_id">
                        @method('PUT')
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-12">
                                <label>{{ __('expiry_date') }} <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="expiry_date form-control" name="expiration_at"
                                    id="edit_expiry_date">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-light border-top">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('close') }}</button>
                        <input class="btn btn-theme" type="submit" value={{ __('edit') }} />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="storeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog m" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom border-secondary">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ __('create') . ' ' . __('enrollment') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="pt-3 store-enrollment-form" id="store-form" method="POST"
                    action="{{ route('enrollment.store') }}" novalidate="novalidate">
                    <div class="modal-body">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-12 mb-2">
                                <label for="lesson_id">{{ __('lesson') }} </label>
                                <select name="lesson_id" id="lesson_id" required class="form-control">
                                    <option>{{ __('select_lesson') }}</option>
                                    @foreach ($lessons as $lesson)
                                        <option value="{{ $lesson->id }}">{{ $lesson->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-2">
                                <label for="expire_date">{{ __('expiry_date') }} </label>
                                <input type="datetime-local" class="form-control" name="expiration_at" id="expire_date">
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label>{{ __('enroll_based_on') }} <span class="text-danger">*</span> <i
                                            class="fa fa-question-circle ml-1" aria-hidden="true"
                                            title="{{ __('class_and_class_section_exam_info') }}"></i></label><br>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="enroll_based_on" class="enroll_based_on"
                                                    value="0">
                                                {{ __('student') }}
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <label class="form-check-label">
                                                <input type="radio" name="enroll_based_on" class="enroll_based_on"
                                                    value="1" checked="true">
                                                {{ __('class_section') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- class container  --}}
                            <div class="col-12">
                                <div class="student_container" style="display : none">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="student_id">{{ __('student') }} </label>
                                            <select name="student_id" id="filter_student" required style="width: 100%"
                                                class="form-control select2 w-100">
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- class section container --}}
                            <div class="col-12">
                                <div class="class_section_container">
                                    <div class="col-12 mb-2">
                                        <label for="class_section_id">{{ __('class') }} </label>
                                        <select name="class_section_id" id="class_section_id" required
                                            class="form-control">
                                            <option>{{ __('select_class') }}</option>
                                            @foreach ($classSectionsMapped as $class)
                                                <option value="{{ $class['id'] }}">
                                                    {{ $class['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-light border-top">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('close') }}</button>
                        <input class="btn btn-theme" type="submit" value={{ __('create') }} />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        window.actionEvents = {
            "click .edit-data": function(e, value, row, index) {
                $("#edit_id").val(row.id);
                $('#edit_expiry_date').val(row.expiration_local_format)
            }
        };
    </script>

    <script type="text/javascript">
        function queryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
                lesson_id: $('#filter_lesson').val(),
                teacher_id: $('#filter_teacher').val(),
            };
        }
        $("#filter_student").select2({
            ajax: {
                url: `{{ route('students.list', '') }}`, // Replace with your actual endpoint URL
                dataType: 'json',
                delay: 500, // Optional: delay before triggering the request
                data: function(params) {
                    return {
                        search: params.term,
                        class_section_id: $('#class_section_id').val()
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.rows.map(student => {
                            return {
                                id: student.id, // Use a unique identifier for the option
                                text: `${student.first_name} ${student.last_name}` // Display text combining first and last names
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            placeholder: 'Select a student',
            allowClear: true
        });

        $("#add_enrollment").click(function() {
            $("#storeModal").modal("show");
        })
        $(".store-enrollment-form").validate({
            rules: {
                lesson_id: {
                    required: true
                },
                student_id: {
                    required: true
                }
            }
        });
        $(".enroll_based_on").change(function() {
            $('.student_container,.class_section_container').hide();
            if ($(this).val() == 1) {
                $(".class_section_container").show();
                $('.student_container').hide();
            } else {
                $(".class_section_container").hide();
                $('.student_container').show();
            }
        });
        $(".store-enrollment-form").submit(function(e) {
            e.preventDefault();
            let formElement = $(this);
            if (formElement.valid()) {

                let submitButtonElement = formElement.find('input[type="submit"]');
                let url = formElement.attr('action');
                // if (formElement.valid()) {
                let submitButtonText = submitButtonElement.val();

                function beforeSendCallback() {
                    submitButtonElement.attr('disabled', true);
                }

                function mainSuccessCallback(response) {
                    showSuccessToast(response.message);
                    $('#storeModal').modal('hide');
                    $('#table_list').bootstrapTable('refresh');
                    formElement[0].reset();
                }

                function mainErrorCallback(response) {
                    showErrorToast(response.message);
                }

                function finalCallback(response) {
                    submitButtonElement.attr('disabled', false);
                }

                ajaxRequest("POST", url, new FormData(formElement[0]), beforeSendCallback, mainSuccessCallback,
                    mainErrorCallback,
                    finalCallback, false);
            }

        })
    </script>
@endsection
