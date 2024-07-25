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

                                            <th scope="col" data-field="lesson" data-sortable="true">
                                                {{ __('lesson_title') }}
                                            </th>

                                            <th scope="col" data-field="purchase_date" data-sortable="true">
                                                {{ __('purchase_date') }}
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
@endsection

@section('script')
    <script>
        window.actionEvents = {};
    </script>

    <script type="text/javascript">
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
