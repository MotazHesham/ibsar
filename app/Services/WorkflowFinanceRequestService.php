<?php

namespace App\Services;

use App\Models\BeneficiaryOrder;
use App\Models\WorkflowFinanceRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WorkflowFinanceRequestService
{
    public function queue(
        BeneficiaryOrder $beneficiaryOrder,
        string $workflowCategory,
        string $workflowStep,
        string $triggerAction,
        string $title,
        ?Model $source = null,
        ?Model $reference = null,
    ): WorkflowFinanceRequest {
        return WorkflowFinanceRequest::firstOrCreate(
            [
                'beneficiary_order_id' => $beneficiaryOrder->id,
                'workflow_category' => $workflowCategory,
                'trigger_action' => $triggerAction,
            ],
            [
                'workflow_step' => $workflowStep,
                'title' => $title,
                'source_type' => $source ? $source->getMorphClass() : null,
                'source_id' => $source?->getKey(),
                'reference_type' => $reference ? $reference->getMorphClass() : null,
                'reference_id' => $reference?->getKey(),
                'status' => WorkflowFinanceRequest::STATUS_UNPOSTED,
                'created_by' => Auth::id(),
            ]
        );
    }

    public function updateCost(WorkflowFinanceRequest $request, array $data): WorkflowFinanceRequest
    {
        if (! $request->isUnposted()) {
            throw ValidationException::withMessages([
                'amount' => 'لا يمكن تعديل طلب مُرحّل.',
            ]);
        }

        $request->update([
            'amount' => $data['amount'],
            'notes' => $data['notes'] ?? $request->notes,
        ]);

        return $request->fresh();
    }

    public function post(WorkflowFinanceRequest $request, array $data): WorkflowFinanceRequest
    {
        if (! $request->isUnposted()) {
            throw ValidationException::withMessages([
                'status' => 'تم ترحيل هذا الطلب مسبقاً.',
            ]);
        }

        if ($request->amount === null || (float) $request->amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'يرجى تحديد التكلفة قبل ترحيل القيد.',
            ]);
        }

        $request->update([
            'amount' => $data['amount'] ?? $request->amount,
            'notes' => $data['notes'] ?? $request->notes,
            'journal_reference' => $data['journal_reference'] ?? null,
            'status' => WorkflowFinanceRequest::STATUS_POSTED,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return $request->fresh();
    }
}
