@extends('layouts.master')

@section('title')
    {{ __('enrollments') }}
@endsection

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
                                            <option value="{{ $lesson->id }}">
                                                {{ $lesson->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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
                                            <th scope="col" data-field="teacher" data-sortable="false">
                                                {{ __('teacher_name') }}
                                            </th>

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
                        <form class="pt-3 edit-enrollment-form" id="edit-form" method="POST" action="{{ route('enrollment.update', '')}}"
                            novalidate="novalidate">
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
    </script>
@endsection
