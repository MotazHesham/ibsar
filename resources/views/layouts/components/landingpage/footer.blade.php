<!-- Start:: Section-11 -->
<footer class="footer mt-auto text-fixed-white position-relative">
    <section class="section py-5 newsLetter-sec">
        <div class="container">
            <div class="row my-auto justify-content-between">
                <div class="col-lg-6">
                    <div class="heading-section text-start mb-3 mb-lg-0">
                        <h3 class="text-fixed-white mb-0">{{ trans('frontend.footer.newsletter_title') }}</h3>
                        <div class="heading-description fs-15 text-fixed-white op-8">
                            {{ trans('frontend.footer.newsletter_description') }}</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form mb-0">
                        <form action="{{ route('frontend.subscription.store') }}" method="post">
                            <div class="form-group custom-form-group mx-auto">
                                @csrf
                                <input type="email" name="email"
                                    class="form-control form-control-lg rounded-pill shadow-none"
                                    placeholder="{{ trans('frontend.footer.newsletter_placeholder') }}">
                                <button
                                    class="custom-form-btn btn btn-primary bg-primary rounded-pill border-0 shadow-none"
                                    type="submit">{{ trans('frontend.footer.newsletter_button') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <div class="py-3 border-top border-white-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <a href="#" class="d-inline-block mb-2"><img src="{{ getSetting('footer_logo') }}"
                            alt="img"></a>
                    <p class="mb-4 op-8 fs-15">
                        {{ getSetting('footer_description') }}
                    </p>
                    <ul class="list-unstyled mb-0">
                        <li class="list-item mb-2"><a href="tel:{{ getSetting('site_phone') }}" class="footer-link"><i
                                    class="fe fe-phone me-2 d-inline-block fs-16"></i>{{ getSetting('site_phone') }}</a>
                        </li>
                        <li class="list-item mb-2"><a href="mailto:{{ getSetting('site_email') }}"
                                class="footer-link"><i
                                    class="fe fe-mail me-2 d-inline-block fs-16"></i>{{ getSetting('site_email') }}</a>
                        </li>
                        <li class="list-item"><a href="javascript:void(0);" class="footer-link"><i
                                    class="fe fe-map-pin me-2 d-inline-block fs-16"></i>{{ getSetting('site_address') }}</a>
                        </li>
                    </ul>
                    <div class="footer-btn-list d-flex align-items-center mt-3">
                        <a aria-label="anchor" href="{{ getSetting('facebook') }}"
                            class="footer-btn btn btn-icon rounded-circle me-2"><i class="fe fe-facebook"></i></a>
                        <a aria-label="anchor" href="{{ getSetting('linkedin') }}"
                            class="footer-btn btn btn-icon rounded-circle me-2"><i class="fe fe-linkedin"></i></a>
                        <a aria-label="anchor" href="{{ getSetting('instagram') }}"
                            class="footer-btn btn btn-icon rounded-circle me-2"><i class="fe fe-instagram"></i></a>
                        <a aria-label="anchor" href="{{ getSetting('twitter') }}"
                            class="footer-btn btn btn-icon rounded-circle me-2"><i class="ri ri-twitter-x-line"></i></a>
                        <a aria-label="anchor" href="{{ getSetting('youtube') }}"
                            class="footer-btn btn btn-icon rounded-circle me-2"><i class="ri-youtube-fill"></i></a>
                        <a aria-label="anchor" href="{{ getSetting('tiktok') }}"
                            class="footer-btn btn btn-icon rounded-circle me-2"><i class="ri-tiktok-fill"></i></a>
                        <a aria-label="anchor" href="{{ getSetting('whatsapp') }}"
                            class="footer-btn btn btn-icon rounded-circle me-2"><i class="ri-whatsapp-fill"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5 class="text-fixed-white">{{ trans('frontend.footer.link1') }}</h5>
                    <ul class="list-unstyled footer-icon mb-0">
                        @foreach (\App\Models\FrontLink::where('position', 1)->get() as $link)
                            <li class="list-item mb-2"><a href="{{ $link->link }}"
                                    class="footer-link">{{ $link->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5 class="text-fixed-white">{{ trans('frontend.footer.link2') }}</h5>
                    <ul class="list-unstyled footer-icon mb-0">
                        @foreach (\App\Models\FrontLink::where('position', 2)->get() as $link)
                            <li class="list-item mb-2"><a href="{{ $link->link }}"
                                    class="footer-link">{{ $link->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-3 col-sm-6 mb-4 mb-lg-0">
                    <h5 class="text-fixed-white">{{ trans('frontend.footer.link3') }}</h5>
                    <ul class="list-unstyled footer-icon mb-0">
                        @foreach (\App\Models\FrontLink::where('position', 3)->get() as $link)
                            <li class="list-item mb-2"><a href="{{ $link->link }}"
                                    class="footer-link">{{ $link->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div> 
    <div class="py-3 border-top border-white-1">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 text-center text-xl-start">
                    <div class="fs-14">
                        Copyright ©
                        <span id="year">2024</span>
                        <a href="{{ url('index') }}" class="text-primary">{{ getSetting('site_name') }}</a>
                        Developed 
                        <span class="bi bi-heart-fill text-danger"></span>
                        by
                        <a href="#" class="text-primary" target="_blank">Integrated Vision</a>
                        All Rights Reserved
                    </div>
                </div> 
            </div>
        </div>
    </div>
</footer>
<!-- End:: Section-11 -->
