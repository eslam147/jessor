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
    <link rel="shortcut icon" href="{{ loadTenantMainAsset('favicon') }}" />
    <link rel="stylesheet" href="{{ global_asset('student/css/vendors_css.css') }}">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <!-- Style-->
    <link rel="stylesheet" href="{{ global_asset('student/css/style.css') }}?v=1.0.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/Ionicons/css/ionicons.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/linea-icons/linea.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/glyphicons/glyphicon.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/flag-icon-css/css/flag-icon.css') }}">
    <link rel="stylesheet"
        href="{{ global_asset('student/assets/icons/material-design-iconic-font/css/materialdesignicons.css') }}">
    <link rel="stylesheet"
        href="{{ global_asset('student/assets/icons/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/cryptocoins-master/cryptocoins.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/weather-icons/css/weather-icons.min.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/iconsmind/style.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/icons/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/assets/vendor_components/animate/animate.css') }}">
    <link rel="stylesheet" href="{{ global_asset('student/css/skin_color.css') }}">
    <link rel="icon" href="{{ url('student/images/favicon.png') }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.css">

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
            <!-- زرار علامة + -->
            <div class="social-btn">
                <button id="toggle-btn">+</button>
            </div>
            <!-- أيقونات مواقع التواصل الاجتماعي -->
            <div class="social-icons" id="social-icons">
                <a href="#" id="share-facebook" target="_blank">
                    <img src="https://img.icons8.com/fluent/48/000000/facebook-new.png" alt="Facebook">
                </a>
                <a href="#" id="share-twitter" target="_blank">
                    <img src="https://img.icons8.com/fluent/48/000000/twitter.png" alt="Twitter">
                </a>
                <a href="#" id="share-instagram" target="_blank">
                    <img src="https://img.icons8.com/fluent/48/000000/instagram-new.png" alt="Instagram">
                </a>
                <a href="#" id="share-whatsapp" target="_blank">
                    <img src="https://img.icons8.com/fluent/48/000000/whatsapp.png" alt="WhatsApp">
                </a>
                <a href="#" id="share-email" target="_blank">
                    <img src="https://img.icons8.com/fluent/48/000000/new-message.png" alt="Email">
                </a>
            </div>
        </div>
    </div>
    {{-- footer --}}
    <!-- Toastr JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // الحصول على زرار التبديل وعناصر الأيقونات
            var toggleBtn = $('#toggle-btn');
            var socialIcons = $('#social-icons');

            // إضافة حدث للنقر لزرار التبديل
            toggleBtn.on('click', function() {
                // تبديل عرض الأيقونات مع أنيميشن الانزلاق
                socialIcons.slideToggle(300);

                // تغيير العلامة من "+" إلى "-" والعكس
                if (toggleBtn.text() === '+') {
                    toggleBtn.text('-');
                } else {
                    toggleBtn.text('+');
                }
            });

            // الحصول على رابط الصفحة الحالية
            var pageUrl = encodeURIComponent(window.location.href);

            // إعداد روابط المشاركة لمواقع التواصل الاجتماعي
            var shareLinks = {
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`,
                twitter: `https://twitter.com/intent/tweet?url=${pageUrl}`,
                instagram: `https://www.instagram.com/?url=${pageUrl}`, // ملاحظة: إنستغرام لا يدعم المشاركة المباشرة
                whatsapp: `https://wa.me/?text=${pageUrl}`,
                email: `mailto:?body=${pageUrl}`
            };

            // إضافة خاصية فتح النوافذ المنبثقة لكل أيقونة
            $('#share-facebook').on('click', function(e) {
                e.preventDefault();
                window.open(shareLinks.facebook, 'facebook-share', 'width=600,height=400');
            });

            $('#share-twitter').on('click', function(e) {
                e.preventDefault();
                window.open(shareLinks.twitter, 'twitter-share', 'width=600,height=400');
            });

            $('#share-instagram').on('click', function(e) {
                e.preventDefault();
                window.open(shareLinks.instagram, 'instagram-share', 'width=600,height=400');
            });

            $('#share-whatsapp').on('click', function(e) {
                e.preventDefault();
                window.open(shareLinks.whatsapp, 'whatsapp-share', 'width=600,height=400');
            });

            $('#share-email').on('click', function(e) {
                e.preventDefault();
                window.open(shareLinks.email, 'email-share', 'width=600,height=400');
            });
        });
    </script>
    <script src="{{ url('js/pusher.js') }}"></script>

    @include('web.footer')
    @yield('js')

    @yield('script')
</body>
</html>
