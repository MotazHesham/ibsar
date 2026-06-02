<?php

namespace Modules\DynamicServices\Services;

use App\Models\BeneficiaryOrder;
use App\Models\UserAlert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Workflows\SocialProgramsWorkflowHandler;

class SocialProgramsWorkflowService
{
    public function getViewData(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        return [
            'targetCount' => $this->getTargetCount($service),
            'registeredCount' => $this->countRegistrations($service),
            'programDetails' => $workflowData['program_details'] ?? [],
            'projectsApproval' => $workflowData['projects'] ?? [],
        ];
    }

    public function getTargetCount(DynamicService $service): int
    {
        return max(0, (int) ($service->service_type ?: 0));
    }

    public function countRegistrations(DynamicService $service): int
    {
        return DynamicServiceOrder::query()
            ->where('dynamic_service_id', $service->id)
            ->whereIn('workflow_step', [
                SocialProgramsWorkflowHandler::STEP_SEND_PROGRAM_DETAILS,
                SocialProgramsWorkflowHandler::STEP_COMPLETED,
            ])
            ->count();
    }

    public function getProgramViewData(DynamicService $service): array
    {
        $targetCount = $this->getTargetCount($service);
        $registeredCount = $this->countRegistrations($service);

        $pendingApproval = DynamicServiceOrder::query()
            ->where('dynamic_service_id', $service->id)
            ->where('workflow_step', SocialProgramsWorkflowHandler::STEP_INITIAL_APPROVAL)
            ->with('beneficiaryOrder.beneficiary.user')
            ->orderBy('created_at')
            ->get();

        $waitingForDetails = DynamicServiceOrder::query()
            ->where('dynamic_service_id', $service->id)
            ->where('workflow_step', SocialProgramsWorkflowHandler::STEP_SEND_PROGRAM_DETAILS)
            ->count();

        $completedOrder = DynamicServiceOrder::query()
            ->where('dynamic_service_id', $service->id)
            ->where('workflow_step', SocialProgramsWorkflowHandler::STEP_COMPLETED)
            ->first();

        $programDetails = $completedOrder?->workflow_data['program_details'] ?? [];

        return [
            'targetCount' => $targetCount,
            'registeredCount' => $registeredCount,
            'pendingApproval' => $pendingApproval,
            'waitingForDetails' => $waitingForDetails,
            'canSendDetails' => $targetCount > 0 && $registeredCount >= $targetCount && $waitingForDetails > 0,
            'isProgramCompleted' => $waitingForDetails === 0 && $pendingApproval->isEmpty() && $completedOrder !== null,
            'programDetails' => $programDetails,
        ];
    }

    public function processProgramAction(DynamicService $service, string $action, array $data): void
    {
        if ($action === SocialProgramsWorkflowHandler::ACTION_SEND_DETAILS) {
            $this->sendProgramDetails($service, $data);

            return;
        }

        $beneficiaryOrderId = $data['beneficiary_order_id'] ?? null;
        if (! $beneficiaryOrderId) {
            throw ValidationException::withMessages([
                'beneficiary_order_id' => 'يجب تحديد طلب المستفيد',
            ]);
        }

        $beneficiaryOrder = BeneficiaryOrder::query()
            ->where('id', $beneficiaryOrderId)
            ->where('service_type', 'dynamic_' . $service->id)
            ->firstOrFail();

        app(DynamicOrderWorkflowService::class)->processAction($beneficiaryOrder, $action, $data);
    }

    public function sendProgramDetails(DynamicService $service, array $data): void
    {
        $targetCount = $this->getTargetCount($service);
        $registeredCount = $this->countRegistrations($service);

        if ($targetCount > 0 && $registeredCount < $targetCount) {
            throw ValidationException::withMessages([
                'program_details_message' => "لم يكتمل العدد المستهدف بعد ({$registeredCount}/{$targetCount})",
            ]);
        }

        $validated = $this->validateAndExtract(
            SocialProgramsWorkflowHandler::STEP_SEND_PROGRAM_DETAILS,
            SocialProgramsWorkflowHandler::ACTION_SEND_DETAILS,
            $data
        );

        app(SocialProgramsWorkflowHandler::class)->sendProgramDetails($service, $validated);
    }

    public function validateAndExtract(string $step, string $action, array $data): array
    {
        if ($step === SocialProgramsWorkflowHandler::STEP_SEND_PROGRAM_DETAILS && $action === SocialProgramsWorkflowHandler::ACTION_SEND_DETAILS) {
            $validated = Validator::make($data, [
                'program_details_message' => 'required|string|max:5000',
            ], [
                'program_details_message.required' => 'يرجى إدخال تفاصيل البرنامج',
            ])->validate();

            return [
                'program_details_message' => $validated['program_details_message'],
                'program_details' => [
                    'message' => $validated['program_details_message'],
                    'sent_at' => now()->toDateTimeString(),
                ],
            ];
        }

        return $data;
    }

    public function mergeWorkflowData(DynamicServiceOrder $dynamicServiceOrder, array $payload): void
    {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        foreach ($payload as $key => $value) {
            if (isset($workflowData[$key]) && is_array($workflowData[$key]) && is_array($value)) {
                $workflowData[$key] = array_merge($workflowData[$key], $value);
            } else {
                $workflowData[$key] = $value;
            }
        }

        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);
    }

    public function notifyBeneficiaryUserAlert(BeneficiaryOrder $beneficiaryOrder, string $text, ?string $link = null): void
    {
        $userId = $beneficiaryOrder->beneficiary?->user_id;
        if (! $userId) {
            return;
        }

        $alert = UserAlert::create([
            'alert_text' => $text,
            'alert_link' => $link ?? route('beneficiary.beneficiary-orders.show', $beneficiaryOrder),
            'user_type' => 'beneficiary',
        ]);
        $alert->users()->sync([$userId]);
    }

    public function notifyAllProgramBeneficiaries(DynamicService $service, string $text): void
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
}
