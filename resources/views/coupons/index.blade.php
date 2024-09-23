@extends('layouts.master')

@section('title')
    {{ __('coupons') }}
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
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
                        @include('coupons.partials.filter')

                        <div class="row">
                            <div class="col-12">
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
                                            <th scope="col" data-field="used_count" data-sortable="true">
                                                {{ __('used_count') }}
                                            </th>
                                            <th scope="col" data-field="expiry_date" data-sortable="true">
                                                {{ __('expiry_date') }}
                                            </th>
                                            <th scope="col" data-field="class_name" data-sortable="true">
                                                {{ __('class_name') }}
                                            </th>
                                            <th scope="col" data-field="subject_name" data-sortable="true">
                                                {{ __('subject') }}
                                            </th>
                                            <th scope="col" data-field="tags_imploded" data-sortable="true">
                                                {{ __('tags') }}
                                            </th>
                                            <th scope="col" data-field="type" data-sortable="true">
                                                {{ __('type') }}
                                            </th>
                                            <th scope="col" data-field="price" data-sortable="true">
                                                {{ __('price') }}
                                            </th>

                                            <th scope="col" data-field="maximum_usage" data-sortable="true">
                                                {{ __('maximum_usage') }}
                                            </th>
                                            @canany(['coupons-delete', 'coupons-edit'])
                                                <th scope="col" data-field="status"
                                                    data-sortable="false">{{ __('status') }}</th>

                                                <th data-events="actionEvents" scope="col" data-field="operate"
                                                    data-sortable="false">{{ __('action') }}</th>
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
    @include('coupons.partials.modals.show')
@endsection

@section('script')
    <script>
        const export_url = `{{ route('coupons.export') }}`;
        window.actionEvents = {
        "click .active_coupon": function (e, value, row, index) {
            e.preventDefault();
            let item = $(this);

            if (e.target !== this) {
                item = $(e.target).closest("a");
            }

            let href = item.attr("href");
            let status = item.data("status");

            changeStatus(href, status);
        },
        "click .view_coupon": function (e, value, row, index) {
            e.preventDefault();
            let item = $(this);

            if (e.target !== this) {
                item = $(e.target).closest("a");
            }

            let url = item.attr("href");

            function successCallback(response) {
                let couponData = response.data;
                $("#coupon-code").text(couponData.code);
                $("#coupon-expiry-date").text(couponData.expiry_date);

                $("#coupon-price").text(couponData.price);
                $("#coupon-is-disabled").text(
                    couponData.is_disabled ? "Yes" : "No"
                );
                $("#coupon-maximum-usage").text(couponData.maximum_usage);
                $("#coupon-type").text(couponData.type);
                $("#coupon-created_at").text(couponData.created_at);

                $("#show_coupon_modal").modal("show");
            }

            function errorCallback(response) {
                showErrorToast(response.message);
            }

            ajaxRequest("get", url, {}, null, successCallback, errorCallback);
        },
        "click .disable_coupon": function (e, value, row, index) {
            e.preventDefault();
            let item = $(this);

            if (e.target !== this) {
                item = $(e.target).closest("a");
            }

            let href = item.attr("href");
            let status = item.data("status");

            Swal.fire({
                title: "Are you sure?",
                text: "to change status of this coupon!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Change it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    changeStatus(href, status);
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
            medium_id: $("#coupon_filter_medium_id").val(),
            price: $("#coupon_filter_price").val(),
            class_id: $("#coupon_filter_class_id").val(),
            teacher_id: $("#coupon_filter_teacher_id").val(),
            subject_id: $("#coupon_filter_subject_id").val(),
            lesson_id: $("#coupon_filter_lesson_id").val(),
            tags: $("#coupon_filter_tags").val(),
            status: $("#coupon_filter_status").val(),

            purchased: $("#coupon_filter_used").prop("checked") ? "true" : null,
        };
    }
    
        const initialData = {
            'classes': @json($mediums->pluck('class', 'id')->toArray()),
            'classSubjects': @json($classSubjects),
            'teachers': @json($teachers),
            'lessons': @json($lessons->groupBy('teacher_id')->toArray()),
            'tags': @json($tags->pluck('name')->toArray()),
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="{{ global_asset('assets/js/custom/coupon.js') }}?v=1.0.6"></script>
@endsection
