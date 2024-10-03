@extends('centeral.admin.layouts.master')
@push('title','اعدادات الموقع')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
    <link rel="stylesheet" href="{{ asset('dashboard-admin-assets/src/assets/css/light/components/tabs.css') }}">
@endpush
@push('page','اعدادات الموقع')

@section('content')
    <div id="tabsSimple" class="col-xl-12 col-12 layout-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h4>اعدادات عامه</h4>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <div class="simple-tab">
                    @include('admin.pages.settings.partials.nav')
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <form action="{{ route('admin-dashboard.setting.general.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-xl-8 col-8 layout-spacing">
                    <div class="statbox widget box box-shadow">
                        <div class="widget-content widget-content-area">
                            <div class="form-group row  mb-4">
                                <label for="siteNameSetting" class="col-sm-2 col-form-label col-form-label-sm">اسم
                                    الموقع</label>
                                <div class="col-sm-10">
                                    <input type="text" name="site_name" class="form-control form-control-sm" id="siteNameSetting"
                                        value="{{ old('site_name', $settings->site_name) }}">
                                        @error('site_name')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="statbox widget box box-shadow">
                        <div class="widget-content widget-content-area">
                            <div class="form-group row  mb-4">
                                <label for="siteNameSetting" class="col-sm-2 col-form-label col-form-label-sm">
                                    اللوجو</label>
                                <div class="col-sm-12">
                                    <input type="file" data-show-remove="false" data-default-file="{{ asset($settings->site_logo) }}"
                                        name="site_logo" id="site_logo">
                                    @error('site_logo')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="statbox widget box box-shadow">
                        <div class="widget-content widget-content-area">
                            <div class="form-group row mb-4">
                                <label for="siteNameSetting" class="col-form-label">
                                    Fav Icon
                                </label>
                                <div class="col-sm-12">
                                    <input type="file" data-show-remove="false" data-default-file="{{ asset($settings->site_favicon) }}"
                                        name="site_favicon" id="site_favicon">
                                    @error('site_favicon')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="statbox mt-3 widget box box-shadow">
                        <div class="widget-content widget-content-area m-auto text-center">
                            <button class="btn btn-primary">تحديث</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
    <script>
        $('#site_logo,#site_favicon').dropify();
    </script>
@endpush