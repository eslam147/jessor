@extends('centeral.admin.layouts.master')

@push('css')
    {{-- <link rel="stylesheet" href="{{ asset('dashboard-admin-assets/src/assets/css/light/dashboard/dash_1.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard-admin-assets/src/assets/css/light/dashboard/dash_2.css') }}"> --}}
@endpush

@section('content')
    <div class="admin">
        <h3>{{ __('dashboard') }}</h3>
    </div>

@endsection
