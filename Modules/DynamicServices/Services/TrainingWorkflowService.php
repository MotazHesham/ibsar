<?php

namespace Modules\DynamicServices\Services;

use App\Models\BeneficiaryOrder;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Workflows\TrainingWorkflowHandler;

class TrainingWorkflowService
{
    public const VISUAL_STATUS_OPTIONS = [
        'total_blind' => 'كف كلي',
        'partial_blind' => 'كف جزئي',
        'severe_low_vision' => 'ضعف بصر شديد',
        'other' => 'أخرى',
    ];

    public const EVALUATION_APPOINTMENT_TYPES = [
        'reception' => 'الاستقبال',
        'social_research' => 'البحث الاجتماعي',
        'training' => 'التدريب',
    ];

    public const TEST_CRITERIA = [
        'content_familiarity' => 'الإلمام بالمحتوى التدريبي',
        'skill_execution' => 'صحة تنفيذ المهارة وإتقانها',
        'tool_usage' => 'حسن استخدام الأدوات والوسائل المساندة',
        'independence' => 'الاستقلالية في الأداء',
        'procedure_compliance' => 'الالتزام بالإجراءات المعتمدة أثناء التنفيذ',
    ];

    public const PASS_THRESHOLD = 70;

    public function getViewData(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $beneficiary = $beneficiaryOrder->beneficiary;
        $groupStats = $service->service_type === 'group' ? $this->getGroupWorkflowStats($service) : [];

        return [
            'trainers' => $this->getTrainers(),
            'visualStatusOptions' => self::VISUAL_STATUS_OPTIONS,
            'evaluationAppointmentTypes' => self::EVALUATION_APPOINTMENT_TYPES,
            'testCriteria' => self::TEST_CRITERIA,
            'passThreshold' => self::PASS_THRESHOLD,
            'beneficiaryDob' => $beneficiary?->dob,
            'evaluation' => $workflowData['evaluation'] ?? [],
            'evaluationAppointment' => $workflowData['evaluation_appointment'] ?? [],
            'financial' => $workflowData['financial'] ?? [],
            'sessions' => $workflowData['sessions'] ?? [],
            'sessionsCount' => (int) ($workflowData['evaluation']['sessions_count'] ?? 0),
            'groupSchedule' => $workflowData['group_schedule'] ?? [],
            'testResult' => $workflowData['test'] ?? [],
            'hasDonationAllocation' => $beneficiaryOrder->donationAllocations()->exists(),
            'programMeetings' => $service->program_meetings ?? [],
            'groupApplicants' => $groupStats['applicants'] ?? collect(),
            'groupApprovedCount' => $groupStats['approvedCount'] ?? 0,
            'groupPendingCount' => $groupStats['pendingCount'] ?? 0,
            'canScheduleGroupMeetings' => $groupStats['canScheduleMeetings'] ?? false,
        ];
    }

    public function getTrainers()
    {
        return User::where('user_type', 'staff')
            ->where('employee_type', 'trainer')
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function validateAndExtract(string $step, string $action, array $data, DynamicService $service): array
    {
        return match ($step) {
            TrainingWorkflowHandler::STEP_EVALUATION_SCHEDULING => $this->validateEvaluationSchedule($data),
            TrainingWorkflowHandler::STEP_EVALUATION => $this->validateEvaluationForm($data),
            TrainingWorkflowHandler::STEP_SESSION_SCHEDULING => $this->validateSessionSchedule($data, $action),
            TrainingWorkflowHandler::STEP_SEND_MEETING_SCHEDULE => $this->validateGroupSchedule($data),
            TrainingWorkflowHandler::STEP_START_PROGRAM => $this->validateGroupAttendance($data, $action),
            TrainingWorkflowHandler::STEP_TESTING => $this->validateTestForm($data, $action, $service),
            default => $data,
        };
    }

    protected function validateEvaluationSchedule(array $data): array
    {
        $validated = Validator::make($data, [
            'evaluation_date' => 'required|date_format:' . config('panel.date_format'),
            'evaluation_time' => 'required|date_format:H:i',
            'evaluation_types' => 'required|array|min:1',
            'evaluation_types.*' => 'in:' . implode(',', array_keys(self::EVALUATION_APPOINTMENT_TYPES)),
        ], [
            'evaluation_date.required' => 'يرجى تحديد تاريخ التقييم',
            'evaluation_time.required' => 'يرجى تحديد وقت التقييم',
            'evaluation_types.required' => 'يرجى اختيار نوع التقييم',
        ])->validate();

        return [
            'evaluation_appointment' => [
                'date' => $validated['evaluation_date'],
                'time' => $validated['evaluation_time'],
                'types' => $validated['evaluation_types'],
                'sent_at' => now()->toDateTimeString(),
            ],
        ];
    }

    protected function validateEvaluationForm(array $data): array
    {
        $rules = [
            'visual_status' => 'required|in:' . implode(',', array_keys(self::VISUAL_STATUS_OPTIONS)),
            'visual_status_other' => 'nullable|string|max:255',
            'sessions_count' => 'required|integer|min:1|max:100',
            'qualified' => 'required|in:yes,no',
            'evaluator_id' => 'required|exists:users,id',
            'evaluation_notes' => 'nullable|string|max:2000',
        ];

        $validated = Validator::make($data, $rules)->validate();

        if ($validated['visual_status'] === 'other' && empty($validated['visual_status_other'])) {
            throw ValidationException::withMessages([
                'visual_status_other' => 'يرجى توضيح الحالة البصرية',
            ]);
        }

        return [
            'evaluation' => [
                'visual_status' => $validated['visual_status'],
                'visual_status_other' => $validated['visual_status_other'] ?? null,
                'sessions_count' => (int) $validated['sessions_count'],
                'qualified' => $validated['qualified'] === 'yes',
                'evaluator_id' => (int) $validated['evaluator_id'],
                'evaluator_name' => User::find($validated['evaluator_id'])?->name,
                'notes' => $validated['evaluation_notes'] ?? null,
                'submitted_at' => now()->toDateTimeString(),
            ],
        ];
    }

    protected function validateSessionSchedule(array $data, string $action): array
    {
        if ($action === TrainingWorkflowHandler::ACTION_SCHEDULE_SESSION) {
            return Validator::make($data, [
                'session_number' => 'required|integer|min:1',
                'session_date' => 'required|date_format:' . config('panel.date_format'),
                'session_time' => 'required|date_format:H:i',
            ])->validate();
        }

        if ($action === TrainingWorkflowHandler::ACTION_MARK_SESSION_ATTENDED) {
            return Validator::make($data, [
                'session_number' => 'required|integer|min:1',
            ])->validate();
        }

        return $data;
    }

    protected function validateGroupSchedule(array $data): array
    {
        $validated = Validator::make($data, [
            'schedule_start_date' => 'required|date',
            'schedule_end_date' => 'required|date|after_or_equal:schedule_start_date',
            'schedule_days' => 'required|array|min:1',
            'schedule_days.*' => 'string',
            'schedule_start_time' => 'required',
            'schedule_end_time' => 'required',
        ])->validate();

        return [
            'group_schedule' => [
                'start_date' => $validated['schedule_start_date'],
                'end_date' => $validated['schedule_end_date'],
                'days' => $validated['schedule_days'],
                'start_time' => $validated['schedule_start_time'],
                'end_time' => $validated['schedule_end_time'],
                'sent_at' => now()->toDateTimeString(),
            ],
        ];
    }

    protected function validateGroupAttendance(array $data, string $action): array
    {
        if ($action !== TrainingWorkflowHandler::ACTION_MARK_PROGRAM_ATTENDANCE) {
            return $data;
        }

        return Validator::make($data, [
            'attendance_barcode' => 'required|string|max:255',
        ], [
            'attendance_barcode.required' => 'يرجى مسح الباركود لتسجيل الحضور.',
        ])->validate();
    }

    protected function validateTestForm(array $data, string $action, DynamicService $service): array
    {
        if ($action === TrainingWorkflowHandler::ACTION_SUBMIT_TEST) {
            $rules = [];
            foreach (array_keys(self::TEST_CRITERIA) as $key) {
                $rules["test_scores.{$key}"] = 'required|numeric|min:0|max:100';
            }
            $rules['needs_device'] = 'required|in:yes,no';
            if ($service->service_type === 'group') {
                $rules['test_attendance_barcode'] = 'required|string|max:255';
            }

            $validated = Validator::make($data, $rules)->validate();

            $scores = $validated['test_scores'];
            $average = round(array_sum($scores) / count($scores), 2);
            $passed = $average >= self::PASS_THRESHOLD;

            return [
                'test' => [
                    'scores' => $scores,
                    'average' => $average,
                    'passed' => $passed,
                    'needs_device' => $validated['needs_device'] === 'yes',
                    'attendance_barcode' => $validated['test_attendance_barcode'] ?? null,
                    'attended' => !empty($validated['test_attendance_barcode']) || $service->service_type !== 'group',
                    'attendance_scanned_at' => !empty($validated['test_attendance_barcode']) ? now()->toDateTimeString() : null,
                    'submitted_at' => now()->toDateTimeString(),
                    'satisfaction_completed' => false,
                ],
            ];
        }

        if ($action === TrainingWorkflowHandler::ACTION_COMPLETE_SATISFACTION) {
            return ['test' => array_merge($data['existing_test'] ?? [], ['satisfaction_completed' => true])];
        }

        return $data;
    }

    public function mergeWorkflowData(DynamicServiceOrder $dynamicServiceOrder, array $payload): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        foreach ($payload as $key => $value) {
            if ($key === 'test' && isset($workflowData['test']) && is_array($value)) {
                $workflowData['test'] = array_merge($workflowData['test'], $value);
            } else {
                $workflowData[$key] = $value;
            }
        }

        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);
    }

    public function initializeSessions(DynamicServiceOrder $dynamicServiceOrder, int $count): void
    {
        $sessions = [];
        for ($i = 1; $i <= $count; $i++) {
            $sessions[] = [
                'number' => $i,
                'date' => null,
                'time' => null,
                'scheduled_at' => null,
                'attended' => false,
                'barcode_scanned_at' => null,
            ];
        }

        $this->mergeWorkflowData($dynamicServiceOrder, ['sessions' => $sessions]);
    }

    public function scheduleSession(DynamicServiceOrder $dynamicServiceOrder, int $sessionNumber, string $date, string $time): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $sessions = $workflowData['sessions'] ?? [];

        foreach ($sessions as &$session) {
            if ((int) $session['number'] === $sessionNumber) {
                $session['date'] = $date;
                $session['time'] = $time;
                $session['scheduled_at'] = now()->toDateTimeString();
                break;
            }
        }

        $this->mergeWorkflowData($dynamicServiceOrder, ['sessions' => $sessions]);
    }

    public function markSessionAttended(DynamicServiceOrder $dynamicServiceOrder, int $sessionNumber): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $sessions = $workflowData['sessions'] ?? [];

        foreach ($sessions as &$session) {
            if ((int) $session['number'] === $sessionNumber) {
                $session['attended'] = true;
                $session['barcode_scanned_at'] = now()->toDateTimeString();
                break;
            }
        }

        $this->mergeWorkflowData($dynamicServiceOrder, ['sessions' => $sessions]);
    }

    public function allSessionsAttended(DynamicServiceOrder $dynamicServiceOrder): bool
    {
        $sessions = $dynamicServiceOrder->workflow_data['sessions'] ?? [];

        if (empty($sessions)) {
            return false;
        }

        foreach ($sessions as $session) {
            if (empty($session['attended'])) {
                return false;
            }
        }

        return true;
    }

    public function notifyBeneficiaryUserAlert(BeneficiaryOrder $beneficiaryOrder, string $text, ?string $link = null): void
    {
        $userId = $beneficiaryOrder->beneficiary?->user_id;

        if (! $userId) {
            return;
        }

        $alert = UserAlert::create([
            'alert_text' => $text,
            'alert_link' => $link ?? $this->beneficiaryOrderShowUrl($beneficiaryOrder),
            'user_type' => 'beneficiary',
        ]);

        $alert->users()->sync([$userId]);
    }

    public function notifyStaffUserAlert(string $text, ?string $link = null, ?Collection $staffUsers = null): void
    {
        $users = $staffUsers ?? User::query()
            ->where('user_type', 'staff')
            ->whereIn('employee_type', ['trainer', 'specialist'])
            ->get(['id']);

        if ($users->isEmpty()) {
            return;
        }

        $alert = UserAlert::create([
            'alert_text' => $text,
            'alert_link' => $link ?? '#',
            'user_type' => 'staff',
        ]);

        $alert->users()->sync($users->pluck('id')->all());
    }

    public function getGroupWorkflowStats(DynamicService $service): array
    {
        $orders = DynamicServiceOrder::query()
            ->where('dynamic_service_id', $service->id)
            ->with('beneficiaryOrder.beneficiary.user')
            ->orderBy('created_at')
            ->get();

        $approvedCount = $orders->filter(function (DynamicServiceOrder $order) {
            return in_array($order->workflow_step, [
                TrainingWorkflowHandler::STEP_SEND_MEETING_SCHEDULE,
                TrainingWorkflowHandler::STEP_START_PROGRAM,
                TrainingWorkflowHandler::STEP_TESTING,
                TrainingWorkflowHandler::STEP_COMPLETED,
            ], true) && $order->workflow_step !== TrainingWorkflowHandler::STEP_REJECTED;
        })->count();

        $pendingCount = $orders->where('workflow_step', TrainingWorkflowHandler::STEP_INITIAL_APPROVAL)->count();

        $applicants = $orders->map(function (DynamicServiceOrder $order) {
            $beneficiaryOrder = $order->beneficiaryOrder;
            $beneficiaryName = $beneficiaryOrder?->beneficiary?->user?->name
                ?? $beneficiaryOrder?->beneficiary?->name
                ?? ('مستفيد #' . ($beneficiaryOrder?->id ?? $order->beneficiary_order_id));

            return [
                'beneficiary_order_id' => $beneficiaryOrder?->id,
                'name' => $beneficiaryName,
                'step' => $order->workflow_step,
                'is_approved' => in_array($order->workflow_step, [
                    TrainingWorkflowHandler::STEP_SEND_MEETING_SCHEDULE,
                    TrainingWorkflowHandler::STEP_START_PROGRAM,
                    TrainingWorkflowHandler::STEP_TESTING,
                    TrainingWorkflowHandler::STEP_COMPLETED,
                ], true),
            ];
        });

        return [
            'approvedCount' => $approvedCount,
            'pendingCount' => $pendingCount,
            'canScheduleMeetings' => $approvedCount > 0 && $pendingCount === 0,
            'applicants' => $applicants,
        ];
    }

    public function approvedGroupOrdersForScheduling(DynamicService $service)
    {
        return DynamicServiceOrder::query()
            ->where('dynamic_service_id', $service->id)
            ->where('workflow_step', TrainingWorkflowHandler::STEP_SEND_MEETING_SCHEDULE)
            ->with('beneficiaryOrder.beneficiary')
            ->get();
    }

    public function notifyGroupBeneficiaries(DynamicService $service, string $text): void
    {
        $orders = BeneficiaryOrder::query()
            ->where('service_type', 'dynamic_' . $service->id)
            ->where('accept_status', 'yes')
            ->with('beneficiary')
            ->get();

        foreach ($orders as $order) {
            $this->notifyBeneficiaryUserAlert($order, $text);
        }
    }

    public function beneficiaryOrderShowUrl(BeneficiaryOrder $beneficiaryOrder): string
    {
        return route('beneficiary.beneficiary-orders.show', $beneficiaryOrder);
    }

    public function attendanceQrPayload(BeneficiaryOrder $beneficiaryOrder, int $sessionNumber): string
    {
        return 'training_attendance:' . $beneficiaryOrder->id . ':' . $sessionNumber;
    }

    public function resolveBeneficiaryOrderIdFromBarcode(string $barcode): int
    {
        $payload = trim($barcode);
        if (preg_match('/^training_attendance:(\d+):(\d+)$/', $payload, $matches)) {
            return (int) ($matches[1] ?? 0);
        }

        if (preg_match('/^training_test:(\d+)$/', $payload, $matches)) {
            return (int) ($matches[1] ?? 0);
        }

        if (preg_match('/^training_group_attendance:(\d+)$/', $payload, $matches)) {
            return (int) ($matches[1] ?? 0);
        }

        throw ValidationException::withMessages([
            'attendance_barcode' => 'صيغة الباركود غير صحيحة لمسار التدريب.',
        ]);
    }

    public function resolveTestBeneficiaryOrderIdFromBarcode(string $barcode): int
    {
        $payload = trim($barcode);
        if (preg_match('/^training_test:(\d+)$/', $payload, $matches)) {
            return (int) ($matches[1] ?? 0);
        }

        throw ValidationException::withMessages([
            'test_attendance_barcode' => 'صيغة باركود الاختبار غير صحيحة.',
        ]);
    }

    public function markGroupAttendance(DynamicServiceOrder $dynamicServiceOrder, array $meta = []): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $groupAttendance = $workflowData['group_attendance'] ?? [];
        $scans = $groupAttendance['scans'] ?? [];

        $scans[] = array_merge([
            'scanned_at' => now()->toDateTimeString(),
            'scanned_by' => auth()->id(),
        ], $meta);

        $workflowData['group_attendance'] = [
            'attended' => true,
            'last_scanned_at' => now()->toDateTimeString(),
            'scans' => $scans,
        ];

        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);
    }

    public function submitBeneficiarySatisfaction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $data
    ): void {
        $validated = Validator::make($data, [
            'satisfaction_rating' => 'required|integer|min:1|max:5',
            'satisfaction_comment' => 'nullable|string|max:2000',
        ], [
            'satisfaction_rating.required' => 'يرجى اختيار تقييم الرضا',
        ])->validate();

        $workflowData = $dynamicServiceOrder->workflow_data ?? [];
        $test = $workflowData['test'] ?? [];

        if (! empty($test['satisfaction_completed'])) {
            throw ValidationException::withMessages([
                'satisfaction' => 'تم إكمال استبيان الرضا مسبقاً.',
            ]);
        }

        $workflowData['test'] = array_merge($test, [
            'satisfaction_completed' => true,
            'satisfaction_rating' => (int) $validated['satisfaction_rating'],
            'satisfaction_comment' => $validated['satisfaction_comment'] ?? null,
            'satisfaction_completed_at' => now()->toDateTimeString(),
        ]);

        $history = $workflowData['history'] ?? [];
        $history[] = [
            'step' => TrainingWorkflowHandler::STEP_TESTING,
            'action' => 'beneficiary_satisfaction',
            'at' => now()->toDateTimeString(),
            'by' => auth()->id(),
            'rating' => (int) $validated['satisfaction_rating'],
            'comment' => $validated['satisfaction_comment'] ?? null,
        ];
        $workflowData['history'] = $history;

        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);

        $this->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'شكراً لإكمال استبيان الرضا. سيتم متابعة إصدار الشهادة أو تسليم الجهاز حسب حالة طلبكم.'
        );
    }
}
