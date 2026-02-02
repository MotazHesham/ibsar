<!DOCTYPE html>
{{-- Direction and theme_mode (light/dark) come from localStorage via new-switcher.js; all other attributes from DB --}}
<html @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif
    @if(isset($themeSettings))
        @foreach($themeSettings['attributes'] as $key => $value)
            {{ $key }}="{{ $value }}"
        @endforeach
    @endif
    data-theme-mode="light"
    @if(isset($themeSettings) && !empty($themeSettings['css_variables']))
        style="{{ implode(' ', array_map(function($key, $value) { return $key . ': ' . $value; }, array_keys($themeSettings['css_variables']), $themeSettings['css_variables'])) }}"
    @endif>

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

    <!-- Start::main-switcher -->
    @include('layouts.components.switcher')
    <!-- End::main-switcher -->

    <!-- Loader -->
    <div id="loader">
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="">
    </div>
    <!-- Loader -->

    <div class="page">

        <!-- Start::main-header -->
        @include('layouts.components.main-header')
        <!-- End::main-header -->

        <!-- Start::main-sidebar -->
        @include('layouts.components.main-sidebar')
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
                
                @yield('content')

            </div>
        </div>
        <!-- End::content  -->

        <!-- Start::main-footer -->
        @include('layouts.components.footer')
        <!-- End::main-footer -->

        <!-- Start::main-modal -->
        @include('layouts.components.modal')
        <!-- End::main-modal -->

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

    <!-- Theme API URLs + initial theme (avoid flash: DB theme applied before any localStorage) -->
    <script>
        window.THEME_UPDATE_URL = "{{ route('admin.settings.updateTheme') }}";
        window.THEME_GET_SETTINGS_URL = "{{ route('admin.settings.getThemeSettings') }}";
        window.__INITIAL_THEME_SETTINGS__ = @json(isset($themeSettings['settings']) ? $themeSettings['settings'] : []);
    </script> 
    <!-- New Switcher JS (direction + lighting = localStorage; rest = DB) -->
    <script type="module" src="{{ asset('assets/js/new-switcher.js') }}"></script> 
    <!-- App JS-->
    <script src="{{ asset('assets/js/custom.js') }}"></script> 
    
</body>

</html>
