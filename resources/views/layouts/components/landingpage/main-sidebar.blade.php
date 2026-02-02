<aside class="app-sidebar sticky" id="sidebar">

    <div class="container-xl">
        <!-- Start::main-sidebar -->
        <div class="main-sidebar shadow-none">

            <!-- Start::nav -->
            <nav class="main-menu-container nav nav-pills sub-open">
                <div class="landing-logo-container">
                    <div class="horizontal-logo">
                        <a href="#" class="header-logo">
                            <img src="{{ getSetting('site_logo') }}" alt="">
                        </a>
                    </div>
                </div>
                <div class="slide-left" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                        width="24" height="24" viewBox="0 0 24 24">
                        <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
                    </svg></div>
                <ul class="main-menu">
                    <!-- Start::slide -->
                    <li class="slide">
                        <a class="side-menu__item" href="#home">
                            <span class="side-menu__label">{{ __('frontend.header.home') }}</span>
                        </a>
                    </li>
                    <!-- End::slide -->
                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="#about" class="side-menu__item">
                            <span class="side-menu__label">{{ __('frontend.header.about') }}</span>
                        </a>
                    </li>
                    <!-- End::slide -->
                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="#projects" class="side-menu__item">
                            <span class="side-menu__label">{{ __('frontend.header.projects') }}</span>
                        </a>
                    </li>
                    <!-- End::slide -->
                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="#features" class="side-menu__item">
                            <span class="side-menu__label">{{ __('frontend.header.features') }}</span>
                        </a>
                    </li>
                    <!-- End::slide -->
                    <!-- Start::slide -->
                    <li class="slide">
                        <a href="#contact" class="side-menu__item">
                            <span class="side-menu__label">{{ __('frontend.header.contact') }}</span>
                        </a>
                    </li>
                    <!-- End::slide -->

                </ul>
                <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                        width="24" height="24" viewBox="0 0 24 24">
                        <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z">
                        </path>
                    </svg></div>
                <div class="d-lg-flex d-none">
                    <div class="btn-list d-lg-flex d-none mt-lg-2 mt-xl-0 mt-0">
                        <a href="{{ route('login') }}" class="btn btn-wave btn-success">
                            {{ __('frontend.header.sign_in') }}
                        </a> 
                    </div>
                </div>
            </nav>
            <!-- End::nav -->

        </div>
        <!-- End::main-sidebar -->
    </div>

</aside>
