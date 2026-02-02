<!-- Start:: Section-1 -->
<div class="landing-banner" id="home">
    <section class="banner-section section">
        <div class="container main-banner-container pb-lg-0">
            <div class="row align-items-center">
                @foreach ($sliders as $slider)
                    <div class="col-lg-7">
                        <div class="mb-5"> 
                            <h1 class="mb-3 content-1 text-fixed-white"> {{ $slider->title }} </h1>
                            <p class="content-2 text-fixed-white"> {{ $slider->sub_title }} </p>
                        </div>
                        <div class="btn-list">
                            <a href="{{ $slider->button_link }}" class="btn btn-lg btn-secondary mb-2 mb-sm-0"><i
                                    class="fe fe-arrow-right me-2 d-inline-block"></i>{{ $slider->button_name }}</a>
                        </div>
                    </div>
                    <div class="col-lg-5 mt-4 mt-lg-0 text-end">
                        <div class="add-content bg-transparent shadow-none p-0">
                            <div class="add-content1">
                                <img src="{{ $slider->image ? $slider->image->getUrl() : '' }}" alt="img" class="img-fluid">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
<!-- End:: Section-1 -->
