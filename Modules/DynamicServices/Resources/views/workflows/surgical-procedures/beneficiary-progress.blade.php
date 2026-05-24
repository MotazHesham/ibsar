@php
    $w = $dynamicServiceOrder->workflow_data ?? [];
    $currentStep = $workflowContext['currentStep'];
@endphp
<div class="card custom-card mt-3">
    <div class="card-header"><div class="card-title mb-0">مسار العمليات الجراحية</div></div>
    <div class="card-body">
        @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])
        <div class="mb-3"><span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span></div>
        @if (!empty($w['transfer']['clinic_name']))
            <div class="alert alert-primary">العيادة: {{ $w['transfer']['clinic_name'] }}</div>
        @endif
        @if (!empty($w['contribution']['contribution_amount']) && empty($w['financial']['approved_at'] ?? null))
            <div class="alert alert-warning">
                المساهمة: {{ $w['contribution']['contribution_amount'] }} — يرجى السداد ورفع الإيصال
                <form class="mt-2" method="POST" action="{{ route('beneficiary.beneficiary-orders.surgical-receipt', $beneficiaryOrder) }}">@csrf
                    @include('utilities.form.textarea', ['name' => 'receipt_notes', 'label' => 'ملاحظات الإيصال', 'isRequired' => false])
                    <button class="btn btn-sm btn-primary">تأكيد رفع الإيصال</button>
                </form>
            </div>
        @endif
        @if ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
            <div class="alert alert-success">تم إكمال مسار العملية الجراحية</div>
        @endif
    </div>
</div>
