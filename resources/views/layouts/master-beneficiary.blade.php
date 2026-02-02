<!DOCTYPE html> 
<html @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif
    data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">
<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="Description" content="{{ getSetting('site_description') }}">
    <meta name="Author" content="{{ getSetting('site_name') }}">
    <meta name="keywords" content="">
    <!-- Title-->
    <title> {{ getSetting('site_name') }} </title>

    <!-- Favicon -->
    <link rel="icon" href="{{ getSetting('site_logo') }}" type="image/x-icon">

    <!-- Bootstrap theme-related localStorage from DB before main.js runs -->
    <script>
        window.__themeSettingsFromDb = @json(isset($themeSettings['settings']) && is_array($themeSettings['settings']) ? $themeSettings['settings'] : null);
    </script>
    <script src="{{ asset('assets/js/set-localstorage-data.js') }}"></script>
    
    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- ICONS CSS -->
    <link href="{{ asset('assets/icon-fonts/icons.css') }}" rel="stylesheet">

    @include('layouts.components.styles')

    <!-- APP CSS & APP SCSS -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    @yield('styles')
    @if (app()->getLocale() == 'ar')
        <style>
            @import url(https://fonts.googleapis.com/earlyaccess/droidarabicnaskh.css);

            body {
                font-family: 'Droid Arabic Naskh', 'Roboto', serif;
            }

            .main-menu i {
                font-size: 1.1rem;
                padding: 0 0 0 10px;
            }
        </style>
    @else
        <style>
            .main-menu i {
                font-size: 1.1rem;
                padding: 0 10px 0 0;
            }
        </style>
    @endif

</head>

<body class=""> 

    <!-- Loader -->
    <div id="loader">
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="">
    </div>
    <!-- Loader -->

    <div class="page">

        <!-- Start::main-header -->
        @include('layouts.components.beneficiary.main-header')
        <!-- End::main-header -->

        <!-- Start::main-sidebar -->
        @include('layouts.components.beneficiary.main-sidebar')
        <!-- End::main-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content app-content">
            <div class="container-fluid">

                @if($errors->count() > 0)
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-check-line me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')

            </div>
        </div>
        <!-- End::content  -->

        <!-- Start::main-footer -->
        @include('layouts.components.footer')
        <!-- End::main-footer -->
        

        @yield('modals')

        {{-- ajax modal --}}
        <div class="modal fade" id="ajaxModal" tabindex="-1" aria-labelledby="ajaxModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                </div>
            </div>
        </div>

        {{-- ajax offcanvas --}}
        <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="ajaxOffcanvas" aria-labelledby="ajaxOffcanvasLabel">

        </div>
        
        {{-- mail show offcanvas --}}
        <div class="offcanvas offcanvas-end mail-info-offcanvas" data-bs-scroll="true" tabindex="-1" id="mail-show-offcanvas" aria-labelledby="mail-show-offcanvasLabel">

        </div>

        <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
    </div> 

    <!-- Scripts -->
    @include('layouts.components.scripts')

    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>

    <!-- Custom-Switcher JS -->
    <script type="module" src="{{ asset('assets/js/custom-switcher.js') }}"></script>

    <!-- App JS-->
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>

</html>
