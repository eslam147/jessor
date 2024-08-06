<link rel="stylesheet" href="{{ url('assets/css/vendor.bundle.base.css') }}" async>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" async />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" async>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
<link rel="stylesheet" href="{{ url('assets/color-picker/color.min.css') }}" async>
@if (session('language') && session('language')->is_rtl)
    <link rel="stylesheet" href="{{ url('assets/css/rtl.css') }}">
@else
    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
@endif
<link rel="stylesheet" href="{{ url('assets/css/datepicker.min.css') }}" async>
<link rel="stylesheet" href="{{ url('assets/css/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ url('assets/css/ekko-lightbox.css') }}">

<link rel="stylesheet" href="{{ url('assets/bootstrap-table/bootstrap-table.min.css') }}">
<link rel="stylesheet" href="{{ url('assets/bootstrap-table/fixed-columns.min.css') }}">
<link rel="stylesheet" href="{{ url('assets/bootstrap-table/reorder-rows.css') }}">

<link rel="shortcut icon" href="{{ url(Storage::url(settingByType('favicon'))) }}" />

@php
    $theme_color = getSettings('theme_color')['theme_color'];
    $secondary_color = getSettings('secondary_color')['secondary_color'];

    $login_image = url(Storage::url('eschool.jpg'));
    $loginImageSetting = getSettings('login_image');

    if (!empty($loginImageSetting['login_image'])) {
        $login_image = url(Storage::url($loginImageSetting['login_image']));
    }

@endphp
<style>
    :root {
        --image-url: url('{{ $login_image }}');
        --theme-color: {{ $theme_color }};
    }
</style>
<script>
    var baseUrl = `{{ URL::to('/') }}`;
    const onErrorImage = (e) => {
        e.target.src = `{{ global_asset('images/no_image_available.jpg') }}`;
    };
</script>
