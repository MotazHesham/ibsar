@php
    $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
    $currentStep = $workflowContext['currentStep'];
    $w = $dynamicServiceOrder->workflow_data ?? [];
    $exam = $w['exam_appointment'] ?? [];
    $contribution = $w['contribution'] ?? [];
    $financial = $w['financial'] ?? [];
    $allocation = $w['allocation'] ?? [];
    $pickup = $w['pickup'] ?? [];
    $outcome = $w['doctor_evaluation']['outcome'] ?? null;
@endphp
<div class="card custom-card mt-3">
    <div class="card-header"><div class="card-title mb-0">مسار مركز كشف إبصار</div></div>
    <div class="card-body">
        @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])
        <div class="mb-3"><span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span></div>

        @if (!empty($exam['date']))
            <div class="alert alert-primary">موعد الكشف: {{ $exam['date'] }} — {{ $exam['time'] ?? '' }}</div>
        @endif

        @if ($workflowContext['isRejected'])
            <div class="alert alert-danger">تم رفض الطلب</div>
        @elseif ($outcome === 'external_clinic' || ($workflowContext['isCompleted'] && $outcome === 'no_further_action'))
            <div class="alert alert-success">تم حفظ تقرير الكشف.</div>
            @if (empty($w['satisfaction']['completed_at']))
                <form method="POST" action="{{ route('beneficiary.beneficiary-orders.detection-satisfaction', $beneficiaryOrder) }}">@csrf
                    @include('utilities.form.select', ['name' => 'satisfaction_rating', 'label' => 'تقييم الرضا', 'isRequired' => true, 'grid' => 'col-md-6', 'options' => [1=>'1',2=>'2',3=>'3',4=>'4',5=>'5'], 'value' => ''])
                    @include('utilities.form.textarea', ['name' => 'satisfaction_comment', 'label' => 'ملاحظات', 'isRequired' => false])
                    <button class="btn btn-primary btn-sm">إرسال الاستبيان</button>
                </form>
            @endif
        @elseif ($outcome === 'low_vision_clinic')
            @if (!empty($contribution['amount']) && empty($financial['approved_at']))
                @if (!empty($financial['receipt_submitted_at']))
                    <div class="alert alert-success mb-0">
                        <i class="ri-check-line me-1"></i>
                        تم إرسال إيصال السداد — جاري مراجعته من المالية.
                        @if (!empty($financial['receipt_notes']))
                            <div class="mt-2"><strong>ملاحظات الإيصال:</strong> {{ $financial['receipt_notes'] }}</div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning">
                        المساهمة: {{ $contribution['amount'] }} — يرجى السداد ورفع الإيصال
                        <form class="mt-2" method="POST" action="{{ route('beneficiary.beneficiary-orders.detection-receipt', $beneficiaryOrder) }}">@csrf
                            @include('utilities.form.textarea', ['name' => 'receipt_notes', 'label' => 'ملاحظات الإيصال', 'isRequired' => false, 'rawLabel' => true])
                            <button type="submit" class="btn btn-sm btn-primary">تأكيد رفع الإيصال</button>
                        </form>
                    </div>
                @endif
            @elseif (!empty($financial['approved_at']) && empty($pickup['confirmed_at']))
                <div class="card border mb-3">
                    <div class="card-body">
                        <h6>تأكيد موعد الاستلام</h6>
                        <form method="POST" action="{{ route('beneficiary.beneficiary-orders.detection-pickup', $beneficiaryOrder) }}">@csrf
                            @include('utilities.form.date', ['id' => 'pickup_date', 'name' => 'pickup_date', 'label' => 'تاريخ الاستلام', 'isRequired' => true, 'grid' => 'col-md-6', 'helperBlock' => ''])
                            @include('utilities.form.time', ['id' => 'pickup_time', 'name' => 'pickup_time', 'label' => 'وقت الاستلام', 'isRequired' => true, 'grid' => 'col-md-6', 'helperBlock' => ''])
                            <button class="btn btn-primary btn-sm">حفظ الموعد</button>
                        </form>
                    </div>
                </div>
            @elseif (!empty($allocation['item_name']))
                <div class="alert alert-info">تم تخصيص: {{ $allocation['item_name'] }}. سيصلكم رمز OTP عند تأكيد الموعد.</div>
            @elseif ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
                <div class="alert alert-success">تم استلام الطلب بنجاح.</div>
            @endif
        @else
            @switch($currentStep)
                @case('initial_approval')<div class="alert alert-warning mb-0">طلبكم قيد اعتماد الاستقبال.</div>@break
                @case('attendance')<div class="alert alert-info mb-0">يرجى الحضور في موعد الكشف المحدد.</div>@break
                @case('second_approval')<div class="alert alert-info mb-0">جاري تقييم الدكتور.</div>@break
                @case('financial_approval')<div class="alert alert-info mb-0">جاري اعتماد المالية.</div>@break
            @endswitch
        @endif

        @include('dynamicservices::workflows.partials.history', [
            'dynamicServiceOrder' => $workflowContext['dynamicServiceOrder'],
            'workflowContext' => $workflowContext,
            'open' => true,
        ])
    </div>
</div>
