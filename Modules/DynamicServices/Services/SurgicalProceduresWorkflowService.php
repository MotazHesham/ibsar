<?php

namespace Modules\DynamicServices\Services;

use App\Models\BeneficiaryOrder;
use App\Models\UserAlert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Workflows\SurgicalProceduresWorkflowHandler;

class SurgicalProceduresWorkflowService
{
    public const CLOSE_REASONS = [
        'external_surgery' => 'أجرى العملية بدعم خارجي',
        'systemic_condition' => 'وضعه النظامي',
        'health_condition' => 'وضعه الصحي',
        'deceased' => 'متوفى',
        'partner_referral' => 'تحويل لجهة شريكة',
        'other' => 'سبب آخر',
    ];

    public function getViewData(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        return [
            'waitingList' => $workflowData['waiting_list'] ?? [],
            'transfer' => $workflowData['transfer'] ?? [],
            'clinic' => $workflowData['clinic'] ?? [],
            'contribution' => $workflowData['contribution'] ?? [],
            'financial' => $workflowData['financial'] ?? [],
            'operation' => $workflowData['operation'] ?? [],
            'closeReasons' => self::CLOSE_REASONS,
        ];
    }

    public function validateAndExtract(string $step, string $action, array $data): array
    {
        return match (true) {
            $step === SurgicalProceduresWorkflowHandler::STEP_WAITING_LIST && $action === SurgicalProceduresWorkflowHandler::ACTION_TRANSFER_CLINIC
                => $this->validateTransfer($data),
            $step === SurgicalProceduresWorkflowHandler::STEP_WAITING_LIST && $action === SurgicalProceduresWorkflowHandler::ACTION_CLOSE_CASE
                => $this->validateCloseCase($data),
            $step === SurgicalProceduresWorkflowHandler::STEP_CLINIC_ACCOUNT && $action === SurgicalProceduresWorkflowHandler::ACTION_SUBMIT_CLINIC_REPORT
                => $this->validateClinicReport($data),
            $step === SurgicalProceduresWorkflowHandler::STEP_CLINIC_ACCOUNT && $action === SurgicalProceduresWorkflowHandler::ACTION_SUBMIT_CONTRIBUTION
                => $this->validateContribution($data),
            $step === SurgicalProceduresWorkflowHandler::STEP_FINANCIAL_APPROVAL && $action === SurgicalProceduresWorkflowHandler::ACTION_APPROVE_FINANCIAL
                => $this->validateFinancialApproval($data),
            $step === SurgicalProceduresWorkflowHandler::STEP_PERFORM_OPERATION && $action === SurgicalProceduresWorkflowHandler::ACTION_SUBMIT_OPERATION
                => $this->validateOperation($data),
            default => $data,
        };
    }

    protected function validateTransfer(array $data): array
    {
        $validated = Validator::make($data, [
            'clinic_name' => 'required|string|max:255',
            'clinic_id' => 'nullable|string|max:50',
        ])->validate();

        return ['transfer' => array_merge($validated, ['transferred_at' => now()->toDateTimeString()])];
    }

    protected function validateCloseCase(array $data): array
    {
        $validated = Validator::make($data, [
            'close_reason' => 'required|in:' . implode(',', array_keys(self::CLOSE_REASONS)),
            'close_reason_other' => 'nullable|string|max:500',
        ])->validate();

        return ['close_case' => array_merge($validated, ['closed_at' => now()->toDateTimeString()])];
    }

    protected function validateClinicReport(array $data): array
    {
        $validated = Validator::make($data, [
            'operation_type' => 'required|string|max:255',
            'operation_name' => 'required|string|max:255',
            'operation_price' => 'required|numeric|min:0',
            'cost_report_notes' => 'nullable|string|max:2000',
        ])->validate();

        return ['clinic' => array_merge($validated, ['submitted_at' => now()->toDateTimeString()])];
    }

    protected function validateContribution(array $data): array
    {
        $validated = Validator::make($data, [
            'contribution_amount' => 'required|numeric|min:0',
            'contribution_notes' => 'nullable|string|max:2000',
        ])->validate();

        return ['contribution' => array_merge($validated, ['submitted_at' => now()->toDateTimeString()])];
    }

    protected function validateFinancialApproval(array $data): array
    {
        return ['financial' => [
            'approved' => true,
            'approved_at' => now()->toDateTimeString(),
            'notes' => $data['finance_notes'] ?? null,
        ]];
    }

    protected function validateOperation(array $data): array
    {
        $validated = Validator::make($data, [
            'operation_date' => 'required|date_format:' . config('panel.date_format'),
            'operation_summary' => 'required|string|max:2000',
            'invoice_reference' => 'nullable|string|max:255',
        ])->validate();

        return ['operation' => array_merge($validated, ['performed_at' => now()->toDateTimeString()])];
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

    public function submitBeneficiaryReceipt(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $data
    ): void {
        $validated = Validator::make($data, [
            'receipt_notes' => 'nullable|string|max:2000',
        ])->validate();

        $this->mergeWorkflowData($dynamicServiceOrder, [
            'financial' => array_merge($dynamicServiceOrder->workflow_data['financial'] ?? [], [
                'receipt_submitted_at' => now()->toDateTimeString(),
                'receipt_notes' => $validated['receipt_notes'] ?? null,
            ]),
        ]);

        $history = $dynamicServiceOrder->workflow_data['history'] ?? [];
        $history[] = [
            'step' => SurgicalProceduresWorkflowHandler::STEP_CLINIC_ACCOUNT,
            'action' => 'beneficiary_receipt',
            'at' => now()->toDateTimeString(),
            'by' => auth()->id(),
        ];
        $workflowData = $dynamicServiceOrder->workflow_data;
        $workflowData['history'] = $history;
        $dynamicServiceOrder->update(['workflow_data' => $workflowData]);

        $this->notifyBeneficiaryUserAlert($beneficiaryOrder, 'تم استلام إيصال السداد. جاري مراجعته من المالية.');
    }
}
