<link rel="stylesheet" href="{{ asset('vendor/simplenotify/css/simple-notify.min.css') }}" />

<link href="{{ asset('dashboard-admin-assets/layouts/vertical-light-menu/css/light/loader.css') }}" rel="stylesheet"
    type="text/css" />
<script src="{{ asset('dashboard-admin-assets/layouts/vertical-light-menu/loader.js') }}"></script>

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
<link href="{{ asset('dashboard-admin-assets/src/bootstrap/css/bootstrap.rtl.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('dashboard-admin-assets/layouts/vertical-light-menu/css/light/plugins.css') }}" rel="stylesheet"
    type="text/css" />
<link rel="stylesheet" href="{{ asset('dashboard-admin-assets/customs/perfect-scrollbar-rtl.css') }}">
<!-- END GLOBAL MANDATORY STYLES -->

<link rel="stylesheet" href="{{ asset('dashboard-admin-assets/customs/custom-datatable.css') }}">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900;1000&display=swap');

    body {
        font-family: 'Cairo', sans-serif;
    }

    .secondary-nav {
        background: #fafafa94;
        backdrop-filter: blur(5px);
    }

    .icon-sm {
        height: 15px !important;
        width: 15px !important;
    }

    .mainloader_sm {
        width: 48px;
        height: 48px;
        border: 5px solid #000;
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
    }

    @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    div.dataTables_wrapper div.dataTables_processing {
        z-index: 99997;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #ffffffb5 !important;
        width: 100px;
    }

    html[dir="rtl"] input {
        direction: rtl;
    }

    .flatpickr-calendar.open {
        z-index: 999999999;
    }
</style>

@stack('css')
