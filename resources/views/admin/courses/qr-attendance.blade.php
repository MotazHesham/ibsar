<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ getSetting('site_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        @font-face {

            font-family: 'Lalezar';
            src: url('{{ asset('Lalezar-Regular.otf') }}') format('opentype');
            font-weight: normal;
            font-style: normal;
        }
    </style>

</head>

<body style="overflow: hidden">
    <div class="row">
        <div class="col-md-6">
            <div style="background: #ba9a56;margin: 10%;border-radius:25px">
                <div class="text-center p-5">
                    <h5 class=" p-2" style="color:white;font-family:'Lalezar', sans-serif;font-size:48px">تسجيل حضور
                    </h5>
                    <img height="100" style="float: left" src="{{ asset('Vector.png') }}" alt="">
                </div>
                <div class="text-center p-5" style="clear: both">
                    <a href="{{ $course->attendanceRoute() }}" target="_blank">
                        <img src="{{ qrCodeGenerate($course->attendanceRoute()) }}" alt="">
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div>
                <img src="{{ asset('frontend/img/logo.png') }}" alt="" style="float: right">
            </div>
            <div style="clear: both"></div>
            <div style="text-align: right;padding:40px">
                <h3 style="font-family:'Lalezar', sans-serif;font-size:48px;color:#BA9A56">{{ $course->title }}</h3>
                <div style="font-family:'Lalezar', sans-serif;font-size:28px;color:black">
                    {!! $course->short_description !!}
                </div>
            </div>
            <div style="text-align: right;padding:40px;font-size:35px">
                <b style="float: right"> . 1 </b> &nbsp; <span style="text-align: right"> <b>Qr Code</b> أولا قم بمسح
                    رمز</span>
                <div style="clear: both"></div>
                <b style="float: right"> . 2 </b> &nbsp; <span style="text-align: right">ثانيا قم بكتابة رقم الهوية
                    المسجل في الدورة لتسجيل حضورك في الدورة </span>
                <div style="clear: both"></div>
                <b style="float: right"> . 3 </b> &nbsp; <span style="text-align: right">ثالثا انقر اثبات الحضور </span>
                <div style="clear: both"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
