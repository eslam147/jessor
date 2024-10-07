@extends('centeral.admin.layouts.master')
@push('title', 'اضافة مشرف جديد')
@section('content')
    <div class="col-12">
        <div class="middle-content container">
            <div class="row layout-top-spacing mb-4">
                <div class="widget-content widget-content-area p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="m-0">اضافه جسر جديد</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <form method="POST" action="{{ route('central.tenants.store') }}">
            @csrf
            <div class="card">
                <div class="card-body">
                    @include('centeral.admin.pages.tenants.partials._form') </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-success">
                        انشاء
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
