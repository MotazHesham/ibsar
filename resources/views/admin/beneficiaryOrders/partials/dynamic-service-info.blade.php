@php
    $dynamicServiceOrder = $beneficiaryOrder->dynamicServiceOrder;
    $dynamicService = $dynamicServiceOrder ? $dynamicServiceOrder->dynamicService : null;
    
    // Helper function to get display value for select/radio/checkbox fields
    function getFieldDisplayValue($field) {
        $value = $field['value'];
        
        // For select, radio, checkbox with options, convert index to text
        if (in_array($field['type'], ['select', 'radio', 'checkbox']) && !empty($field['options'])) {
            if (is_numeric($value)) {
                $index = (int)$value;
                if (isset($field['options'][$index])) {
                    return $field['options'][$index];
                }
            }
        }
        
        return $value;
    }
@endphp

@if($dynamicServiceOrder && $dynamicService)
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-3">
                <span class="avatar avatar-md me-2 bg-primary-gradient">
                    @if($dynamicService->getFirstMedia('icon'))
                        <img src="{{ $dynamicService->getFirstMedia('icon')->getUrl() }}" alt="{{ $dynamicService->title }}" class="card-img">
                    @else
                        <i class="ri-settings-3-line fs-18"></i>
                    @endif
                </span>
                <div>
                    <h6 class="fw-semibold mb-1">{{ $dynamicService->title }}</h6>
                    @if($dynamicService->description)
                        <small class="text-muted">{{ $dynamicService->description }}</small>
                    @endif
                </div>
            </div>

            @if($dynamicServiceOrder->field_data && count($dynamicServiceOrder->field_data) > 0)
                <div class="row g-3">
                    @foreach($dynamicServiceOrder->field_data as $field)
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <label class="form-label fw-semibold text-dark mb-0 small">
                                        {{ $field['label'] ?? 'Field ' . $field['id'] }}
                                    </label>
                                    @if(isset($field['required']) && $field['required'])
                                        <span class="badge bg-danger-transparent badge-sm">Required</span>
                                    @endif
                                </div>
                                
                                <div class="field-value">
                                    @switch($field['type'] ?? 'text')
                                        @case('email')
                                            <a href="mailto:{{ $field['value'] }}" class="text-primary text-decoration-none">
                                                <i class="ri-mail-line me-1"></i>{{ $field['value'] }}
                                            </a>
                                            @break
                                        @case('url')
                                            <a href="{{ $field['value'] }}" target="_blank" class="text-primary text-decoration-none">
                                                <i class="ri-external-link-line me-1"></i>{{ $field['value'] }}
                                            </a>
                                            @break
                                        @case('date')
                                            <span class="badge bg-info-transparent">
                                                <i class="ri-calendar-line me-1"></i>{{ $field['value'] }}
                                            </span>
                                            @break
                                        @case('number')
                                            <span class="badge bg-secondary-transparent">
                                                <i class="ri-number-line me-1"></i>{{ $field['value'] }}
                                            </span>
                                            @break
                                        @case('textarea')
                                            <div class="text-wrap">
                                                {!! nl2br(e($field['value'])) !!}
                                            </div>
                                            @break
                                        @case('select')
                                        @case('radio')
                                        @case('checkbox')
                                            <span class="badge bg-primary-transparent">
                                                <i class="ri-checkbox-line me-1"></i>{{ getFieldDisplayValue($field) }}
                                            </span>
                                            @break
                                        @default
                                            <span class="text-dark">{{ $field['value'] ?? 'Not provided' }}</span>
                                    @endswitch
                                </div> 
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="ri-information-line me-2"></i>
                    No dynamic fields found for this service.
                </div>
            @endif
        </div>
    </div>
@else
    <div class="alert alert-warning">
        <i class="ri-error-warning-line me-2"></i>
        Dynamic service information not found.
    </div>
@endif
