@extends('centeral.admin.layouts.master')

@push('title','Edit Admin')

@section('content')
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" style="margin-bottom:24px;">
        <form method="POST" action="{{ route('admin-dashboard.admin.update', $admin) }}">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h3>تحديث بيانات الادمن - {{ $admin->full_name }} </h3>
                </div>
                <div class="card-body">
                    @include('admin.pages.admin.partials._form')
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success mt-3">حفظ</button>
                </div>
            </div>
        </form>
    </div>
@endsection


