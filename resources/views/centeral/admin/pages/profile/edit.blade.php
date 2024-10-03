@extends('centeral.admin.layouts.master')
@push('title','اعدادات الحساب')

@section('css')
    <!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
    <link rel="stylesheet" type="text/css" href="{{ asset('adminAssets/plugins/ltr/dropify/dropify.min.css') }}">
    <link href="{{ asset('adminAssets/assets/css/ltr/users/account-setting.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL CUSTOM STYLES -->
@endsection


@section('content')
    <!--  BEGIN CONTENT AREA  -->
    <div class="layout-px-spacing">

        <div class="account-settings-container layout-top-spacing">

            <div class="account-content">
                <div class="scrollspy-example" data-spy="scroll" data-target="#account-settings-scroll" data-offset="-100">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 layout-spacing">

                            <form id="contact" action="{{ route('admin.profile.updateInfo', $user->id) }}"  method="post" class="section contact">
                                @csrf
                                @method('PUT')
                                <div class="info">
                                    <h5 class="">Info</h5>
                                    <div class="row">
                                        <div class="col-md-11 mx-auto">
                                            <div class="row">

                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <label for="name">الاسم</label>
                                                        <input type="text" class="form-control mb-4  @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}">
                                                    </div>
                                                    @error('name')
                                                        <div class="error text-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <label for="email">البريد</label>
                                                        <input type="text" class="form-control mb-4  @error('email') is-invalid @enderror" name="email" value="{{ $user->email }}">
                                                    </div>
                                                    @error('email')
                                                        <div class="error text-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <label for="phone">رقم الهاتف</label>
                                                        <input type="text" class="form-control mb-4  @error('phone') is-invalid @enderror" name="phone" value="{{ $user->phone }}">
                                                    </div>
                                                    @error('phone')
                                                        <div class="error text-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <button class="btn btn-primary">
                                        UPDATE
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6 layout-spacing">

                            <form id="contact" action="{{ route('admin.profile.updatePassword', $user->id) }}"  method="post" class="section contact">
                                @csrf
                                @method('PUT')
                                <div class="info">
                                    <h5 class="">تغيير كلمة السر</h5>
                                    <div class="row">
                                        <div class="col-md-11 mx-auto">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <label for="current_password">كلمة السر الحاليه</label>
                                                        <input type="text" class="form-control mb-4  @error('current_password') is-invalid @enderror" name="current_password">
                                                    </div>
                                                    @error('current_password')
                                                        <div class="error text-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <label for="new_password">كلمة السر الجديده</label>
                                                        <input type="text" class="form-control mb-4  @error('new_password') is-invalid @enderror" name="new_password">
                                                    </div>
                                                    @error('new_password')
                                                        <div class="error text-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-10">
                                                    <div class="form-group">
                                                        <label for="new_confirm_password">تأكيد كلمة السر الجديده</label>
                                                        <input type="text" class="form-control mb-4  @error('new_confirm_password') is-invalid @enderror" name="new_confirm_password">
                                                    </div>
                                                    @error('new_confirm_password')
                                                        <div class="error text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <button class="btn btn-primary">
                                        UPDATE
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('adminAssets/plugins/ltr/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('adminAssets/plugins/ltr/blockui/jquery.blockUI.min.js') }}"></script>
    <script src="{{ asset('adminAssets/assets/js/users/account-settings.js') }}"></script>
@endpush
