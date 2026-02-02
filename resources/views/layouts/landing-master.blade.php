<!DOCTYPE html>
<html @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif
    data-nav-layout="horizontal" data-nav-style="menu-click" data-menu-position="fixed" data-theme-mode="light">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="{{ getSetting('site_description') }}">
    <meta name="Author" content="{{ getSetting('site_name') }}">
    <meta name="keywords" content="">

    <!-- Title-->
    <title> {{ getSetting('site_name') }} </title>

    <!-- Favicon -->
    <link rel="icon" href="{{ getSetting('site_logo') }}" type="image/x-icon">

    <!-- Icons CSS -->
    <link href="{{ asset('assets/icon-fonts/icons.css') }}" rel="stylesheet">

    <!-- Bootstrap theme-related localStorage from DB before main.js runs -->
    <script>
        window.__themeSettingsFromDb = @json(isset($themeSettings['settings']) && is_array($themeSettings['settings']) ? $themeSettings['settings'] : null);
    </script>
    <script src="{{ asset('assets/js/set-localstorage-data.js') }}"></script>
    
    @include('layouts.components.landingpage.styles')

    <!-- APP CSS & APP SCSS -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    @if (app()->getLocale() == 'ar')
        <style>
            @import url(https://fonts.googleapis.com/earlyaccess/droidarabicnaskh.css);

            body {
                font-family: 'Droid Arabic Naskh', 'Roboto', serif;
            } 
        </style> 
    @endif
    @yield('styles')

</head>

<body class="landing-body">

    <!-- Start::main-switcher -->
    @include('layouts.components.landingpage.switcher')
    <!-- End::main-switcher -->

    <div class="landing-page-wrapper">

        <!-- Start::main-header -->
        @include('layouts.components.landingpage.main-header')
        <!-- End::main-header -->

        <!-- Start::main-sidebar -->
        @include('layouts.components.landingpage.main-sidebar')
        <!-- End::main-sidebar -->

        <!-- Start::app-content -->
        <div class="main-content landing-main px-0">

            @yield('content')

        </div>
        <!-- End::main-content -->

    </div>
    <!--app-content closed-->

    @yield('modals') 

    @include('layouts.components.landingpage.footer')
    
    <!-- Scripts -->
    @include('layouts.components.landingpage.scripts')

</body>

</html>
