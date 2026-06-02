@if (!empty($workflowContext))
    @php
        $currentStep = $workflowContext['currentStep'];
        $targetCount = $workflowContext['targetCount'] ?? 0;
        $registeredCount = $workflowContext['registeredCount'] ?? 0;
        $programDetails = $workflowContext['programDetails'] ?? [];
    @endphp

    <div class="alert alert-info py-2 mb-3">
        <small><strong>برنامج اجتماعي</strong> — العدد المستهدف: {{ $targetCount ?: 'غير محدد' }} | المعتمدون: {{ $registeredCount }}</small>
    </div>

    @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

    @if ($workflowContext['isRejected'])
        <div class="alert alert-danger">تم رفض الطلب@if ($beneficiaryOrder->refused_reason): {{ $beneficiaryOrder->refused_reason }}@endif</div>
    @elseif ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
        <div class="alert alert-success">تم إرسال تفاصيل البرنامج للمستفيدين</div>
        @if (!empty($programDetails['message']))
            <div class="card mt-2"><div class="card-body small">{{ $programDetails['message'] }}</div></div>
        @endif
    @else
        @switch($currentStep)
            @case('initial_approval')
                <div class="alert alert-warning mb-0">طلبكم قيد المراجعة. يتم الاعتماد من صفحة البرنامج.</div>
                @break
            @case('send_program_details')
                <div class="alert alert-info mb-0">تم اعتماد طلبكم. سيتم إرسال تفاصيل البرنامج عند اكتمال العدد المستهدف.</div>
                @break
        @endswitch
    @endif

    @include('dynamicservices::workflows.partials.history', [
        'dynamicServiceOrder' => $workflowContext['dynamicServiceOrder'],
        'workflowContext' => $workflowContext,
    ])
@endif
