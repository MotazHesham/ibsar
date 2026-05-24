<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Services\TrainingWorkflowService;

class TrainingWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_INITIAL_APPROVAL = 'initial_approval';
    public const STEP_EVALUATION_SCHEDULING = 'evaluation_scheduling';
    public const STEP_ATTENDANCE = 'attendance';
    public const STEP_EVALUATION = 'evaluation';
    public const STEP_FINANCIAL_APPROVAL = 'financial_approval';
    public const STEP_DONATION_ALLOCATION = 'donation_allocation';
    public const STEP_SESSION_SCHEDULING = 'session_scheduling';
    public const STEP_SEND_MEETING_SCHEDULE = 'send_meeting_schedule';
    public const STEP_START_PROGRAM = 'start_program';
    public const STEP_TESTING = 'testing';

    public const ACTION_SUBMIT_EVALUATION_SCHEDULE = 'submit_evaluation_schedule';
    public const ACTION_SUBMIT_EVALUATION = 'submit_evaluation';
    public const ACTION_RESCHEDULE = 'reschedule';
    public const ACTION_APPROVE_FINANCIAL = 'approve_financial';
    public const ACTION_REJECT_FINANCIAL = 'reject_financial';
    public const ACTION_CONFIRM_DONATION = 'confirm_donation';
    public const ACTION_SCHEDULE_SESSION = 'schedule_session';
    public const ACTION_MARK_SESSION_ATTENDED = 'mark_session_attended';
    public const ACTION_SUBMIT_TEST = 'submit_test';
    public const ACTION_COMPLETE_SATISFACTION = 'complete_satisfaction';
    public const ACTION_SUBMIT_GROUP_SCHEDULE = 'submit_group_schedule';
    public const ACTION_MARK_PROGRAM_ATTENDANCE = 'mark_program_attendance';

    public function __construct(
        protected TrainingWorkflowService $trainingService
    ) {
    }

    public function category(): string
    {
        return DynamicService::CATEGORY_TRAINING;
    }

    public function viewName(): string
    {
        return 'dynamicservices::workflows.training.edit-status';
    }

    public function steps(DynamicService $service): array
    {
        if ($service->service_type === 'group') {
            return [
                self::STEP_INITIAL_APPROVAL => 'الاعتماد',
                self::STEP_SEND_MEETING_SCHEDULE => 'إرسال جدول اللقاءات',
                self::STEP_START_PROGRAM => 'البدء في البرنامج',
                self::STEP_TESTING => 'الاختبار',
                self::STEP_COMPLETED => 'مكتمل',
            ];
        }

        return [
            self::STEP_INITIAL_APPROVAL => 'اعتماد الاستقبال',
            self::STEP_EVALUATION_SCHEDULING => 'جدولة موعد التقييم',
            self::STEP_ATTENDANCE => 'الحضور',
            self::STEP_EVALUATION => 'نموذج التقييم',
            self::STEP_FINANCIAL_APPROVAL => 'اعتماد المالية',
            self::STEP_DONATION_ALLOCATION => 'تخصيص التبرعات',
            self::STEP_SESSION_SCHEDULING => 'جدولة المواعيد',
            self::STEP_TESTING => 'الاختبار',
            self::STEP_COMPLETED => 'مكتمل',
        ];
    }

    public function initialStep(DynamicService $service): string
    {
        return self::STEP_INITIAL_APPROVAL;
    }

    public function availableActions(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        if ($this->isCompleted($dynamicServiceOrder) || $this->isRejected($dynamicServiceOrder)) {
            return [];
        }

        if ($service->service_type === 'group') {
            return $this->groupActions($dynamicServiceOrder);
        }

        return $this->individualActions($beneficiaryOrder, $dynamicServiceOrder);
    }

    protected function individualActions(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): array
    {
        $test = $dynamicServiceOrder->workflow_data['test'] ?? null;

        return match ($dynamicServiceOrder->workflow_step) {
            self::STEP_INITIAL_APPROVAL => $this->approvalActions('يعتمد', 'لا يعتمد'),
            self::STEP_EVALUATION_SCHEDULING => [[
                'key' => self::ACTION_SUBMIT_EVALUATION_SCHEDULE,
                'label' => 'حفظ وإرسال موعد التقييم',
                'type' => 'primary',
                'form_submit' => true,
            ]],
            self::STEP_ATTENDANCE => array_merge($this->attendanceActions(), [[
                'key' => self::ACTION_RESCHEDULE,
                'label' => 'إعادة جدولة الموعد',
                'type' => 'secondary',
            ]]),
            self::STEP_EVALUATION => [[
                'key' => self::ACTION_SUBMIT_EVALUATION,
                'label' => 'حفظ نموذج التقييم',
                'type' => 'primary',
                'form_submit' => true,
            ]],
            self::STEP_FINANCIAL_APPROVAL => [
                ['key' => self::ACTION_APPROVE_FINANCIAL, 'label' => 'يعتمد (المالية)', 'type' => 'success'],
                ['key' => self::ACTION_REJECT_FINANCIAL, 'label' => 'لا يعتمد (لم يسدد)', 'type' => 'danger'],
            ],
            self::STEP_DONATION_ALLOCATION => [[
                'key' => self::ACTION_CONFIRM_DONATION,
                'label' => 'تأكيد تخصيص التبرع',
                'type' => 'primary',
            ]],
            self::STEP_SESSION_SCHEDULING => $this->sessionSchedulingActions($dynamicServiceOrder),
            self::STEP_TESTING => $this->testingActions($test),
            default => [],
        };
    }

    protected function groupActions(DynamicServiceOrder $dynamicServiceOrder): array
    {
        $test = $dynamicServiceOrder->workflow_data['test'] ?? null;

        return match ($dynamicServiceOrder->workflow_step) {
            self::STEP_INITIAL_APPROVAL => $this->approvalActions('يعتمد', 'لا يعتمد'),
            self::STEP_SEND_MEETING_SCHEDULE => [[
                'key' => self::ACTION_SUBMIT_GROUP_SCHEDULE,
                'label' => 'حفظ وإرسال جدول اللقاءات',
                'type' => 'primary',
                'form_submit' => true,
            ]],
            self::STEP_START_PROGRAM => [[
                'key' => self::ACTION_MARK_PROGRAM_ATTENDANCE,
                'label' => 'تسجيل حضور (قص الباركود)',
                'type' => 'success',
            ], [
                'key' => self::ACTION_ADVANCE,
                'label' => 'انتهاء الجلسات والانتقال للاختبار',
                'type' => 'primary',
            ]],
            self::STEP_TESTING => $this->testingActions($test),
            default => [],
        };
    }

    protected function sessionSchedulingActions(DynamicServiceOrder $dynamicServiceOrder): array
    {
        $sessions = $dynamicServiceOrder->workflow_data['sessions'] ?? [];
        $actions = [[
            'key' => self::ACTION_SCHEDULE_SESSION,
            'label' => 'حفظ وجدولة الموعد',
            'type' => 'primary',
            'form_submit' => true,
        ]];

        foreach ($sessions as $session) {
            if (! empty($session['date']) && empty($session['attended'])) {
                $actions[] = [
                    'key' => self::ACTION_MARK_SESSION_ATTENDED,
                    'label' => 'تسجيل حضور الجلسة ' . $session['number'],
                    'type' => 'success',
                    'session_number' => $session['number'],
                ];
            }
        }

        if ($this->trainingService->allSessionsAttended($dynamicServiceOrder)) {
            $actions[] = [
                'key' => self::ACTION_ADVANCE,
                'label' => 'جدولة موعد الاختبار',
                'type' => 'warning',
            ];
        }

        return $actions;
    }

    protected function testingActions(?array $test): array
    {
        if (empty($test)) {
            return [[
                'key' => self::ACTION_SUBMIT_TEST,
                'label' => 'حفظ درجة الاختبار',
                'type' => 'primary',
                'form_submit' => true,
            ]];
        }

        if (empty($test['passed'])) {
            return [];
        }

        if (empty($test['satisfaction_completed'])) {
            return [[
                'key' => self::ACTION_COMPLETE_SATISFACTION,
                'label' => 'تأكيد إكمال استبيان الرضا',
                'type' => 'success',
            ]];
        }

        return [[
            'key' => self::ACTION_COMPLETE,
            'label' => 'إنهاء الطلب',
            'type' => 'success',
        ]];
    }

    public function processAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        array $data = []
    ): void {
        $step = $dynamicServiceOrder->workflow_step;

        if ($action === self::ACTION_REJECT) {
            $this->reject($beneficiaryOrder, $dynamicServiceOrder, $data['reason'] ?? null);
            $this->trainingService->notifyBeneficiaryUserAlert(
                $beneficiaryOrder,
                'نعتذر، لم يتم قبول طلبكم. ' . ($data['reason'] ?? '')
            );

            return;
        }

        $validated = $this->trainingService->validateAndExtract($step, $action, $data, $service);

        if ($service->service_type === 'group') {
            $this->processGroupAction($beneficiaryOrder, $dynamicServiceOrder, $service, $action, $step, $validated);

            return;
        }

        $this->processIndividualAction($beneficiaryOrder, $dynamicServiceOrder, $service, $action, $step, $validated);
    }

    protected function processIndividualAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        string $step,
        array $data
    ): void {
        match (true) {
            $step === self::STEP_INITIAL_APPROVAL && $action === self::ACTION_APPROVE => $this->handleInitialApproval($beneficiaryOrder, $dynamicServiceOrder),

            $step === self::STEP_EVALUATION_SCHEDULING && $action === self::ACTION_SUBMIT_EVALUATION_SCHEDULE => $this->handleEvaluationSchedule($beneficiaryOrder, $dynamicServiceOrder, $data),

            $step === self::STEP_ATTENDANCE && $action === self::ACTION_MARK_NOT_ATTENDED => $this->handleNotAttended($beneficiaryOrder, $dynamicServiceOrder),

            $step === self::STEP_ATTENDANCE && $action === self::ACTION_MARK_ATTENDED => $this->handleMarkAttended($dynamicServiceOrder),

            $step === self::STEP_ATTENDANCE && $action === self::ACTION_RESCHEDULE => $this->moveToStep($dynamicServiceOrder, self::STEP_EVALUATION_SCHEDULING, 1),

            $step === self::STEP_EVALUATION && $action === self::ACTION_SUBMIT_EVALUATION => $this->handleEvaluationSubmit($beneficiaryOrder, $dynamicServiceOrder, $data),

            $step === self::STEP_FINANCIAL_APPROVAL && $action === self::ACTION_APPROVE_FINANCIAL => $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_DONATION_ALLOCATION, 4),

            $step === self::STEP_FINANCIAL_APPROVAL && $action === self::ACTION_REJECT_FINANCIAL => $this->handleFinancialRejection($dynamicServiceOrder, $data),

            $step === self::STEP_DONATION_ALLOCATION && $action === self::ACTION_CONFIRM_DONATION => $this->handleDonationConfirmed($beneficiaryOrder, $dynamicServiceOrder),

            $step === self::STEP_SESSION_SCHEDULING && $action === self::ACTION_SCHEDULE_SESSION => $this->handleScheduleSession($beneficiaryOrder, $dynamicServiceOrder, $data),

            $step === self::STEP_SESSION_SCHEDULING && $action === self::ACTION_MARK_SESSION_ATTENDED => $this->handleSessionAttendance($dynamicServiceOrder, $data),

            $step === self::STEP_SESSION_SCHEDULING && $action === self::ACTION_ADVANCE => $this->moveToStep($dynamicServiceOrder, self::STEP_TESTING),

            $step === self::STEP_TESTING && $action === self::ACTION_SUBMIT_TEST => $this->handleTestSubmit($beneficiaryOrder, $dynamicServiceOrder, $data),

            $step === self::STEP_TESTING && $action === self::ACTION_COMPLETE_SATISFACTION => $this->handleSatisfactionComplete($dynamicServiceOrder, $data),

            $step === self::STEP_TESTING && $action === self::ACTION_COMPLETE => $this->complete($beneficiaryOrder, $dynamicServiceOrder),

            default => null,
        };
    }

    protected function processGroupAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        string $step,
        array $data
    ): void {
        match (true) {
            $step === self::STEP_INITIAL_APPROVAL && $action === self::ACTION_APPROVE => $this->handleGroupInitialApproval($beneficiaryOrder, $dynamicServiceOrder),

            $step === self::STEP_SEND_MEETING_SCHEDULE && $action === self::ACTION_SUBMIT_GROUP_SCHEDULE => $this->handleGroupSchedule($beneficiaryOrder, $dynamicServiceOrder, $data),

            $step === self::STEP_START_PROGRAM && $action === self::ACTION_MARK_PROGRAM_ATTENDANCE => $this->appendHistory($dynamicServiceOrder, $step, $action),

            $step === self::STEP_START_PROGRAM && $action === self::ACTION_ADVANCE => $this->moveToStep($dynamicServiceOrder, self::STEP_TESTING),

            $step === self::STEP_TESTING && $action === self::ACTION_SUBMIT_TEST => $this->handleTestSubmit($beneficiaryOrder, $dynamicServiceOrder, $data),

            $step === self::STEP_TESTING && $action === self::ACTION_COMPLETE_SATISFACTION => $this->handleSatisfactionComplete($dynamicServiceOrder, $data),

            $step === self::STEP_TESTING && $action === self::ACTION_COMPLETE => $this->complete($beneficiaryOrder, $dynamicServiceOrder),

            default => null,
        };
    }

    protected function handleInitialApproval(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_EVALUATION_SCHEDULING, 1);
    }

    protected function handleEvaluationSchedule(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $this->trainingService->mergeWorkflowData($dynamicServiceOrder, $data);
        $this->moveToStep($dynamicServiceOrder, self::STEP_ATTENDANCE, 1);

        $appointment = $data['evaluation_appointment'] ?? [];
        $this->trainingService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم تحديد موعد التقييم بتاريخ ' . ($appointment['date'] ?? '') . ' الساعة ' . ($appointment['time'] ?? '')
        );
        $this->appendHistory($dynamicServiceOrder, self::STEP_ATTENDANCE, self::ACTION_SUBMIT_EVALUATION_SCHEDULE, $appointment);
    }

    protected function handleNotAttended(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->setWorkflowMeta($dynamicServiceOrder, 'attendance', 'not_attended');
        $this->appendHistory($dynamicServiceOrder, self::STEP_ATTENDANCE, self::ACTION_MARK_NOT_ATTENDED);
        $this->moveToStep($dynamicServiceOrder, self::STEP_EVALUATION_SCHEDULING, 1);
    }

    protected function handleEvaluationSubmit(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $evaluation = $data['evaluation'] ?? [];

        if (empty($evaluation['qualified'])) {
            $this->reject($beneficiaryOrder, $dynamicServiceOrder, 'غير مؤهل للتدريب حسب نموذج التقييم');
            $this->trainingService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'نعتذر، لم يتم اعتمادكم للتدريب بناءً على نموذج التقييم.');

            return;
        }

        $this->trainingService->mergeWorkflowData($dynamicServiceOrder, $data);
        $this->trainingService->initializeSessions($dynamicServiceOrder, (int) ($evaluation['sessions_count'] ?? 0));
        $this->moveToStep($dynamicServiceOrder, self::STEP_FINANCIAL_APPROVAL, 3);

        $this->trainingService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم حفظ تقرير التقييم. سيتم إرسال رابط سداد الرسوم في حال كانت الدورة برسوم.'
        );
        $this->appendHistory($dynamicServiceOrder, self::STEP_FINANCIAL_APPROVAL, self::ACTION_SUBMIT_EVALUATION, $evaluation);
    }

    protected function handleFinancialRejection(DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $this->trainingService->mergeWorkflowData($dynamicServiceOrder, [
            'financial' => [
                'approved' => false,
                'rejected_at' => now()->toDateTimeString(),
                'note' => $data['note'] ?? null,
            ],
        ]);
        $this->appendHistory($dynamicServiceOrder, self::STEP_FINANCIAL_APPROVAL, self::ACTION_REJECT_FINANCIAL);
    }

    protected function handleMarkAttended(DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->setWorkflowMeta($dynamicServiceOrder, 'attendance', 'attended');
        $this->moveToStep($dynamicServiceOrder, self::STEP_EVALUATION, 2);
        $this->appendHistory($dynamicServiceOrder, self::STEP_EVALUATION, self::ACTION_MARK_ATTENDED);
    }

    protected function handleDonationConfirmed(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        if (! $beneficiaryOrder->donationAllocations()->exists()) {
            throw ValidationException::withMessages([
                'donation' => 'يجب تخصيص تبرع قبل المتابعة.',
            ]);
        }

        $this->moveToStep($dynamicServiceOrder, self::STEP_SESSION_SCHEDULING, 5);
        $this->appendHistory($dynamicServiceOrder, self::STEP_SESSION_SCHEDULING, self::ACTION_CONFIRM_DONATION);
    }

    protected function handleScheduleSession(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $this->trainingService->scheduleSession(
            $dynamicServiceOrder,
            (int) $data['session_number'],
            $data['session_date'],
            $data['session_time']
        );

        $this->trainingService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم تحديد موعد الجلسة رقم ' . $data['session_number'] . ' بتاريخ ' . $data['session_date']
        );
        $this->appendHistory($dynamicServiceOrder, self::STEP_SESSION_SCHEDULING, self::ACTION_SCHEDULE_SESSION, $data);
    }

    protected function handleSessionAttendance(DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $this->trainingService->markSessionAttended($dynamicServiceOrder, (int) $data['session_number']);
        $this->appendHistory($dynamicServiceOrder, self::STEP_SESSION_SCHEDULING, self::ACTION_MARK_SESSION_ATTENDED, $data);
    }

    protected function handleTestSubmit(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $test = $data['test'] ?? [];
        $this->trainingService->mergeWorkflowData($dynamicServiceOrder, $data);

        if (empty($test['passed'])) {
            $this->trainingService->notifyBeneficiaryUserAlert($beneficiaryOrder, 'لم يتم الاجتياز في الاختبار. متوسط الدرجة: ' . ($test['average'] ?? 0) . '%');
            $this->appendHistory($dynamicServiceOrder, self::STEP_TESTING, self::ACTION_SUBMIT_TEST, $test);

            return;
        }

        $message = ! empty($test['needs_device'])
            ? 'تم الاجتياز. يرجى إكمال استبيان الرضا قبل استلام الجهاز.'
            : 'تم الاجتياز. يرجى إكمال استبيان الرضا قبل إصدار الشهادة.';

        $this->trainingService->notifyBeneficiaryUserAlert($beneficiaryOrder, $message);
        $this->appendHistory($dynamicServiceOrder, self::STEP_TESTING, self::ACTION_SUBMIT_TEST, $test);
    }

    protected function handleSatisfactionComplete(DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $existing = $dynamicServiceOrder->workflow_data['test'] ?? [];
        $this->trainingService->mergeWorkflowData($dynamicServiceOrder, [
            'test' => array_merge($existing, ['satisfaction_completed' => true]),
        ]);
        $this->appendHistory($dynamicServiceOrder, self::STEP_TESTING, self::ACTION_COMPLETE_SATISFACTION);
    }

    protected function handleGroupInitialApproval(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder): void
    {
        $this->approveAndMove($beneficiaryOrder, $dynamicServiceOrder, self::STEP_SEND_MEETING_SCHEDULE, 1);
        $this->trainingService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم قبول طلبكم. سيتم إشعاركم بمواعيد البرنامج لاحقاً.'
        );
    }

    protected function handleGroupSchedule(BeneficiaryOrder $beneficiaryOrder, DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $this->trainingService->mergeWorkflowData($dynamicServiceOrder, $data);
        $this->moveToStep($dynamicServiceOrder, self::STEP_START_PROGRAM, 2);

        $schedule = $data['group_schedule'] ?? [];
        $this->trainingService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم تحديد جدول اللقاءات من ' . ($schedule['start_date'] ?? '') . ' إلى ' . ($schedule['end_date'] ?? '')
        );
        $this->appendHistory($dynamicServiceOrder, self::STEP_START_PROGRAM, self::ACTION_SUBMIT_GROUP_SCHEDULE, $schedule);
    }
}
