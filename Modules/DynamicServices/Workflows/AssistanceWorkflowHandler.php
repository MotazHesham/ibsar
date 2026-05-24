<?php

namespace Modules\DynamicServices\Workflows;

use App\Models\BeneficiaryOrder;
use Illuminate\Validation\ValidationException;
use Modules\DynamicServices\Models\DynamicService;
use Modules\DynamicServices\Models\DynamicServiceOrder;
use Modules\DynamicServices\Services\AssistanceWorkflowService;

class AssistanceWorkflowHandler extends AbstractWorkflowHandler
{
    public const STEP_FIRST_APPROVAL = 'first_approval';
    public const STEP_SECOND_APPROVAL = 'second_approval';
    public const STEP_THIRD_APPROVAL = 'third_approval';
    public const STEP_RECEIVE_ORDER = 'receive_order';

    public const SUBTYPE_IN_KIND = 'in_kind';
    public const SUBTYPE_FINANCIAL = 'financial';

    public const ACTION_APPROVE_RESEARCHER = 'approve_researcher';
    public const ACTION_APPROVE_PROJECTS = 'approve_projects';
    public const ACTION_DISBURSE_FINANCE = 'disburse_finance';
    public const ACTION_VERIFY_OTP = 'verify_otp';
    public const ACTION_REQUEST_INCOMPLETE = 'request_incomplete';
    public const ACTION_RETURN_RESEARCHER = 'return_researcher';

    public function __construct(
        protected AssistanceWorkflowService $assistanceService
    ) {
    }

    public function category(): string
    {
        return DynamicService::CATEGORY_ASSISTANCE;
    }

    public function viewName(): string
    {
        return 'dynamicservices::workflows.assistance.edit-status';
    }

    public function steps(DynamicService $service): array
    {
        if ($this->isFinancial($service)) {
            return [
                self::STEP_FIRST_APPROVAL => 'اعتماد الباحث الاجتماعي',
                self::STEP_SECOND_APPROVAL => 'اعتماد قسم المشاريع',
                self::STEP_THIRD_APPROVAL => 'صرف المالية',
                self::STEP_COMPLETED => 'مكتمل',
            ];
        }

        return [
            self::STEP_FIRST_APPROVAL => 'اعتماد الباحث الاجتماعي',
            self::STEP_SECOND_APPROVAL => 'اعتماد قسم المشاريع',
            self::STEP_RECEIVE_ORDER => 'استلام الطلب',
            self::STEP_COMPLETED => 'مكتمل',
        ];
    }

    public function initialStep(DynamicService $service): string
    {
        return self::STEP_FIRST_APPROVAL;
    }

    public function availableActions(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service
    ): array {
        if ($this->isCompleted($dynamicServiceOrder) || $this->isRejected($dynamicServiceOrder)) {
            return [];
        }

        $workflowData = $dynamicServiceOrder->workflow_data ?? [];

        if (($workflowData['status'] ?? '') === 'incomplete') {
            return [];
        }

        return match ($dynamicServiceOrder->workflow_step) {
            self::STEP_FIRST_APPROVAL => $this->researcherActions($service),
            self::STEP_SECOND_APPROVAL => $this->projectsActions(),
            self::STEP_THIRD_APPROVAL => $this->isFinancial($service)
                ? [[
                    'key' => self::ACTION_DISBURSE_FINANCE,
                    'label' => 'تأكيد الصرف (المالية)',
                    'type' => 'success',
                    'form_submit' => true,
                ]]
                : [],
            self::STEP_RECEIVE_ORDER => $this->receiveOrderActions($workflowData),
            default => [],
        };
    }

    protected function researcherActions(DynamicService $service): array
    {
        $actions = [
            [
                'key' => self::ACTION_APPROVE_RESEARCHER,
                'label' => 'يعتمد (الباحث الاجتماعي)',
                'type' => 'success',
                'form_submit' => true,
            ],
            [
                'key' => self::ACTION_REJECT,
                'label' => 'لا يعتمد',
                'type' => 'danger',
                'requires_reason' => true,
            ],
        ];

        if ($this->isFinancial($service)) {
            $actions[] = [
                'key' => self::ACTION_REQUEST_INCOMPLETE,
                'label' => 'طلب استكمال الوثائق',
                'type' => 'warning',
                'form_submit' => true,
            ];
        }

        return $actions;
    }

    protected function projectsActions(): array
    {
        return [
            [
                'key' => self::ACTION_APPROVE_PROJECTS,
                'label' => 'يعتمد (المشاريع)',
                'type' => 'success',
                'form_submit' => true,
            ],
            [
                'key' => self::ACTION_RETURN_RESEARCHER,
                'label' => 'إعادة للباحث الاجتماعي',
                'type' => 'warning',
                'requires_reason' => true,
            ],
        ];
    }

    protected function receiveOrderActions(array $workflowData): array
    {
        if (empty($workflowData['pickup']['confirmed_at'])) {
            return [];
        }

        return [[
            'key' => self::ACTION_VERIFY_OTP,
            'label' => 'تأكيد الاستلام (OTP)',
            'type' => 'primary',
            'form_submit' => true,
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
        $validated = $this->assistanceService->validateAndExtract($step, $action, $data, $service);

        if ($action === self::ACTION_REJECT) {
            $this->handleReject($beneficiaryOrder, $dynamicServiceOrder, $step, $data);

            return;
        }

        if ($action === self::ACTION_RETURN_RESEARCHER) {
            $this->handleReturnToResearcher($dynamicServiceOrder, $data);

            return;
        }

        if ($this->isFinancial($service)) {
            $this->processFinancialAction($beneficiaryOrder, $dynamicServiceOrder, $service, $action, $step, $validated, $data);

            return;
        }

        $this->processInKindAction($beneficiaryOrder, $dynamicServiceOrder, $service, $action, $step, $validated, $data);
    }

    protected function handleReject(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        string $step,
        array $data
    ): void {
        $reason = $data['reason'] ?? null;

        if ($step === self::STEP_FIRST_APPROVAL) {
            $this->reject($beneficiaryOrder, $dynamicServiceOrder, $reason);
            $this->assistanceService->notifyBeneficiaryUserAlert(
                $beneficiaryOrder,
                'نعتذر، لم يتم قبول طلب المساعدة. ' . ($reason ? 'السبب: ' . $reason : '')
            );

            return;
        }

        $this->moveToStep($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, 0);
        $this->appendHistory($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, self::ACTION_RETURN_RESEARCHER, [
            'reason' => $reason,
        ]);
    }

    protected function handleReturnToResearcher(DynamicServiceOrder $dynamicServiceOrder, array $data): void
    {
        $this->moveToStep($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, 0);
        $this->appendHistory($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, self::ACTION_RETURN_RESEARCHER, [
            'reason' => $data['reason'] ?? null,
        ]);
    }

    protected function processInKindAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        string $step,
        array $validated,
        array $data
    ): void {
        match (true) {
            $step === self::STEP_FIRST_APPROVAL && $action === self::ACTION_APPROVE_RESEARCHER
                => $this->handleResearcherApproval($beneficiaryOrder, $dynamicServiceOrder, $validated),

            $step === self::STEP_SECOND_APPROVAL && $action === self::ACTION_APPROVE_PROJECTS
                => $this->handleProjectsApprovalInKind($beneficiaryOrder, $dynamicServiceOrder, $validated),

            $step === self::STEP_RECEIVE_ORDER && $action === self::ACTION_VERIFY_OTP
                => $this->handleOtpVerification($beneficiaryOrder, $dynamicServiceOrder, $data),

            default => null,
        };
    }

    protected function processFinancialAction(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        DynamicService $service,
        string $action,
        string $step,
        array $validated,
        array $data
    ): void {
        match (true) {
            $step === self::STEP_FIRST_APPROVAL && $action === self::ACTION_APPROVE_RESEARCHER
                => $this->handleResearcherApproval($beneficiaryOrder, $dynamicServiceOrder, $validated),

            $step === self::STEP_FIRST_APPROVAL && $action === self::ACTION_REQUEST_INCOMPLETE
                => $this->handleIncompleteDocsRequest($beneficiaryOrder, $dynamicServiceOrder, $validated),

            $step === self::STEP_SECOND_APPROVAL && $action === self::ACTION_APPROVE_PROJECTS
                => $this->handleProjectsApprovalFinancial($beneficiaryOrder, $dynamicServiceOrder, $validated),

            $step === self::STEP_THIRD_APPROVAL && $action === self::ACTION_DISBURSE_FINANCE
                => $this->handleFinanceDisbursement($beneficiaryOrder, $dynamicServiceOrder, $validated),

            default => null,
        };
    }

    protected function handleResearcherApproval(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $validated
    ): void {
        $this->assistanceService->mergeWorkflowData($dynamicServiceOrder, $validated);

        if ($beneficiaryOrder->accept_status !== 'yes') {
            $beneficiaryOrder->update(['accept_status' => 'yes']);
        }

        $this->moveToStep($dynamicServiceOrder, self::STEP_SECOND_APPROVAL, 1);
        $this->appendHistory($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, self::ACTION_APPROVE_RESEARCHER, array_merge(
            $validated['researcher'] ?? [],
            $validated['allocation'] ?? []
        ));

        $this->assistanceService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم اعتماد طلبكم من الباحث الاجتماعي. سيتم مراجعته من قسم المشاريع.'
        );
    }

    protected function handleIncompleteDocsRequest(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $validated
    ): void {
        $this->assistanceService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->appendHistory($dynamicServiceOrder, self::STEP_FIRST_APPROVAL, self::ACTION_REQUEST_INCOMPLETE, [
            'message' => $validated['incomplete_docs']['message'] ?? null,
        ]);

        $this->assistanceService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'يرجى استكمال الأمور الناقصة: ' . ($validated['incomplete_docs']['message'] ?? '')
        );
    }

    protected function handleProjectsApprovalInKind(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $validated
    ): void {
        $this->assistanceService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->moveToStep($dynamicServiceOrder, self::STEP_RECEIVE_ORDER, 2);
        $this->appendHistory($dynamicServiceOrder, self::STEP_SECOND_APPROVAL, self::ACTION_APPROVE_PROJECTS, $validated['projects'] ?? []);

        $message = 'تم اعتماد طلبكم من قسم المشاريع. يرجى تأكيد موعد الاستلام من صفحة الطلب.';
        if (! empty($validated['projects']['requires_training'])) {
            $message .= ' سيتم تحويلكم لمسار التدريب والتأهيل (' .
                ($validated['projects']['training_type'] === 'group' ? 'جماعي' : 'فردي') . ').';
        }

        $this->assistanceService->notifyBeneficiaryUserAlert($beneficiaryOrder, $message);
    }

    protected function handleProjectsApprovalFinancial(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $validated
    ): void {
        $this->assistanceService->mergeWorkflowData($dynamicServiceOrder, $validated);
        $this->moveToStep($dynamicServiceOrder, self::STEP_THIRD_APPROVAL, 2);
        $this->appendHistory($dynamicServiceOrder, self::STEP_SECOND_APPROVAL, self::ACTION_APPROVE_PROJECTS, array_merge(
            $validated['projects'] ?? [],
            $validated['financial'] ?? []
        ));

        $this->assistanceService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم اعتماد طلب المساعدة المالية. جاري إجراءات الصرف من قسم المالية.'
        );
    }

    protected function handleFinanceDisbursement(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $validated
    ): void {
        $this->assistanceService->mergeWorkflowData($dynamicServiceOrder, array_merge($validated, [
            'disbursed' => true,
            'disbursed_at' => now()->toDateTimeString(),
        ]));
        $this->moveToStep($dynamicServiceOrder, self::STEP_COMPLETED);
        $this->appendHistory($dynamicServiceOrder, self::STEP_THIRD_APPROVAL, self::ACTION_DISBURSE_FINANCE, $validated['financial'] ?? []);

        $amount = $dynamicServiceOrder->workflow_data['financial']['amount'] ?? '';
        $this->assistanceService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم صرف المساعدة المالية بمبلغ ' . $amount . '. يرجى إكمال استبيان الرضا من صفحة الطلب.'
        );
    }

    protected function handleOtpVerification(
        BeneficiaryOrder $beneficiaryOrder,
        DynamicServiceOrder $dynamicServiceOrder,
        array $data
    ): void {
        if (! $this->assistanceService->verifyOtp($dynamicServiceOrder, $data['otp_code'] ?? '')) {
            throw ValidationException::withMessages([
                'otp_code' => 'رمز OTP غير صحيح',
            ]);
        }

        $this->assistanceService->mergeWorkflowData($dynamicServiceOrder, [
            'otp' => array_merge($dynamicServiceOrder->workflow_data['otp'] ?? [], [
                'verified_at' => now()->toDateTimeString(),
            ]),
        ]);

        $this->assistanceService->deductInventory($dynamicServiceOrder);
        $this->moveToStep($dynamicServiceOrder, self::STEP_COMPLETED);
        $this->assistanceService->mergeWorkflowData($dynamicServiceOrder, [
            'delivered' => true,
            'delivered_at' => now()->toDateTimeString(),
        ]);
        $this->appendHistory($dynamicServiceOrder, self::STEP_RECEIVE_ORDER, self::ACTION_VERIFY_OTP, [
            'verified_at' => now()->toDateTimeString(),
        ]);

        $this->assistanceService->notifyBeneficiaryUserAlert(
            $beneficiaryOrder,
            'تم تأكيد استلام المساعدة بنجاح. يرجى إكمال استبيان تقييم الرضا من صفحة الطلب.'
        );
    }

    protected function isFinancial(DynamicService $service): bool
    {
        return $service->service_type === self::SUBTYPE_FINANCIAL;
    }
}
