@php $currentStep = $workflowContext['currentStep']; $programDetails = $workflowContext['programDetails'] ?? []; @endphp
<div class="card custom-card mt-3">
    <div class="card-header"><div class="card-title mb-0">مسار البرامج الاجتماعية</div></div>
    <div class="card-body">
        @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])
        <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">المرحلة</span>
            <span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span>
        </div>
        @if ($workflowContext['isRejected'])
            <div class="alert alert-danger">تم رفض الطلب</div>
        @elseif (!empty($programDetails['message']))
            <div class="alert alert-success"><strong>تفاصيل البرنامج:</strong><div class="mt-2">{{ $programDetails['message'] }}</div></div>
        @else
            @switch($currentStep)
                @case('initial_approval')<div class="alert alert-warning mb-0">طلبكم قيد اعتماد قسم المشاريع.</div>@break
                @case('send_program_details')<div class="alert alert-info mb-0">تم اعتماد طلبكم. سيتم إرسال تفاصيل البرنامج عند اكتمال العدد.</div>@break
            @endswitch
        @endif
        <p class="text-muted small mt-3 mb-0">المسجلون: {{ $workflowContext['registeredCount'] ?? 0 }} / {{ $workflowContext['targetCount'] ?: '—' }}</p>

        @include('dynamicservices::workflows.partials.history', [
            'dynamicServiceOrder' => $workflowContext['dynamicServiceOrder'],
            'workflowContext' => $workflowContext,
            'open' => true,
        ])
    </div>
</div>
