@extends('layouts.master')

@section('title', __('user_devices'))

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('user_devices') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list') . ' ' . __('user_devices') }}
                        </h4>
                        <div class="row">
                            <div class="col-12">
                                <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                    data-url="{{ route('user_devices.list') }}" data-click-to-select="true"
                                    data-side-pagination="server" data-pagination="true"
                                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                                    data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                    data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                    data-maintain-selected="true" data-export-types='["txt","excel"]'
                                    data-export-options='{ "fileName": "devices-list-{{ today() }}" ,"ignoreColumn":
                                    ["operate"]}'
                                    data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th scope="col" data-field="id" data-sortable="true" data-visible="false">
                                                {{ __('id') }}
                                            </th>
                                            <th scope="col" data-field="no" data-sortable="false">
                                                {{ __('no.') }}
                                            </th>
                                            <th scope="col" data-field="user_id" data-sortable="false"
                                                data-visible="false">
                                                {{ __('user_id') }}
                                            </th>
                                            <th scope="col" data-field="student" data-sortable="false">
                                                {{ __('student') }}
                                            </th>
                                            <th scope="col" data-field="os" data-sortable="false">
                                                {{ __('os') }}
                                            </th>
                                            <th scope="col" data-field="city" data-sortable="false">
                                                {{ __('city') }}
                                            </th>
                                            <th scope="col" data-field="device_ip" data-sortable="false">
                                                {{ __('device_ip') }}
                                            </th>
                                            <th scope="col" data-field="session_start_at" data-sortable="false">
                                                {{ __('session_started_at') }}
                                            </th>
                                            <th scope="col" data-field="session_end_at" data-sortable="false">
                                                {{ __('session_ended_at') }}
                                            </th>
                                            <th scope="col" data-field="browser" data-sortable="false">
                                                {{ __('browser') }}
                                            </th>
                                            <th data-events="actionEvents" scope="col" data-field="operate"
                                                data-sortable="false">{{ __('action') }}
                                            </th>
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
{{-- userDevicesActionEvents --}}
@section('script')
    <script>
        window.actionEvents = {
            "click .delete_device": function(e, value, row, index) {
                e.preventDefault();
                let item = $(e.target);

                Swal.fire({
                    title: "Are you sure?",
                    text: "to delete this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Delete The Device!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        let data = JSON.stringify({
                            _token: $("meta[name=csrf-token]").attr("content"),
                            _method: "DELETE",
                        });

                        function successCallback(response) {
                            $("#table_list").bootstrapTable("refresh");
                            showSuccessToast(response.message);
                        }

                        function errorCallback(response) {
                            showErrorToast(response.message);
                        }

                        ajaxRequest("DELETE", item.data("url"), data, null, successCallback, errorCallback);
                    }
                });
            },
        };

        function queryParams(p) {
            return {
                limit: p.limit,
                sort: p.sort,
                order: p.order,
                offset: p.offset,
                search: p.search,
            };
        }
    </script>
@endsection
