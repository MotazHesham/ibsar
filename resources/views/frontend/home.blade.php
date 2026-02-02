@extends('layouts.landing-master')

@section('styles')
@endsection

@section('content')
    @include('frontend.partials.slider', ['sliders' => $sliders])

    <!-- Start:: Section-2 -->
    <section class="section bg-white" id="about">
        <div class="container">
            <div class="heading-section">
                <div class="heading-subtitle"><span class="text-primary fw-semibold">{{ trans('frontend.home.about') }}</span>
                </div>
                <hr class="center-diamond">
                <div class="heading-title">{{ trans('frontend.home.why_choose_us_title') }}</div>
                <div class="heading-description">{{ getSetting('home_about_why_choose_us_text') }}</div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4">
                    <div class="card mb-lg-0 home-cards border shadow-none reveal reveal-active">
                        <div class="card-body d-flex main-card-body">
                            <div class="b-icons fs-3 mx-auto br-style5 flex-shrink-0 bg-primary-transparent"><i
                                    class="bx bx-layer lh-0"></i></div>
                            <div class="ms-3">
                                <h5>{{ getSetting('home_about_why_choose_use_1_title') }}</h5>
                                <p class="mb-0 card-main-content">{{ getSetting('home_about_why_choose_use_1_sub_title') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-lg-0 home-cards border shadow-none reveal reveal-active">
                        <div class="card-body d-flex main-card-body">
                            <div
                                class="b-icons fs-3 mx-auto br-style5 flex-shrink-0 bg-secondary-transparent text-secondary">
                                <i class="bx bx-package lh-0"></i>
                            </div>
                            <div class="ms-3">
                                <h5>{{ getSetting('home_about_why_choose_use_2_title') }}</h5>
                                <p class="mb-0 card-main-content">{{ getSetting('home_about_why_choose_use_2_sub_title') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-lg-0 home-cards border shadow-none reveal reveal-active">
                        <div class="card-body d-flex main-card-body">
                            <div class="b-icons fs-3 mx-auto br-style5 flex-shrink-0 bg-info-transparent text-info"><i
                                    class="bx bx-analyse lh-0"></i></div>
                            <div class="ms-3">
                                <h5>{{ getSetting('home_about_why_choose_use_3_title') }}</h5>
                                <p class="mb-0 card-main-content">{{ getSetting('home_about_why_choose_use_3_sub_title') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End:: Section-2 -->

    <!-- Start:: Section-3 -->
    <section class="section bg-background" id="projects">
        <div class="container">
            <div class="row">
                <div class="heading-section">
                    <div class="heading-subtitle"><span
                            class="text-primary fw-semibold">{{ trans('frontend.home.projects') }}</span></div>
                    <hr class="center-diamond">
                    <div class="heading-title">{{ getSetting('home_projects_title') }}</div>
                    <div class="heading-description">{{ getSetting('home_projects_sub_title') }}</div>
                </div>
                @foreach ($projects as $project)
                    <div class="col-xl-3 col-sm-6">
                        <div class="card bg-custom-white reveal reveal-active custom-card">
                            <div class="position-relative">
                                <a href="javascript:void(0);">
                                    <img class="card-img-top" src="{{ $project->image ? $project->image->getUrl() : '' }}"
                                        alt="blog-image">
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5>{{ $project->title }}</h5>
                                <span class="d-block mb-3">{{ $project->description }}</span>
                                <a href="javascript:void(0);" class="fs-14 text-primary fw-semibold">
                                    {{ trans('frontend.home.read_more') }}
                                    <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- End:: Section-3 -->

    <!-- Start:: Section-4 -->
    <section class="section overflow-hidden bg-white" id="features">
        <div class="container">
            <div class="row">
                <div class="heading-section">
                    <div class="heading-subtitle"><span
                            class="text-primary fw-semibold">{{ trans('frontend.home.achievements') }}</span></div>
                    <hr class="center-diamond">
                    <div class="heading-title">{{ getSetting('home_achievements_title') }}</div>
                    <div class="heading-description">{{ getSetting('home_achievements_sub_title') }}</div>
                </div>
                @foreach ($achievements as $achievement)
                    <div class="col-xl-3 col-sm-6">
                        <div
                            class="card bg-image add-class theme-cards text-center shadow-none border reveal reveal-active">
                            <div class="card-body main-card-body">
                                <div class="text-primary addons fs-4 mb-3">
                                    <img src="{{ $achievement->icon ? $achievement->icon->getUrl('thumb') : '' }}"
                                        alt="">
                                </div>
                                <h5>{{ $achievement->title }}</h5>
                                <p class="card-main-content mb-0">{{ $achievement->achievement }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- End:: Section-4 -->

    <!-- Start:: Section-8 -->
    <section class="section bg-white" id="team">
        <div class="container TeamContainer">
            <div class="heading-section">
                <div class="heading-subtitle"><span
                        class="text-primary fw-semibold">{{ trans('frontend.home.partners') }}</span></div>
                <hr class="center-diamond">
                <div class="heading-title">{{ getSetting('home_partners_title') }}</div>
                <div class="heading-description">{{ getSetting('home_partners_sub_title') }}</div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="swiper swiper-overflow">
                                <div class="swiper-wrapper">
                                    @foreach ($partners as $partner)
                                        <div class="swiper-slide">
                                            <img class="img-fluid"
                                                src="{{ $partner->image ? $partner->image->getUrl() : '' }}"
                                                alt="img">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End:: Section-8 -->

    <!-- Start:: Section-9 -->
    <section class="section bg-primary" id="testimonials">
        <div class="container reviews-container">
            <div class="row gy-3">
                <div class="col-xl-4">
                    <div class="heading-section text-start mb-0 mt-4">
                        <div class="heading-subtitle style1"><span
                                class="text-fixed-white fs-16 fw-semibold">{{ trans('frontend.home.what_people_say') }}</span>
                        </div>
                        <div class="heading-title text-fixed-white">
                            {{ getSetting('home_reviews_title') }}
                        </div>
                        <div class="heading-description text-fixed-white op-8">
                            {{ getSetting('home_reviews_sub_title') }}
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div
                        class="swiper pagination-dynamic testimonialSwiperService swiper-initialized swiper-horizontal swiper-pointer-events swiper-backface-hidden">
                        <div class="swiper-wrapper">
                            @foreach ($reviews as $index => $review)
                                <div class="swiper-slide" data-swiper-slide-index="{{ $index }}" role="group" aria-label="{{ $index + 1 }} / {{ count($reviews) }}"
                                    style="width: 418px; margin-right: 20px;">
                                    <div class="card custom-card text-fixed-white border-0 reveal reveal-active">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="avatar rounded-circle me-2">
                                                    @if($review->photo)
                                                        <img src="{{ $review->photo->getUrl('thumb') }}" alt="{{ $review->name }}"
                                                            class="img-fluid rounded-circle">
                                                    @else
                                                        <img src="{{ asset('assets/images/faces/1.jpg') }}" alt="{{ $review->name }}"
                                                            class="img-fluid rounded-circle">
                                                    @endif
                                                </span>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold fs-14 text-fixed-white">{{ $review->name }}</h6>
                                                    <p class="mb-0 fs-11 fw-normal op-8">{{ $review->email }}</p>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                @php
                                                    $reviewText = Str::limit($review->review, 100);
                                                    $fullReview = $review->review;
                                                @endphp
                                                <span class="op-8">- {{ $reviewText }}
                                                    @if(strlen($review->review) > 100)
                                                        <a href="javascript:void(0);"
                                                            class="fw-semibold fs-11 text-fixed-white" data-bs-toggle="tooltip"
                                                            data-bs-custom-class="tooltip-primary" data-bs-placement="top"
                                                            data-bs-title="{{ $fullReview }}">Read More</a>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="op-8">Rating : </span>
                                                    <span class="text-warning d-block ms-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rate)
                                                                <i class="ri-star-fill"></i>
                                                            @else
                                                                <i class="ri-star-line"></i>
                                                            @endif
                                                        @endfor
                                                    </span>
                                                </div>
                                                <div class="float-end fs-12 fw-semibold op-8 text-end">
                                                    <span>{{ $review->created_at->diffForHumans() }}</span>
                                                    <span class="d-block fs-12 text-success"><i>{{ $review->name }}</i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal swiper-pagination-bullets-dynamic"
                            style="width: 80px;"><span class="swiper-pagination-bullet" tabindex="0" role="button"
                                aria-label="Go to slide 1" style="left: -32px;"></span><span
                                class="swiper-pagination-bullet" tabindex="0" role="button"
                                aria-label="Go to slide 2" style="left: -32px;"></span><span
                                class="swiper-pagination-bullet swiper-pagination-bullet-active-prev-prev" tabindex="0"
                                role="button" aria-label="Go to slide 3" style="left: -32px;"></span><span
                                class="swiper-pagination-bullet swiper-pagination-bullet-active-prev" tabindex="0"
                                role="button" aria-label="Go to slide 4" style="left: -32px;"></span><span
                                class="swiper-pagination-bullet swiper-pagination-bullet-active swiper-pagination-bullet-active-main"
                                tabindex="0" role="button" aria-label="Go to slide 5" aria-current="true"
                                style="left: -32px;"></span><span
                                class="swiper-pagination-bullet swiper-pagination-bullet-active-next" tabindex="0"
                                role="button" aria-label="Go to slide 6" style="left: -32px;"></span></div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End:: Section-9 -->

    <!-- Start:: Section-10 -->
    <section class="section overflow-hidden bg-white" id="contact">
        <div class="container">
            <div class="heading-section mb-4">
                <div class="heading-section">
                    <div class="heading-subtitle"><span class="text-primary fw-semibold">{{ trans('frontend.home.contact') }}</span></div>
                    <hr class="center-diamond">
                    <div class="heading-title">{{ getSetting('home_contact_title') }}</div>
                    <div class="heading-description fs-15 mb-5">{{ getSetting('home_contact_sub_title') }}</div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-xxl-11">
                        <div class="row">
                            <div class="col-xxl-3">
                                <div class="card border shadow-none reveal reveal-active">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center text-start">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded bg-primary">
                                                    <i class="fe fe-map-pin fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="ms-2 text-default  fs-14">
                                                <h6 class="mb-0">{{ trans('frontend.home.address') }}</h6>
                                                <p class="mb-0">{{ getSetting('site_address') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3">
                                <div class="card border shadow-none reveal reveal-active">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center text-start">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded bg-success">
                                                    <i class="fe fe-phone fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="ms-2 text-default  fs-14">
                                                <h6 class="mb-0">{{ trans('frontend.home.phone') }}</h6>
                                                <p class="mb-0">{{ getSetting('site_phone') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3">
                                <div class="card border shadow-none reveal reveal-active">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center text-start">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded bg-info">
                                                    <i class="fe fe-mail fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="ms-2 text-default  fs-14">
                                                <h6 class="mb-0">{{ trans('frontend.home.email') }}</h6>
                                                <p class="mb-0">{{ getSetting('site_email') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3">
                                <div class="card border shadow-none reveal reveal-active">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center text-start">
                                            <div>
                                                <span class="avatar avatar-md avatar-rounded bg-secondary">
                                                    <i class="fe fe-clock fs-12"></i>
                                                </span>
                                            </div>
                                            <div class="ms-2 text-default  fs-14">
                                                <h6 class="mb-0">{{ trans('frontend.home.working_hours') }}</h6>
                                                <p class="mb-0">{{ getSetting('site_working_hours') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End:: Section-10 -->
@endsection

@section('scripts')
    <script>
        var swiper = new Swiper(".swiper-overflow", {
            effect: "coverflow",
            grabCursor: false,
            centeredSlides: true,
            slidesPerView: "4",
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: !0,
            },
            loop: true,
            autoplay: {
                delay: 1500,
                disableOnInteraction: false
            }
        });
    </script>
@endsection
