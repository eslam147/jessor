<header class="topHeader">
    <div class="container">
        <div class="row divWrapper">
            <div class="col-8 col-sm-8 col-md-8 col-lg-6">
                <div class="leftDiv">
                    <span class="commonSpan">
                        <i class="fa-solid fa-envelope-circle-check"></i>
                        <a
                            href="mailto:{{ $settings['school_email'] ?? '' }}">{{ isset($settings['school_email']) ? $settings['school_email'] : 'Schoolinfous@jessor' }}</a>
                    </span>
                    <span class="commonSpan"><i class="fa-solid fa-phone-volume"></i>
                        <a
                            href="tel:{{ $settings['school_phone'] ?? '' }}">{{ isset($settings['school_phone']) ? $settings['school_phone'] : '( +91 ) 12345 67890' }}</a>
                    </span>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-md-4 col-lg-6">
                <div class="rightDiv">
                    <div class="langs">
                        @if (count($languagesEnabled))
                            <div class="dropdown">
                                <a class="dropdown-toggle " href="#" role="button" id="dropdownMenuLink"
                                    data-bs-toggle="dropdown" aria-expanded="true">
                                    {{ LaravelLocalization::getCurrentLocaleName() }}
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink" data-bs-popper="none">
                                    @foreach ($languagesEnabled as $lang)
                                        <li>
                                            <a rel="alternate" class="dropdown-item" hreflang="{{ $lang->code }}"
                                                href="{{ LaravelLocalization::getLocalizedURL($lang->code, null, [], true) }}">
                                                {{ $lang->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <span>
                                {{ LaravelLocalization::getCurrentLocaleName() }}

                            </span>
                        @endif

                    </div>

                    <span class="commonSpan">Follow Us:</span>
                    <span>
                        <span class="commonSpan">
                            <a href="{{ isset($settings['facebook']) ? $settings['facebook'] : '' }}" target="_blank">
                                <i class="fa-brands fa-square-facebook"></i>
                            </a>
                        </span>
                        <span class="commonSpan">
                            <a href="{{ isset($settings['linkedin']) ? $settings['linkedin'] : '' }}" target="_blank">
                                <i class="fa-brands fa-linkedin"></i>
                            </a>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- topHeader ends here  -->

<header class="navbar">
    <div class="container">
        <div class="navbarWrapper">
            <div class="navLogoWrapper">
                <div class="navLogo">
                    <a href="{{ url('/') }}">
                        <img src="{{ loadTenantMainAsset('logo1', global_asset('assets/logo.svg')) }}" height="50px"
                            width="150px" alt="logo">
                    </a>
                </div>
            </div>
            <div class="menuListWrapper">
                <ul class="listItems">
                    <li>
                        <a href="{{ url('/') }}">{{ __('home') }}</a>
                    </li>
                    @if ($about || $whoweare || $teacher)
                        <li>
                            <a href="{{ route('about.us') }}">{{ __('about-us') }}</a>
                        </li>
                    @endif

                    @if ($photo || $video)
                        <li>
                            <div class="dropdown">
                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                    id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('gallery') }}
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    @if ($photo)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('photo') }}">
                                                {{ __('photos') }}
                                            </a>
                                        </li>
                                    @endif
                                    <hr>
                                    @if ($video)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('video') }}">
                                                {{ __('videos') }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if ($question)
                        <li>
                            <a href="{{ route('contact.us') }}"> {{ __('contact_us') }}</a>
                        </li>
                    @endif
                    <li>
                        @auth
                            @if (auth()->user()->hasRole('Student'))
                                <a href="{{ route('home.index') }}" class="mb-3 commonBtn panel_btn">
                                    {{ __('my_dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('home') }}" class="text-primary commonBtn mb-3 panel_btn">
                                    {{ __('panel') }}
                                </a>
                            @endif
                        @endauth
                        @guest('web')
                            <button type="submit" class="commonBtn mb-3" name="contactbtn"
                                onclick="window.location='{{ url('login') }}'">
                                {{ __('login') }}
                            </button>
                            <a href="{{ route('signup.index') }}" class="commonBtn mb-3" name="contactbtn">
                                {{ __('signup') }}
                            </a>
                        @endguest
                    </li>
                </ul>
                <div class="hamburg">
                    <span data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i
                            class="fa-solid fa-bars"></i></span>
                </div>
            </div>
        </div>



        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <div class="navLogoWrapper">
                    <div class="navLogo">
                        <img src="{{ loadTenantMainAsset('logo1', global_asset('assets/logo.svg')) }}" height="50px"
                            width="150px" alt="logo">
                    </div>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="listItems">
                    <li>
                        <a href="{{ url('/') }}">{{ __('home') }}</a>
                    </li>
                    @if ($about || $whoweare || $teacher)
                        <li>
                            <a href="{{ route('about.us') }}">{{ __('about-us') }}</a>
                        </li>
                    @endif
                    @if ($photo || $video)
                        <li>
                            <div class="dropdown">
                                <a class="btn btn-secondary dropdown-toggle" href="#" role="button"
                                    id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    Gallery
                                </a>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    @if ($photo)
                                        <li><a class="dropdown-item"
                                                href="{{ route('photo') }}">{{ __('photos') }}</a></li>
                                    @endif
                                    <hr>
                                    @if ($video)
                                        <li><a class="dropdown-item"
                                                href="{{ route('video') }}">{{ __('videos') }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif
                    @if ($question)
                        <li>
                            <a href="{{ route('contact.us') }}">{{ __('contact_us') }}</a>
                        </li>
                    @endif
                    <li>
                        @auth
                            @if (auth()->user()->hasRole('Student'))
                                <a href="{{ route('home.index') }}" class="panel_btn commonBtn ">
                                    {{ __('my_dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('home') }}" class="panel_btn commonBtn ">
                                    {{ __('panel') }}
                                </a>
                            @endif
                        @endauth
                        @guest('web')
                            <button type="submit" class="commonBtn mb-3" name="contactbtn"
                                onclick="window.location='{{ url('login') }}'">
                                {{ __('login') }}
                            </button>
                            <a href="{{ route('signup.index') }}" class="commonBtn mb-3" name="contactbtn">
                                {{ __('signup') }}
                            </a>
                        @endguest
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
