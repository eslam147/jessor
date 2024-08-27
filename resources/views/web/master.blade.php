<!DOCTYPE html>
@php
    $lang = Session::get('language');
@endphp
<html lang="en" dir="{{ isset($lang) && $lang->is_rtl ? 'rtl' : 'ltr' }}">
@php
    $staticSettings = cachedWebSettings();

    $about = $staticSettings->firstWhere('name', 'about_us');
    $whoweare = $staticSettings->firstWhere('name', 'who_we_are');
    $teacher = $staticSettings->firstWhere('name', 'teacher');
    $photo = $staticSettings->firstWhere('name', 'photos');
    $video = $staticSettings->firstWhere('name', 'videos');
    $question = $staticSettings->firstWhere('name', 'question');
@endphp
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ loadTenantMainAsset('favicon')url() }}" />
    @yield('css')
</head>

<body class="sidebar-fixed">
    <div class="container-scroller">
        {{-- header --}}
        @include('web.header')
        <div class="page-body-wrapper">
            <div class="main-panel">
                @yield('content')
            </div>
        </div>
    </div>
    {{-- footer --}}
    @include('web.footer')
    @yield('js')

    @yield('script')
</body>

</html>
