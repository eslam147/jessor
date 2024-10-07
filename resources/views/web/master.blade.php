<!DOCTYPE html>
{{-- @php
    $lang = Session::get('language');
@endphp --}}
<html lang="{{ LaravelLocalization::getCurrentLocale() }}" dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
@php

    $about = cachedWebSettings()->firstWhere('name', 'about_us');
    $whoweare = cachedWebSettings()->firstWhere('name', 'who_we_are');
    $teacher = cachedWebSettings()->firstWhere('name', 'teacher');
    $photo = cachedWebSettings()->firstWhere('name', 'photos');
    $video = cachedWebSettings()->firstWhere('name', 'videos');
    $question = cachedWebSettings()->firstWhere('name', 'question');
@endphp

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ loadTenantMainAsset('favicon') }}" />
    <!-- bootstrap  -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="{{ global_asset('assets/css/webstyle.css') }}">
    <link rel="stylesheet" href="{{ global_asset('assets/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ global_asset('assets/css/ekko-lightbox.css') }}">

    @if (LaravelLocalization::getCurrentLocaleDirection() == 'rtl')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
        <style>
            body,
            .navbar ul li a {
                font-family: "Cairo", sans-serif !important;
            }

            .fa-arrow-right,
            .fa-circle-chevron-right,
            .fa-arrow-left {
                transform: rotate(180deg);
            }

            .topHeader .leftDiv span:first-child {
                border-left: 1px solid #29363f;
                padding-left: 12px;
                border-right: unset;
            }

            .langs {
                border-left: 1px solid #29363f;
                padding-left: 15px;

            }

            .topHeader .leftDiv span:first-child {
                border-right: none;
                border-left: 1px solid #29363f;
                padding-right: unset;
                padding-left: 12px;
            }
        </style>
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .langs {
                border-right: 1px solid #29363f;
                padding-right: 15px;
            }
        </style>
    @endif

    <style>
        .navbar ul li a.panel_btn {
            color: #fff !important;
            background: var(--primary-color) !important;
            /* font-size: 1.25rem !important; */
            /* text-decoration: underline !important; */
        }
    </style>
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

    @include('web.footer')
    @yield('js')

    @yield('script')
</body>
</html>
