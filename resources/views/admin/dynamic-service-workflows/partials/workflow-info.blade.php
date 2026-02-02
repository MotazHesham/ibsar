<div class="card">
    <div class="card-header">
        <h6 class="card-title">معلومات إضافية</h6>
    </div>
    <div class="card-body">
        @php
            $category = $workflow->category;
        @endphp

        {{-- Include category-specific info partial --}}
        @if($category === \App\Models\DynamicService::CATEGORY_TRAINING)
            @include('admin.dynamic-service-workflows.partials.workflow-info-training', ['workflow' => $workflow])
        @elseif($category === \App\Models\DynamicService::CATEGORY_ASSISTANCE)
            @include('admin.dynamic-service-workflows.partials.workflow-info-assistance', ['workflow' => $workflow])
        @elseif($category === \App\Models\DynamicService::CATEGORY_SOCIAL_PROGRAMS)
            @include('admin.dynamic-service-workflows.partials.workflow-info-social-programs', ['workflow' => $workflow])
        @else
            {{-- Fallback for unknown categories --}}
            @if($workflow->refused_reason)
                <div class="alert alert-danger">
                    <strong>سبب الرفض:</strong><br>{{ $workflow->refused_reason }}
                </div>
            @endif

            @if($workflow->specialist)
                <p><strong>الأخصائي:</strong> {{ $workflow->specialist->name }}</p>
            @endif

            @if($workflow->notes)
                <p><strong>ملاحظات:</strong><br>{{ $workflow->notes }}</p>
            @endif
        @endif
    </div>
</div>
