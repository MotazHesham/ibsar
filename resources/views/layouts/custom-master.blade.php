<!DOCTYPE html>
{{-- Direction and theme_mode (light/dark) come from localStorage via new-switcher.js; all other attributes from DB --}}
<html @if (app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif
    @if (isset($themeSettings)) @foreach ($themeSettings['attributes'] as $key => $value)
            {{ $key }}="{{ $value }}"
        @endforeach @endif
    data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="light" data-toggled="close"
    @if (isset($themeSettings) && !empty($themeSettings['css_variables'])) style="{{ implode(' ',array_map(function ($key, $value) {return $key . ': ' . $value;},array_keys($themeSettings['css_variables']),$themeSettings['css_variables'])) }}" @endif>

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

    <!-- Bootstrap theme-related localStorage from DB before main.js runs -->
    <script>
        window.__themeSettingsFromDb = @json(isset($themeSettings['settings']) && is_array($themeSettings['settings']) ? $themeSettings['settings'] : null);
    </script>
    <script src="{{ asset('assets/js/set-localstorage-data.js') }}"></script>
    
    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/authentication-main.js') }}"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- ICONS CSS -->
    <link href="{{ asset('assets/icon-fonts/icons.css') }}" rel="stylesheet">

    <!-- APP CSS & APP SCSS -->
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet">

    @yield('styles')

</head>

<body class="{{ $bodyClass }}">

    <!-- Start Switcher -->
    @include('layouts.components.custom-switcher')
    <!-- End Switcher -->

    @yield('content')

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>


    <script>
        // prevent submitting multiple times
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                if (e.target.dataset.submitted) {
                    e.preventDefault();
                    return false;
                }
                e.target.dataset.submitted = 'true';
            }
        }, true);
    </script>
    @yield('scripts')

</body>

</html>
