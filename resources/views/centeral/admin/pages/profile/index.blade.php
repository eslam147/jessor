@extends('centeral.admin.layouts.master')

@push('title','معلومات الحساب')

@section('page',"معلومات الحساب")

@section('content')
    @include('centeral.admin.pages.profile.partials._account-information')
    @include('centeral.admin.pages.profile.partials._contact-information')
    @include('centeral.admin.pages.profile.partials._account-image')
    @include('centeral.admin.pages.profile.partials._change-password')
@endsection