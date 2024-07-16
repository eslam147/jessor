@extends('layouts.master')

@section('title')
    {{ __('coupons') }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                {{ __('manage_coupons') }}
            </h3>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ __('list_coupons') }}
                        </h4>

                        <div class="row">
                            <div class="col-12">
                                {{-- <table
                                aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                            data-url="{{ route('coupons.list') }}" data-click-to-select="true"
                            data-side-pagination="server" data-pagination="true"
                            data-page-list="[5, 10, 20, 50, 100, 200, All]" data-search="true" data-toolbar="#toolbar"
                            data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                            data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                            data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                            data-maintain-selected="true" data-export-types='["txt","excel"]'
                            data-query-params="CreateLessionQueryParams"
                            data-export-options='{ "fileName": "lesson-list-<?= date('d-m-y') ?>" ,"ignoreColumn":
                            ["operate"]}'
                            data-show-export="true" --}}
                                {{-- > --}}
                                <table aria-describedby="mydesc" class='table' id='table_list' data-toggle="table"
                                    data-url="{{ route('coupons.list') }}" data-click-to-select="true"
                                    data-side-pagination="server" data-pagination="true"
                                    data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-toolbar="#toolbar"
                                    data-show-columns="true" data-show-refresh="true" data-fixed-columns="true"
                                    data-fixed-number="2" data-fixed-right-number="1" data-trim-on-search="false"
                                    data-mobile-responsive="true" data-sort-name="id" data-sort-order="desc"
                                    data-maintain-selected="true" data-export-types='["txt","excel"]'
                                    data-export-options='{ "fileName": "coupon-list-{{ now()->format('d-m-y') }}","ignoreColumn":
                                    ["operate"]}'
                                    data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th scope="col" data-field="id" data-sortable="true" data-visible="false">
                                                {{ __('id') }}</th>
                                            <th scope="col" data-field="no" data-sortable="false">{{ __('no.') }}
                                            </th>

                                            <th scope="col" data-field="code" data-sortable="false">
                                                {{ __('code') }}
                                            </th>
                                            <th scope="col" data-field="used_count" data-sortable="true">{{ __('used_count') }}
                                            </th>
                                            <th scope="col" data-field="created_at" data-sortable="false"
                                                data-visible="false">{{ __('created_at') }}</th>
                                            <th scope="col" data-field="updated_at" data-sortable="false"
                                                data-visible="false">{{ __('updated_at') }}</th>
                                                
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
    window.actionEvents = {
        'click .editdata': function(e, value, row, index) {
            // $('#id').val(row.id);
            // $('#name').val(row.name);
            // $('#status').val(row.status);
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
                search: p.search
            };
        }
    </script>
@endsection
