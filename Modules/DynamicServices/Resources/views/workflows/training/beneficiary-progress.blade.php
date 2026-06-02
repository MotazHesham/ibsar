@php
    use Modules\DynamicServices\Services\TrainingWorkflowService;
    use Modules\DynamicServices\Workflows\TrainingWorkflowHandler;

    $service = $workflowContext['service'];
    $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
    $currentStep = $workflowContext['currentStep'];
    $isGroup = $service->service_type === 'group';
    $workflowData = $dynamicServiceOrder->workflow_data ?? [];
    $evaluationAppointment = $workflowData['evaluation_appointment'] ?? [];
    $evaluation = $workflowData['evaluation'] ?? [];
    $financial = $workflowData['financial'] ?? [];
    $sessions = $workflowData['sessions'] ?? [];
    $groupSchedule = $workflowData['group_schedule'] ?? [];
    $testResult = $workflowData['test'] ?? [];
    $trainingService = app(TrainingWorkflowService::class);

    $nextQrSession = null;
    foreach ($sessions as $session) {
        if (! empty($session['date']) && empty($session['attended'])) {
            $nextQrSession = $session;
            break;
        }
    }

    if (! $nextQrSession && $currentStep === TrainingWorkflowHandler::STEP_TESTING && empty($testResult['attended'])) {
        $nextQrSession = ['number' => 'test', 'label' => 'موعد الاختبار'];
    }
@endphp

<div class="card custom-card mt-3">
    <div class="card-header">
        <div class="card-title mb-0">مسار التدريب والتأهيل</div>
    </div>
    <div class="card-body">
        @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

        <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="text-muted">المرحلة الحالية</span>
            <span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span>
        </div>

        @if ($workflowContext['isRejected'])
            <div class="alert alert-danger mb-3">
                تم رفض الطلب
                @if ($beneficiaryOrder->refused_reason)
                    <div class="mt-2 mb-0"><strong>السبب:</strong> {{ $beneficiaryOrder->refused_reason }}</div>
                @endif
            </div>
        @elseif ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
            <div class="alert alert-success mb-3">
                تم إنهاء الطلب بنجاح. شكراً لمشاركتكم في البرنامج التدريبي.
            </div>
        @else
            @switch($currentStep)
                @case(TrainingWorkflowHandler::STEP_INITIAL_APPROVAL)
                    <div class="alert alert-warning mb-0">
                        طلبكم قيد الاعتماد من قبل الاستقبال. سيتم إشعاركم عند قبول الطلب أو رفضه.
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_EVALUATION_SCHEDULING)
                    <div class="alert alert-info mb-0">
                        تم قبول طلبكم. سيتم تحديد موعد التقييم (الاستقبال، البحث الاجتماعي، التدريب) وإرساله لكم.
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_ATTENDANCE)
                    @if (!empty($evaluationAppointment))
                        <div class="alert alert-primary mb-3">
                            <strong>موعد التقييم:</strong>
                            {{ $evaluationAppointment['date'] ?? '' }}
                            @if (!empty($evaluationAppointment['time']))
                                — {{ $evaluationAppointment['time'] }}
                            @endif
                            @if (!empty($evaluationAppointment['types']))
                                <div class="small mt-2">
                                    أنواع التقييم:
                                    {{ collect($evaluationAppointment['types'])->map(fn ($t) => TrainingWorkflowService::EVALUATION_APPOINTMENT_TYPES[$t] ?? $t)->implode('، ') }}
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="alert alert-info mb-0">
                        يرجى الحضور في الموعد المحدد. في حال تعذر الحضور سيتم التواصل معكم لإعادة الجدولة.
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_EVALUATION)
                    <div class="alert alert-info mb-0">
                        جاري إعداد نموذج التقييم من قبل الفريق المختص. سيتم إشعاركم عند حفظ التقرير.
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_FINANCIAL_APPROVAL)
                    <div class="alert alert-warning mb-0">
                        @if (!empty($evaluation))
                            تم حفظ تقرير التقييم.
                            @if (!empty($evaluation['qualified']))
                                يرجى إتمام سداد الرسوم إن كانت الدورة برسوم، ثم انتظار اعتماد المالية.
                            @endif
                        @else
                            جاري مراجعة المساهمة المالية من قبل قسم المالية.
                        @endif
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_DONATION_ALLOCATION)
                    <div class="alert alert-info mb-0">
                        تم اعتماد المالية. جاري تخصيص التبرع بحسب عدد الجلسات ({{ $workflowContext['sessionsCount'] ?? 0 }} جلسة).
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_SESSION_SCHEDULING)
                    @if (!empty($sessions))
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>الجلسة</th>
                                        <th>التاريخ</th>
                                        <th>الوقت</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td>{{ $session['number'] }}</td>
                                            <td>{{ $session['date'] ?? '—' }}</td>
                                            <td>{{ $session['time'] ?? '—' }}</td>
                                            <td>
                                                @if (!empty($session['attended']))
                                                    <span class="badge bg-success">حضر</span>
                                                @elseif (!empty($session['date']))
                                                    <span class="badge bg-warning">مجدول</span>
                                                @else
                                                    <span class="badge bg-light text-muted">لم يُجدول</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="alert alert-info mb-0">
                        سيتم إرسال مواعيد الجلسات لكم. يرجى إظهار باركود الحضور عند الوصول.
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_SEND_MEETING_SCHEDULE)
                    <div class="alert alert-info mb-0">
                        تم قبول طلبكم. سيتم إرسال جدول اللقاءات لجميع المسجلين عند اكتمال العدد.
                    </div>
                    @break

                @case(TrainingWorkflowHandler::STEP_START_PROGRAM)
                    @if (!empty($groupSchedule))
                        <div class="alert alert-primary mb-3">
                            <strong>جدول البرنامج:</strong>
                            من {{ $groupSchedule['start_date'] ?? '' }} إلى {{ $groupSchedule['end_date'] ?? '' }}
                            @if (!empty($groupSchedule['days']))
                                <div class="small mt-1">الأيام: {{ implode('، ', $groupSchedule['days']) }}</div>
                            @endif
                            @if (!empty($groupSchedule['start_time']) && !empty($groupSchedule['end_time']))
                                <div class="small">الوقت: {{ $groupSchedule['start_time'] }} — {{ $groupSchedule['end_time'] }}</div>
                            @endif
                        </div>
                    @endif
                    <div class="alert alert-info mb-0">يرجى الحضور حسب الجدول وإظهار باركود الحضور.</div>
                    @break

                @case(TrainingWorkflowHandler::STEP_TESTING)
                    @if (!empty($testResult))
                        <div class="alert {{ !empty($testResult['passed']) ? 'alert-success' : 'alert-danger' }} mb-3">
                            <strong>نتيجة الاختبار:</strong>
                            {{ !empty($testResult['passed']) ? 'اجتاز' : 'لم يجتز' }}
                            — المتوسط: {{ $testResult['average'] ?? 0 }}%
                        </div>
                    @else
                        <div class="alert alert-info mb-3">تم تحديد مرحلة الاختبار. يرجى الحضور في الموعد وإظهار باركود الحضور.</div>
                    @endif
                    @break
            @endswitch
        @endif

        @if (!empty($evaluation) && !empty($evaluation['submitted_at']))
            <div class="card border mt-3" id="evaluation-report">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">تقرير التقييم</h6>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                        <i class="ri-printer-line"></i> حفظ PDF
                    </button>
                </div>
                <div class="card-body small">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>الحالة البصرية:</strong>
                            {{ TrainingWorkflowService::VISUAL_STATUS_OPTIONS[$evaluation['visual_status'] ?? ''] ?? ($evaluation['visual_status'] ?? '—') }}
                            @if (($evaluation['visual_status'] ?? '') === 'other' && !empty($evaluation['visual_status_other']))
                                ({{ $evaluation['visual_status_other'] }})
                            @endif
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>عدد الجلسات:</strong> {{ $evaluation['sessions_count'] ?? '—' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>المؤهل للتدريب:</strong>
                            {{ !empty($evaluation['qualified']) ? 'نعم' : 'لا' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>المقيّم:</strong> {{ $evaluation['evaluator_name'] ?? '—' }}
                        </div>
                        @if (!empty($evaluation['notes']))
                            <div class="col-12 mb-2">
                                <strong>ملاحظات:</strong> {{ $evaluation['notes'] }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if (!empty($testResult) && empty($testResult['satisfaction_completed']))
            <div class="card border border-warning mt-3">
                <div class="card-header py-2">
                    <h6 class="mb-0">استبيان تقييم الرضا</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        يرجى تعبئة استبيان الرضا مباشرة بعد حفظ نتيجة الاختبار.
                        @if (!empty($testResult['passed']))
                            لا يمكن استلام الشهادة أو الجهاز إلا بعد إكمال استبيان الرضا.
                        @endif
                    </p>
                    <form action="{{ route('beneficiary.beneficiary-orders.training-satisfaction', $beneficiaryOrder) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">تقييم الرضا <span class="text-danger">*</span></label>
                            <select name="satisfaction_rating" class="form-select" required>
                                <option value="">اختر التقييم</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('satisfaction_rating') == $i)>{{ $i }} — {{ ['', 'ضعيف', 'مقبول', 'جيد', 'جيد جداً', 'ممتاز'][$i] }}</option>
                                @endfor
                            </select>
                        </div>
                        @include('utilities.form.textarea', [
                            'name' => 'satisfaction_comment',
                            'label' => 'ملاحظاتكم',
                            'isRequired' => false,
                            'value' => old('satisfaction_comment'),
                        ])
                        <button type="submit" class="btn btn-primary">إرسال استبيان الرضا</button>
                    </form>
                </div>
            </div>
        @elseif (!empty($testResult['satisfaction_completed']))
            <div class="alert alert-success mt-3 mb-0">
                تم إكمال استبيان الرضا. شكراً لتقييمكم.
                @if (!empty($testResult['needs_device']))
                    سيتم التواصل معكم بخصوص تسليم الجهاز.
                @else
                    سيتم إصدار الشهادة قريباً.
                @endif
            </div>
        @endif

        @if ($nextQrSession)
            <div class="card border mt-3">
                <div class="card-header py-2">
                    <h6 class="mb-0">باركود الحضور</h6>
                </div>
                <div class="card-body text-center">
                    @php
                        $sessionNumber = $nextQrSession['number'];
                        $qrPayload = $sessionNumber === 'test'
                            ? 'training_test:' . $beneficiaryOrder->id
                            : $trainingService->attendanceQrPayload($beneficiaryOrder, (int) $sessionNumber);
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrPayload) }}"
                        alt="QR">
                    <p class="text-muted mt-3 mb-0 small">
                        @if ($sessionNumber === 'test')
                            باركود حضور الاختبار
                        @else
                            باركود حضور الجلسة رقم {{ $sessionNumber }}
                            @if (!empty($nextQrSession['date']))
                                — {{ $nextQrSession['date'] }} {{ $nextQrSession['time'] ?? '' }}
                            @endif
                        @endif
                    </p>
                </div>
            </div>
        @endif

        @include('dynamicservices::workflows.training.partials.history', [
            'dynamicServiceOrder' => $dynamicServiceOrder,
            'workflowContext' => $workflowContext,
            'open' => true,
        ])
    </div>
</div>
