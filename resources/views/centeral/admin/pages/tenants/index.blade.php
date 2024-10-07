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
                <table class="table text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Domain</th>
                            <th>منذ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tenants as $tenant)
                            <tr>
                                <td scope="row">
                                    {{ $loop->iteration }}
                                </td>
                                <td >{{ $tenant->id }}</td>
                                <td>
                                    @if ($domain = $tenant->domains()->value('domain'))
                                        <a class="btn btn-sm btn-primary" target="_blank" href="//{{ $domain }}">عرض
                                            المنصه</a>
                                    @endif
                                </td>
                                <td>{{ $tenant->created_at->toDateString() }}</td>

                                {{-- <td>
                                    <a class="btn btn-sm btn-primary" href="#">Edit</a>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection



@push('js')
    <script src="{{ asset('dashboard-admin-assets/src/plugins/src/table/datatable/datatables.js') }}"></script>
    <script src="{{ asset('dashboard-admin-assets/src/plugins/src/sweetalerts2/sweetalerts2.min.js') }}"></script>
    <script src="{{ asset('js/button-confirmation-datatable.js') }}?v=123"></script>
    <script src="{{ asset('js/update-status-mode.js') }}"></script>
@endpush
