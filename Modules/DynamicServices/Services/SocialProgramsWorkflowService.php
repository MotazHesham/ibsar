<?php

namespace Modules\DynamicServices\Services;

use App\Models\BeneficiaryOrder;
use App\Models\UserAlert;
use Illuminate\Support\Facades\Validator;
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
            ->whereHas('beneficiaryOrder', fn ($q) => $q->where('accept_status', 'yes'))
            ->where('workflow_step', '!=', 'rejected')
            ->count();
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
