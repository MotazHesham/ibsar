
<!-- Bootstrap Css -->
<link id="style" href="{{asset('assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

<!-- Node Waves Css -->
<link href="{{asset('assets/libs/node-waves/waves.min.css')}}" rel="stylesheet">

<!-- SwiperJS Css -->
<link rel="stylesheet" href="{{asset('assets/libs/swiper/swiper-bundle.min.css')}}">

<!-- Color Picker Css -->
<link rel="stylesheet" href="{{asset('assets/libs/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/libs/@simonwep/pickr/themes/nano.min.css')}}">

<!-- Choices Css -->
<link rel="stylesheet" href="{{asset('assets/libs/choices.js/public/assets/styles/choices.min.css')}}">

<!-- Toastify CSS -->
<link rel="stylesheet" href="{{asset('assets/libs/toastify-js/src/toastify.css')}}">

<script>
        if (localStorage.zenodarktheme) {
        document.querySelector("html").setAttribute("data-theme-mode", "dark")
        }
        if (localStorage.zenortl) {
        document.querySelector("html").setAttribute("dir", "rtl")
        document.querySelector("#style")?.setAttribute("href", "{{asset('assets/libs/bootstrap/css/bootstrap.rtl.min.css')}}");
        }
</script>
