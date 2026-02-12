@extends('layouts.custom-master')

@php
    $bodyClass = 'bg-white';
@endphp

@section('styles')
    @if(getSetting('login_cover'))
        <style>
            .authentication .authentication-cover:before{
                background-image: url('{{ getSetting('login_cover') }}');
            }
        </style>
    @endif
@endsection

@section('content')
    <div class="row authentication authentication-cover-main mx-0">
        <div class="col-xxl-5 col-xl-7">
            <div class="row justify-content-center align-items-center h-100">
                <div class="col-xxl-8 col-xl-9 col-lg-6 col-md-6 col-sm-8 col-12">
                    <div class="card custom-card my-4 border" style="box-shadow: 5px 6px 26px #bfb8b8;">
                        <div class="card-body p-5">
                            <p class="h5 mb-2 text-center">{{ trans('frontend.volunteer.login_title') }}</p>
                            <form method="POST" action="{{ route('volunteer.login.submit') }}">
                                @csrf
                                @if (session('message'))
                                    <div class="alert alert-info" role="alert">
                                        {{ session('message') }}
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <div class="row gy-3">
                                    <div class="col-xl-12">
                                        <label for="signin-username"
                                            class="form-label text-default">{{ trans('global.login_email') }} / {{ trans('global.identity_num') }}</label>
                                        <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" id="signin-username"
                                            placeholder="{{ trans('global.login_email') }} / {{ trans('global.identity_num') }}" required autocomplete="username"
                                            autofocus value="{{ old('email', null) }}">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-xl-12 mb-2">
                                        <label for="signin-password" class="form-label text-default d-block">
                                            {{ trans('global.login_password') }}
                                        </label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control create-password-input"
                                                id="signin-password" name="password" required
                                                placeholder="{{ trans('global.login_password') }}">
                                            <a href="javascript:void(0);" class="show-password-button text-muted"
                                                onclick="createpassword('signin-password',this)" id="button-addon2"><i
                                                    class="ri-eye-off-line align-middle"></i></a>
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <label class="form-check-label" for="remember">{{ trans('global.remember_me') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid mt-3">
                                    <button type="submit" class="btn btn-primary">{{ trans('global.login') }}</button>
                                    <p class="text-muted mt-3 mb-0 text-center">
                                        <a href="{{ route('login') }}" class="text-primary fw-medium">{{ trans('global.login') }} ({{ app()->getLocale() == 'ar' ? 'لوحة الإدارة' : 'Admin' }})</a>
                                    </p>
                                    <p class="text-muted mt-2 mb-0 text-center">
                                        <a href="{{ route('frontend.volunteer.join') }}" class="text-primary fw-medium">{{ trans('frontend.volunteer.join_title') }}</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-7 col-xl-5 col-lg-12 d-xl-block d-none px-0">
            <div class="authentication-cover overflow-hidden" style="flex-direction: column;">
                <a href="{{ route('home') }}">
                    <img src="{{ getSetting('site_logo') }}" alt="logo"
                        class="authentication-brand desktop-white" style="height: 9rem !important">
                </a>
                <div style="padding: 0 2.5rem;">
                    <p class="text-center text-white mt-3 py-4" style="font-size: 27px;">{{ getSetting('site_login_text', 'جمعية Mostafed') }}</p>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="{{ getSetting('facebook') }}" class="text-white me-2" style=" font-size:2.25rem;    background: #e0e0e0;
    border-radius: 3rem;
    color: #423d3d !important;"><i class="ri-facebook-fill"></i></a>
                    <a href="{{ getSetting('instagram') }}" class="text-white me-2" style=" font-size:2.25rem;    background: #e0e0e0;
    border-radius: 3rem;
    color: #423d3d !important;"><i class="ri-instagram-fill"></i></a>
                    <a href="{{ getSetting('twitter') }}" class="text-white me-2" style=" font-size:2.25rem;    background: #e0e0e0;
    border-radius: 3rem;
    color: #423d3d !important;"><i class="ri-twitter-fill"></i></a>
                    <a href="{{ getSetting('youtube') }}" class="text-white me-2" style=" font-size:2.25rem;    background: #e0e0e0;
    border-radius: 3rem;
    color: #423d3d !important;"><i class="ri-youtube-fill"></i></a>
                    <a href="{{ getSetting('tiktok') }}" class="text-white me-2" style=" font-size:2.25rem;    background: #e0e0e0;
    border-radius: 3rem;
    color: #423d3d !important;"><i class="ri-tiktok-fill"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/show-password.js') }}"></script>
@endsection
