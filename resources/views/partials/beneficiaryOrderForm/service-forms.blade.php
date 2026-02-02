<!-- Financial Service Form -->
@if (request()->get('service_type') == 'financial')
    <div id="financial-form">
        @include('partials.beneficiaryOrderForm.financial-form')
    </div>
@endif

<!-- Housing Service Form -->
@if (request()->get('service_type') == 'housing')
    <div id="housing-form">
        @include('partials.beneficiaryOrderForm.housing-form')
    </div>
@endif

<!-- Social Service Form -->
@if (request()->get('service_type') == 'social')
    <div id="social-form">
        @include('partials.beneficiaryOrderForm.social-form')
    </div>
@endif

<!-- Consultant Service Form -->
@if (request()->get('service_type') == 'consultant')
    <div id="consultant-form">
        @include('partials.beneficiaryOrderForm.consultant-form')
    </div>
@endif

<!-- Courses Service Form -->
@if (request()->get('service_type') == 'courses')
    <div id="courses-form">
        @include('partials.beneficiaryOrderForm.courses-form')
    </div>
@endif

<!-- Loan Service Form -->
@if (request()->get('service_type') == 'loan')
    <div id="loan-form">
        @include('partials.beneficiaryOrderForm.loan-form')
    </div>
@endif

<!-- Dynamic Service Forms -->
@if (str_starts_with(request()->get('service_type'), 'dynamic_'))
    @php
        $dynamicServiceId = str_replace('dynamic_', '', request()->get('service_type'));
        $dynamicService = $dynamicServices->firstWhere('id', $dynamicServiceId);
    @endphp
    @if($dynamicService)
        <div id="dynamic-form-{{ $dynamicServiceId }}">
            @include('partials.beneficiaryOrderForm.dynamic-form', ['dynamicService' => $dynamicService])
        </div>
    @endif
@endif