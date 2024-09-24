@extends('errors.minimal')
@section('title', __('Not Found'))
@section('code', '404')
@section('lottie', '1d462ed2-e9b1-435f-8224-91c40d2a92c3/MOPdM5HCcn.json')
@section('message')
    <div>
        <p>
            {{ __('not_found_msg') }}
        </p>
        <a href="{{ route('end_user.home') }}" class="btn btn-primary">
            {{ __('go_back_home') }}
        </a>
    </div>
@endsection
