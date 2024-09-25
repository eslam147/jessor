<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <a href="#" class="waves-effect waves-light nav-link d-none d-md-inline-block mx-10 push-btn bg-transparent"
            data-toggle="push-menu" role="button">
            <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span
                    class="path3"></span></span>
        </a>
        <!-- Logo -->
        <a href="{{ route('home.index') }}" class="logo">
            <!-- logo-->
            <div class="logo-lg">
                <span class="light-logo overflow-hidden">
                    <img class="" src="{{ loadTenantMainAsset('logo1', global_asset('assets/logo.svg')) }}"
                        alt="logo">
                </span>
            </div>
        </a>
    </div>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <div class="app-menu">
            <ul class="header-megamenu nav">
                <li class="btn-group nav-item d-md-none">
                    <a href="#" class="waves-effect waves-light nav-link push-btn" data-toggle="push-menu"
                        role="button">
                        <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span
                                class="path3"></span></span>
                    </a>
                </li>
                {{-- <li class="btn-group nav-item d-none d-xl-inline-block">
                    <a href="contact_app_chat.html" class="waves-effect waves-light nav-link svg-bt-icon"
                        title="Chat">
                        <i class="icon-Chat"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="btn-group nav-item d-none d-xl-inline-block">
                    <a href="mailbox.html" class="waves-effect waves-light nav-link svg-bt-icon" title="Mailbox">
                        <i class="icon-Mailbox"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                <li class="btn-group nav-item d-none d-xl-inline-block">
                    <a href="extra_taskboard.html" class="waves-effect waves-light nav-link svg-bt-icon"
                        title="Taskboard">
                        <i class="icon-Clipboard-check"><span class="path1"></span><span class="path2"></span><span
                                class="path3"></span></i>
                    </a>
                </li> --}}
            </ul>
        </div>

        <div class="navbar-custom-menu r-side">
            <ul class="nav navbar-nav">
                <li class="btn-group nav-item d-lg-inline-flex d-none">
                    <a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link full-screen"
                        title="Full Screen">
                        <i class="icon-Expand-arrows"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </li>
                {{-- <li class="btn-group d-lg-inline-flex d-none">
                    <div class="app-menu">
                        <div class="search-bx mx-5">
                            <form>
                                <div class="input-group">
                                    <input type="search" class="form-control" placeholder="Search" aria-label="Search"
                                        aria-describedby="button-addon2">
                                    <div class="input-group-append">
                                        <button class="btn" type="submit" id="button-addon3"><i
                                                class="ti-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </li> --}}
                <!-- Notifications -->
                <x-student-dashboard.notification.notification-component />
                <!-- User Account-->
                <li class="dropdown user user-menu">
                    <a href="#" class="waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown"
                        title="User">
                        <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                    <ul class="dropdown-menu animated flipInX">
                        <li class="user-body">
                            <a class="dropdown-item" href="#"><i class="ti-user text-muted me-2"></i>
                                Profile</a>
                            <a class="dropdown-item" href="{{ route('student_dashboard.wallet.index') }}"><i
                                    class="ti-wallet text-muted me-2"></i> My
                                Wallet</a>
                            <a class="dropdown-item" href="{{ route('student-settings.index') }}"><i
                                    class="ti-settings text-muted me-2"></i>
                                Settings</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                    class="ti-lock text-muted me-2"></i> Logout</a>
                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
