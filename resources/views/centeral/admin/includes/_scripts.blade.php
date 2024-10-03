<script src="{{ global_asset('vendor/simplenotify/js/simple-notify.min.js') }}"></script>
{{-- <script src="{{ global_asset('js/app.min.js') }}?v3.8"></script> --}}
<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="{{ global_asset('dashboard-admin-assets/src/plugins/src/global/vendors.min.js') }}"></script>
<script src="{{ global_asset('dashboard-admin-assets/src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ global_asset('dashboard-admin-assets/src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ global_asset('dashboard-admin-assets/src/plugins/src/mousetrap/mousetrap.min.js') }}"></script>
<script src="{{ global_asset('dashboard-admin-assets/src/plugins/src/waves/waves.min.js') }}"></script>
<script src="{{ global_asset('dashboard-admin-assets/layouts/vertical-light-menu/app.js') }}"></script>
<script src="{{ global_asset('dashboard-admin-assets/src/assets/js/custom.js') }}"></script>
<!-- END GLOBAL MANDATORY SCRIPTS -->
@include('sweetalert::alert')
@stack('js')