@php
    use Modules\DynamicServices\Workflows\AssistanceWorkflowHandler;

    $service = $workflowContext['service'];
    $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
    $currentStep = $workflowContext['currentStep'];
    $isFinancial = $workflowContext['isFinancial'] ?? false;
    $workflowData = $dynamicServiceOrder->workflow_data ?? [];
    $allocation = $workflowData['allocation'] ?? [];
    $pickup = $workflowData['pickup'] ?? [];
    $financial = $workflowData['financial'] ?? [];
    $incompleteDocs = $workflowData['incomplete_docs'] ?? [];
    $satisfaction = $workflowData['satisfaction'] ?? [];
    $delivered = !empty($workflowData['delivered']) || !empty($workflowData['disbursed']);
    $assistanceTypeLabel = $isFinancial ? 'مساعدة مالية' : 'استلام عيني';
@endphp

<div class="card custom-card mt-3">
    <div class="card-header">
        <div class="card-title mb-0">مسار المساعدات — {{ $assistanceTypeLabel }}</div>
    </div>
    <div class="card-body">
        @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="text-muted">المرحلة الحالية</span>
            <span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span>
        </div>

        @if ($workflowContext['isRejected'])
            <div class="alert alert-danger">تم رفض الطلب.
                @if ($beneficiaryOrder->refused_reason)
                    <div class="mt-1"><strong>السبب:</strong> {{ $beneficiaryOrder->refused_reason }}</div>
                @endif
            </div>
        @elseif (($workflowData['status'] ?? '') === 'incomplete')
            <div class="alert alert-warning">
                <strong>طلب غير مكتمل</strong>
                @if (!empty($incompleteDocs['message']))
                    <p class="mb-2 mt-2">{{ $incompleteDocs['message'] }}</p>
                @endif
            </div>
            <div class="card border border-warning">
                <div class="card-body">
                    <form action="{{ route('beneficiary.beneficiary-orders.assistance-complete-docs', $beneficiaryOrder) }}" method="POST">
                        @csrf
                        @include('utilities.form.textarea', [
                            'name' => 'completion_notes',
                            'label' => 'توضيح ما تم استكماله',
                            'isRequired' => true,
                            'value' => old('completion_notes'),
                        ])
                        <button type="submit" class="btn btn-primary">إرسال التحديث للباحث</button>
                    </form>
                </div>
            </div>
        @elseif ($workflowContext['isCompleted'] && $beneficiaryOrder->done)
            <div class="alert alert-success">تم إنهاء طلب المساعدة بنجاح.</div>
        @else
            @switch($currentStep)
                @case(AssistanceWorkflowHandler::STEP_FIRST_APPROVAL)
                    <div class="alert alert-warning mb-0">طلبكم قيد مراجعة الباحث الاجتماعي.</div>
                    @break
                @case(AssistanceWorkflowHandler::STEP_SECOND_APPROVAL)
                    <div class="alert alert-info mb-0">تم اعتماد الباحث. جاري مراجعة قسم المشاريع.</div>
                    @break
                @case(AssistanceWorkflowHandler::STEP_THIRD_APPROVAL)
                    <div class="alert alert-info mb-0">جاري إجراءات صرف المساعدة المالية من قسم المالية.</div>
                    @break
                @case(AssistanceWorkflowHandler::STEP_RECEIVE_ORDER)
                    @if (!empty($allocation))
                        <div class="alert alert-primary mb-3">
                            <strong>الصنف:</strong> {{ $allocation['item_name'] ?? '' }} —
                            <strong>الكمية:</strong> {{ $allocation['quantity'] ?? '' }}
                        </div>
                    @endif
                    @if (empty($pickup['confirmed_at']))
                        <div class="card border mb-3">
                            <div class="card-body">
                                <h6 class="mb-3">تأكيد موعد الاستلام</h6>
                                <p class="small text-muted">يُشترط الحضور خلال 14 يوماً من اعتماد المشاريع وإلا تُعاد الكمية لمستفيد آخر.</p>
                                <form action="{{ route('beneficiary.beneficiary-orders.assistance-pickup', $beneficiaryOrder) }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        @include('utilities.form.date', [
                                            'id' => 'pickup_date',
                                            'name' => 'pickup_date',
                                            'label' => 'تاريخ الاستلام',
                                            'isRequired' => true,
                                            'grid' => 'col-md-6',
                                            'helperBlock' => '',
                                        ])
                                        @include('utilities.form.time', [
                                            'id' => 'pickup_time',
                                            'name' => 'pickup_time',
                                            'label' => 'وقت الاستلام',
                                            'isRequired' => true,
                                            'grid' => 'col-md-6',
                                            'helperBlock' => '',
                                        ])
                                    </div>
                                    <button type="submit" class="btn btn-primary">تأكيد الموعد</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-success mb-3">
                            موعد الاستلام: {{ $pickup['date'] }} — {{ $pickup['time'] ?? '' }}
                            @if (!empty($workflowData['otp']['code']) && empty($workflowData['otp']['verified_at']))
                                <div class="mt-2"><strong>رمز الاستلام (OTP):</strong> {{ $workflowData['otp']['code'] }}</div>
                                <small class="text-muted">أظهر هذا الرمز عند قسم المخزون.</small>
                            @endif
                        </div>
                    @endif
                    @break
            @endswitch

            @if ($isFinancial && !empty($financial['disbursed_at']))
                <div class="alert alert-success mt-3">
                    تم صرف المبلغ: <strong>{{ $financial['amount'] ?? '' }}</strong>
                </div>
            @endif
        @endif

        @if ($delivered && empty($satisfaction['completed_at']) && !$beneficiaryOrder->done)
            <div class="card border border-warning mt-3">
                <div class="card-header py-2">
                    <h6 class="mb-0">استبيان تقييم الرضا</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('beneficiary.beneficiary-orders.assistance-satisfaction', $beneficiaryOrder) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">تقييم الرضا <span class="text-danger">*</span></label>
                            <select name="satisfaction_rating" class="form-select" required>
                                <option value="">اختر التقييم</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        @include('utilities.form.textarea', [
                            'name' => 'satisfaction_comment',
                            'label' => 'ملاحظاتكم',
                            'isRequired' => false,
                            'value' => old('satisfaction_comment'),
                        ])
                        <button type="submit" class="btn btn-primary">إرسال الاستبيان</button>
                    </form>
                </div>
            </div>
        @elseif (!empty($satisfaction['completed_at']))
            <div class="alert alert-success mt-3 mb-0">تم إكمال استبيان الرضا. شكراً لتقييمكم.</div>
        @endif

        @include('dynamicservices::workflows.assistance.partials.history', [
            'dynamicServiceOrder' => $dynamicServiceOrder,
            'workflowContext' => $workflowContext,
            'open' => true,
        ])
    </div>
</div>
