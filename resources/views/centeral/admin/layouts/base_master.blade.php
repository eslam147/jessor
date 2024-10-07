<!DOCTYPE html>
<html lang"{{ app()->getLocale() }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="lang" content="{{ app()->getLocale() }}" />
    <title>Jessor</title>
    <link rel="shortcut icon" href="{{ global_asset('assets/logo.svg') }}">
    @include('centeral.admin.includes._styles')
</head>
<body class="layout-boxed">
    @include('centeral.admin.includes._loader')
    <!-- BEGIN LOADER -->
    @yield('main_content')
    <!-- END MAIN CONTAINER -->
    @include('centeral.admin.includes._scripts')
</body>
</html>