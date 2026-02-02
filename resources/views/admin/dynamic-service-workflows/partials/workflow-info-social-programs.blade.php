{{-- Social Programs Workflow Info --}}
@php
    $categoryData = $workflow->socialPrograms;
@endphp

@if($workflow->refused_reason)
    <div class="alert alert-danger">
        <strong>سبب الرفض:</strong><br>{{ $workflow->refused_reason }}
    </div>
@endif

@if($workflow->specialist)
    <p><strong>الأخصائي:</strong> {{ $workflow->specialist->name }}</p>
@endif

@if($categoryData)
    @if($categoryData->program_accepted !== null)
        <p><strong>قرار القبول:</strong>
            <span class="badge bg-{{ $categoryData->program_accepted ? 'success' : 'danger' }}">
                {{ $categoryData->program_accepted ? 'مقبول' : 'مرفوض' }}
            </span>
        </p>
    @endif

    @if($categoryData->program_completed !== null)
        <p><strong>اكتمال البرنامج:</strong>
            <span class="badge bg-{{ $categoryData->program_completed ? 'success' : 'warning' }}">
                {{ $categoryData->program_completed ? 'مكتمل' : 'غير مكتمل' }}
            </span>
        </p>
    @endif

    @if($categoryData->waiting_list_position)
        <p><strong>الموقع في قائمة الانتظار:</strong> {{ $categoryData->waiting_list_position }}</p>
    @endif

    @if($categoryData->document_prepared)
        <p><strong>تم إعداد الوثائق:</strong>
            <span class="badge bg-success">نعم</span>
        </p>
        @if($categoryData->document_data)
            <div class="mt-2">
                <strong>بيانات الوثائق:</strong>
                @if(is_array($categoryData->document_data))
                    <pre class="bg-light p-2 rounded">{{ json_encode($categoryData->document_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                @else
                    <p>{{ $categoryData->document_data }}</p>
                @endif
            </div>
        @endif
    @endif

    @if($categoryData->executive_decision)
        <p><strong>قرار الإدارة التنفيذية:</strong>
            <span class="badge bg-{{ $categoryData->executive_decision === 'accepted' ? 'success' : 'danger' }}">
                {{ $categoryData->executive_decision === 'accepted' ? 'مقبول' : 'مرفوض' }}
            </span>
        </p>
        @if($categoryData->executive_decision_notes)
            <div class="mt-2">
                <strong>ملاحظات القرار:</strong>
                <p>{{ $categoryData->executive_decision_notes }}</p>
            </div>
        @endif
    @endif

    @if($categoryData->program_proceeded)
        <p><strong>تم المضي قدماً في البرنامج:</strong>
            <span class="badge bg-success">نعم</span>
        </p>
        @if($categoryData->program_proceeded_date)
            <p><strong>تاريخ المضي قدماً:</strong> {{ $categoryData->program_proceeded_date->format('Y-m-d') }}</p>
        @endif
    @endif

    @if($categoryData->review_notes)
        <div class="mt-3">
            <strong>ملاحظات المراجعة:</strong>
            <p>{{ $categoryData->review_notes }}</p>
        </div>
    @endif
@endif

@if($workflow->notes)
    <div class="mt-3">
        <strong>ملاحظات:</strong><br>{{ $workflow->notes }}
    </div>
@endif

