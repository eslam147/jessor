@extends('layouts.master')

@section('title', __('deleted_students'))

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('deleted_students') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('students') }}
                        </h4>
                        <div id="toolbar">
                            <div class="row">
                                <div class="col">
                                    <select name="filter_class_section_id" id="filter_class_section_id"
                                        class="form-control">
                                        <option value="">{{ __('select_class_section') }}</option>
                                        @foreach ($class_section as $class)
                                            <option value={{ $class->id }}>
                                                {{ $class->class->name . ' ' . $class->section->name . ' ' . $class->class->medium->name . ' ' . ($class->class->streams->name ?? ' ') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table table-responsive' id='table_list'
                                    data-toggle="table" data-url="{{ route('students.deleted.list') }}"
                                    data-click-to-select="true" data-side-pagination="server" data-pagination="true"
                                    data-page-list="[5, 10, 20, 50, 100, 200 , 500]" data-search="true"
                                    data-toolbar="#toolbar" data-show-columns="true" data-show-refresh="true"
                                    data-fixed-columns="true" data-fixed-number="2" data-fixed-right-number="1"
                                    data-trim-on-search="false" data-mobile-responsive="true" data-sort-name="id"
                                    data-sort-order="desc" data-maintain-selected="true" data-export-types='["txt","excel"]'
                                    data-export-options='{ "fileName": "students-list-{{ date('d-m-y') }}" ,"ignoreColumn":
                                    ["operate"]}'
                                    data-query-params="studentDetailsqueryParams" data-check-on-init="true">

                                    <thead>
                                        <tr>
                                            <th scope="col" data-field="id" class="w-5" data-sortable="true"
                                                data-visible="false">
                                                {{ __('id') }}</th>
                                            <th scope="col" data-field="no" data-sortable="false">{{ __('no.') }}
                                            </th>
                                            <th scope="col" data-field="user_id" data-sortable="false"
                                                data-visible="false">{{ __('user_id') }}</th>


                                            <th scope="col" data-field="first_name" data-sortable="false">
                                                {{ __('first_name') }}</th>
                                            <th scope="col" data-field="last_name" data-sortable="false">
                                                {{ __('last_name') }}</th>
                                            <th scope="col" data-field="dob" data-sortable="false">{{ __('dob') }}
                                            </th>
                                            <th scope="col" data-field="mobile" data-sortable="false">
                                                {{ __('mobile') }}
                                            </th>
                                            <th scope="col" data-field="email" data-sortable="false">{{ __('email') }}
                                            </th>
                                            <th scope="col" data-field="gender" data-sortable="false">
                                                {{ __('gender') }}
                                            </th>
                                            <th scope="col" data-field="image" data-sortable="false"
                                                data-formatter="imageFormatter">{{ __('image') }}
                                            </th>
                                            <th scope="col" data-field="class_section_id" data-sortable="false"
                                                data-visible="false">
                                                {{ __('class') . ' ' . __('section') . ' ' . __('id') }}</th>
                                            <th scope="col" data-field="class_section_name" data-sortable="false">
                                                {{ __('class') . ' ' . __('section') }}</th>
                                            <th scope="col" data-field="stream_name" data-sortable="false"
                                                data-visible="false">
                                                {{ __('stream') }}</th>
                                            <th scope="col" data-field="category_id" data-sortable="false"
                                                data-visible="false">{{ __('category') . ' ' . __('id') }}</th>
                                            <th scope="col" data-field="category_name" data-sortable="false"
                                                data-visible="false">
                                                {{ __('category') }}</th>
                                            <th scope="col" data-field="admission_date" data-sortable="false">
                                                {{ __('admission_date') }}</th>
                                            @canany(['student-edit', 'student-delete'])
                                                <th data-events="actionEvents" data-width="150" scope="col"
                                                    data-field="operate" data-sortable="false">{{ __('action') }}</th>
                                            @endcanany
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
@endsection
@section('script')
    <script>
        console.log('asdfsadf');


        window.actionEvents = {
            'click .restore_btn': function(e, value, row, index) {
                let btn = $(e.target);

                Swal.fire({
                    title: translations.are_u_sure_restore,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: lang_yes,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = btn.data("url");
                        console.log(url);

                        ajaxRequest(
                            "POST",
                            url,
                            null,
                            null,
                            (response) => {
                                $("#table_list").bootstrapTable("refresh");
                                showSuccessToast(response.message);
                            },
                            (response) => showErrorToast(response.message),
                        );
                    }
                });
            },
            'click .force_delete_btn': function(e, value, row, index) {
                let btn = $(e.target);
                Swal.fire({
                    title: "{{ __('delete_title') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "{{ __('yes_delete') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = btn.data("url");

                        ajaxRequest(
                            "DELETE",
                            url,
                            null,
                            null,
                            (response) => {
                                $("#table_list").bootstrapTable("refresh");
                                showSuccessToast(response.message);
                            },
                            (response) => showErrorToast(response.message),
                        );
                    }
                });
            }

        };
    </script>
@endsection
