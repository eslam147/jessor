@extends('errors.minimal')
@section('title', __('Service Unavailable'))
@section('code', '503')
@section('lottie', '644e49f5-3586-4929-b85e-6d2edfdb98aa/FHd5u8mX2e.json')
@section('message')

    <div>
        <p>
            <b>{{ __('Service Unavailable') }}</b>
        </p>
        <p class="mb-0 p-0">We are currently undergoing scheduled maintenance. We apologize for any inconvenience this may
            cause.</p>
        <p class="mb-0 p-0">Our team is working hard to restore the service as quickly as possible. Please check back soon.
        </p>
        <p class="mb-0 p-0">Thank you for your patience and understanding!</p>

    </div>
@endsection
