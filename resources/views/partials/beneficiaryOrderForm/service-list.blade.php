<h6 class="mb-3">{{ trans('cruds.beneficiaryOrder.fields.service_type') }}</h6>

<!-- Social Service -->
<a class="card custom-card service-type-card @if (request()->get('service_type') == 'social') selected @endif"
    href="{{ route(auth()->user()->user_type == 'staff' ? 'admin.beneficiary-orders.create' : 'beneficiary.beneficiary-orders.create', ['service_type' => 'social']) }}"
    style="text-decoration: none;">
    <div class="ribbon-2 ribbon-info ribbon-left">
        إجتماعية
    </div>
    <div class="card-body text-center">
        <img src="{{ asset('assets/images/services/social.png') }}" class="card-img mb-3" alt="social">
        <input type="radio" name="service_type" value="social" id="service_social" class="service-type-radio"
            style="display: none;" @if (request()->get('service_type') == 'social') checked @endif>
    </div>
</a>

<!-- Courses Service -->
<a class="card custom-card service-type-card @if (request()->get('service_type') == 'courses') selected @endif"
    href="{{ route(auth()->user()->user_type == 'staff' ? 'admin.beneficiary-orders.create' : 'beneficiary.beneficiary-orders.create', ['service_type' => 'courses']) }}">
    <div class="ribbon-2 ribbon-primary ribbon-left">
        <span class="ribbon-text">دورة تدريبية</span>
    </div>
    <div class="card-body text-center">
        <img src="{{ asset('assets/images/services/courses.png') }}" class="card-img mb-3" alt="courses">
        <input type="radio" name="service_type" value="courses" id="service_courses" class="service-type-radio"
            style="display: none;" @if (request()->get('service_type') == 'courses') checked @endif>
    </div>
</a>

<!-- Financial Service -->
<a class="card custom-card service-type-card @if (request()->get('service_type') == 'financial') selected @endif"
    href="{{ route(auth()->user()->user_type == 'staff' ? 'admin.beneficiary-orders.create' : 'beneficiary.beneficiary-orders.create', ['service_type' => 'financial']) }}">
    <div class="ribbon-2 ribbon-warning ribbon-left">
        <span class="ribbon-text">مالية</span>
    </div>
    <div class="card-body text-center">
        <img src="{{ asset('assets/images/services/financial.png') }}" class="card-img mb-3" alt="financial">
        <input type="radio" name="service_type" value="financial" id="service_financial" class="service-type-radio"
            style="display: none;" @if (request()->get('service_type') == 'financial') checked @endif>
    </div>
</a>

<!-- Consultant Service -->
<a class="card custom-card service-type-card @if (request()->get('service_type') == 'consultant') selected @endif"
    href="{{ route(auth()->user()->user_type == 'staff' ? 'admin.beneficiary-orders.create' : 'beneficiary.beneficiary-orders.create', ['service_type' => 'consultant']) }}">
    <div class="ribbon-2 ribbon-success ribbon-left">
        إستشارية
    </div>
    <div class="card-body text-center">
        <img src="{{ asset('assets/images/services/consultant.png') }}" class="card-img mb-3" alt="consultant">
        <input type="radio" name="service_type" value="consultant" id="service_consultant" class="service-type-radio"
            style="display: none;" @if (request()->get('service_type') == 'consultant') checked @endif>
    </div>
</a>

<!-- Loan Service -->
<a class="card custom-card service-type-card @if (request()->get('service_type') == 'loan') selected @endif"
    href="{{ route(auth()->user()->user_type == 'staff' ? 'admin.beneficiary-orders.create' : 'beneficiary.beneficiary-orders.create', ['service_type' => 'loan']) }}">
    <div class="ribbon-2 ribbon-danger ribbon-left">
        <span class="ribbon-text">قروض</span>
    </div>
    <div class="card-body text-center">
        <img src="{{ asset('assets/images/services/loan.png') }}" class="card-img mb-3" alt="loan">
        <input type="radio" name="service_type" value="loan" id="service_loan" class="service-type-radio"
            style="display: none;" @if (request()->get('service_type') == 'loan') checked @endif>
    </div>
</a>

<!-- Dynamic Services -->
@foreach ($dynamicServices ?? [] as $dynamicService)
    <a class="card custom-card service-type-card @if (request()->get('service_type') == 'dynamic_' . $dynamicService->id) selected @endif"
        href="{{ route(auth()->user()->user_type == 'staff' ? 'admin.beneficiary-orders.create' : 'beneficiary.beneficiary-orders.create', ['service_type' => 'dynamic_' . $dynamicService->id]) }}">
        <div class="ribbon-2 ribbon-secondary ribbon-left">
            <span class="ribbon-text">{{ $dynamicService->title }}</span>
        </div>
        <div class="card-body text-center">
            @if ($dynamicService->getFirstMedia('icon'))
                <img src="{{ $dynamicService->getFirstMedia('icon')->getUrl() }}" class="card-img mb-3"
                    alt="{{ $dynamicService->title }}">
            @else
                {{-- <img src="{{ asset('assets/images/services/dynamic.png') }}" class="card-img mb-3" alt="dynamic"> --}}
            @endif
            <input type="radio" name="service_type" value="dynamic_{{ $dynamicService->id }}"
                id="service_dynamic_{{ $dynamicService->id }}" class="service-type-radio" style="display: none;"
                @if (request()->get('service_type') == 'dynamic_' . $dynamicService->id) checked @endif>
        </div>
    </a>
@endforeach

<style>
    .service-type-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .service-type-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .service-type-card.selected {
        border-color: var(--primary-color);
        background-color: rgba(var(--primary-rgb), 0.05);
    }

    .service-type-card.selected .card-body {
        background-color: rgba(var(--primary-rgb), 0.1);
    }

    .service-type-card .card-img {
        max-height: 80px;
        width: auto;
        object-fit: contain;
        object-position: center;
    }

    .service-type-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 120px;
    }
</style>
