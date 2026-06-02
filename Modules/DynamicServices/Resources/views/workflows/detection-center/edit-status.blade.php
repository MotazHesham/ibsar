@if (!empty($workflowContext))
    @php
        $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
        $availableActions = $workflowContext['availableActions'] ?? [];
        $currentStep = $workflowContext['currentStep'];
        $examAppointment = $workflowContext['examAppointment'] ?? [];
        $doctorEvaluation = $workflowContext['doctorEvaluation'] ?? [];
        $contribution = $workflowContext['contribution'] ?? [];
        $allocation = $workflowContext['allocation'] ?? [];
        $examTypes = $workflowContext['examTypes'] ?? [];
        $doctorOutcomes = $workflowContext['doctorOutcomes'] ?? [];
        $visualAids = $workflowContext['visualAids'] ?? [];
        $availableStockItems = $workflowContext['availableStockItems'] ?? collect();
        $financial = $workflowContext['financial'] ?? [];
    @endphp

    <div class="alert alert-info py-2 mb-3">
        <small><strong>مركز كشف إبصار</strong></small>
    </div>

    @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

    @if ($workflowContext['isRejected'])
        <div class="alert alert-danger">تم رفض الطلب</div>
    @elseif (count($availableActions) > 0)
        <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" method="POST" id="detection-workflow-form">
            @csrf @method('PUT')
            <input type="hidden" name="dynamic_workflow" value="1">

            @if ($currentStep === 'initial_approval')
                <div class="card mb-3">
                    <div class="card-header py-2"><h6 class="mb-0">جدولة موعد الكشف</h6></div>
                    <div class="card-body row">
                        @include('utilities.form.date', ['id' => 'exam_date', 'name' => 'exam_date', 'label' => 'تاريخ الكشف', 'isRequired' => true, 'grid' => 'col-md-6', 'helperBlock' => '', 'value' => $examAppointment['date'] ?? ''])
                        @include('utilities.form.time', ['id' => 'exam_time', 'name' => 'exam_time', 'label' => 'وقت الكشف', 'isRequired' => true, 'grid' => 'col-md-6', 'helperBlock' => '', 'value' => $examAppointment['time'] ?? ''])
                        <div class="col-12 mb-3">
                            <label class="form-label">أنواع الكشف <span class="text-danger">*</span></label>
                            @foreach ($examTypes as $key => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="exam_types[]" value="{{ $key }}" id="exam_type_{{ $key }}"
                                        @checked(in_array($key, $examAppointment['types'] ?? [], true))>
                                    <label class="form-check-label" for="exam_type_{{ $key }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($currentStep === 'second_approval')
                <div class="card mb-3">
                    <div class="card-header py-2"><h6 class="mb-0">تقييم الدكتور</h6></div>
                    <div class="card-body">
                        @include('utilities.form.select', ['name' => 'outcome', 'label' => 'الخيار', 'isRequired' => true, 'grid' => 'col-md-12', 'options' => $doctorOutcomes, 'value' => $doctorEvaluation['outcome'] ?? ''])
                        @include('utilities.form.select', ['name' => 'visual_aids[]', 'label' => 'المعينات / العدسات', 'isRequired' => false, 'grid' => 'col-md-12', 'options' => $visualAids, 'value' => $doctorEvaluation['visual_aids'] ?? [], 'attributes' => 'multiple'])
                        @include('utilities.form.text', ['name' => 'contribution_amount', 'label' => 'قيمة المساهمة (عيادة ضعف الإبصار)', 'isRequired' => false, 'grid' => 'col-md-6', 'value' => $doctorEvaluation['contribution_amount'] ?? '', 'attributes' => 'type="number" step="0.01"'])
                        @include('utilities.form.textarea', ['name' => 'evaluation_notes', 'label' => 'ملاحظات', 'isRequired' => false, 'value' => $doctorEvaluation['evaluation_notes'] ?? ''])
                    </div>
                </div>
            @endif

            @if ($currentStep === 'receive_order' && empty($allocation['item_name'] ?? null))
                @include('utilities.form.select', ['name' => 'donation_item_id', 'label' => 'تخصيص من المخزون', 'isRequired' => true, 'grid' => 'col-md-12', 'options' => $availableStockItems, 'value' => ''])
            @endif

            @if ($currentStep === 'receive_order' && !empty($workflowContext['pickup']['confirmed_at'] ?? null))
                @include('utilities.form.text', ['name' => 'otp_code', 'label' => 'رمز OTP', 'isRequired' => true, 'grid' => 'col-md-6', 'attributes' => 'maxlength="6"'])
            @endif

            @if ($currentStep === 'financial_approval')
                <div class="card mb-3">
                    <div class="card-header py-2"><h6 class="mb-0">بيانات إيصال السداد</h6></div>
                    <div class="card-body">
                        @if (!empty($contribution['amount']))
                            <p class="mb-2"><strong>قيمة المساهمة:</strong> {{ $contribution['amount'] }}</p>
                        @endif
                        @if (!empty($financial['receipt_submitted_at']))
                            <p class="mb-2 text-success"><strong>تاريخ رفع الإيصال:</strong> {{ $financial['receipt_submitted_at'] }}</p>
                            @if (!empty($financial['receipt_notes']))
                                <p class="mb-0"><strong>ملاحظات الإيصال:</strong> {{ $financial['receipt_notes'] }}</p>
                            @else
                                <p class="mb-0 text-muted">لا توجد ملاحظات.</p>
                            @endif
                        @else
                            <div class="alert alert-warning mb-0">لم يرفع المستفيد الإيصال بعد.</div>
                        @endif
                    </div>
                </div>
            @endif

            <div id="workflow-reject-reason-wrapper" class="mb-3" style="display:none;">
                @include('utilities.form.textarea', ['name' => 'reason', 'label' => 'السبب', 'isRequired' => false])
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach ($availableActions as $action)
                    <button type="submit" name="workflow_action" value="{{ $action['key'] }}"
                        class="btn btn-{{ $action['type'] ?? 'primary' }}"
                        @if (!empty($action['requires_reason'])) data-requires-reason="1" @endif>
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </form>
    @endif

    @include('dynamicservices::workflows.partials.history', [
        'dynamicServiceOrder' => $dynamicServiceOrder,
        'workflowContext' => $workflowContext,
    ])
@endif

@section('scripts') @parent
<script>
document.querySelectorAll('#detection-workflow-form [data-requires-reason]').forEach(b => b.addEventListener('click', () => {
    document.getElementById('workflow-reject-reason-wrapper').style.display = 'block';
}));
</script>
@endsection
