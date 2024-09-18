@extends('web.master')
@section('css')
    <link rel="stylesheet" href="{{ url('/assets/css/frontend.css') }}">
@endsection
<!-- navbar ends here  -->
@section('content')
    <div class="main">
        <section class="content">
            <div class="theme-primary container">
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header no-border">
                                <h4 class="box-title">{{ __('teachers') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row get_teachers w-100">
                        @include('web.get_teachers')
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('js')
    <script src="{{ url('/assets/js/teachers.js') }}"></script>
@endsection
