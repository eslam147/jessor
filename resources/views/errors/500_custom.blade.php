@extends('errors.minimal')
@section('title', __('Server Error'))
@section('code', '500')
@section('icon')
    @section('lottie','e4548ca3-600e-4f2c-a510-9bbcb267cd52/KJTaEu8lid.json')
@endsection
@section('message')
    <div>
        <p>
            Oops ! Something went wrong.
        </p>
        @if (isset($eventId))
            <p>Error ID: <b>{{ $eventId }}</b>. <br> Please share this with our support team.</p>
        @endif
    </div>
@endsection
