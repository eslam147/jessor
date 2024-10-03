@extends('centeral.admin.layouts.master')

@push('title', 'Tenants')

@push('css')
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard-admin-assets/src/plugins/src/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('dashboard-admin-assets/src/plugins/css/light/table/datatable/dt-global_style.css') }}">
    <!-- END PAGE LEVEL STYLES -->
    <link rel="stylesheet" href="{{ asset('dashboard-admin-assets/src/assets/css/light/forms/switches.css') }}">

    <!-- BEGIN THEME GLOBAL STYLES -->
    <link rel="stylesheet" href="{{ asset('dashboard-admin-assets/src/plugins/src/sweetalerts2/sweetalerts2.css') }}">
    <!-- END THEME GLOBAL STYLES -->
@endpush






@section('content')
    <div class="col-12">
        <div class="middle-content container">
            <div class="row layout-top-spacing mb-4">
                <div class="widget-content widget-content-area p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="m-0">المشاريع</h5>
                        <div class="short_actions">
                            <a href="{{ route('central.tenants.create') }}" class="btn btn-light-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-plus">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                اضافة جديد
                            </a>
                            {{-- @admincan('create-admin')
                        @endadmincan --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Domain</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tenants as $tenant)
                            <tr>
                                <td scope="row">{{ $tenant->id }}</td>
                                <td>{{ $tenant->domains()->value('domain') }}</td>
                                <td>
                                    <a class="btn btn-sm btn-primary" href="#">Edit</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
                {{-- {!! $dataTable->table(['class' => 'table table-striped dt-table-hover dataTable text-center']) !!} --}}
            </div>
        </div>
    </div>
@endsection



@push('js')
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('dashboard-admin-assets/src/plugins/src/table/datatable/datatables.js') }}"></script>
    <!-- END PAGE LEVEL SCRIPTS -->

    <!-- BEGIN THEME GLOBAL STYLE -->
    <script src="{{ asset('dashboard-admin-assets/src/plugins/src/sweetalerts2/sweetalerts2.min.js') }}"></script>
    <script src="{{ asset('js/button-confirmation-datatable.js') }}?v=123"></script>
    <script src="{{ asset('js/update-status-mode.js') }}"></script>
    <!-- END THEME GLOBAL STYLE -->

    {{-- {!! $dataTable->scripts() !!} --}}
@endpush
