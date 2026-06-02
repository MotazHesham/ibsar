@if (!empty($workflowContext))
    @php
        $service = $workflowContext['service'];
        $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
        $availableActions = $workflowContext['availableActions'] ?? [];
        $currentStep = $workflowContext['currentStep'];
        $isFinancial = $workflowContext['isFinancial'] ?? false;
        $categoryLabel =
            \Modules\DynamicServices\Models\DynamicService::CATEGORIES[$service->category] ?? $service->category;
        $assistanceTypeLabel = $isFinancial ? 'مساعدة مالية' : 'استلام عيني';

        $availableStockItems = $workflowContext['availableStockItems'] ?? collect();
        $researcherReview = $workflowContext['researcherReview'] ?? [];
        $projectsReview = $workflowContext['projectsReview'] ?? [];
        $financialReview = $workflowContext['financialReview'] ?? [];
        $allocation = $workflowContext['allocation'] ?? [];
        $pickup = $workflowContext['pickup'] ?? [];
        $incompleteDocs = $workflowContext['incompleteDocs'] ?? [];
    @endphp

    <div class="alert alert-info py-2 mb-3">
        <small>
            <strong>مساعدات — {{ $assistanceTypeLabel }}</strong>
        </small>
    </div>

    @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-muted">المرحلة الحالية</span>
        <span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span>
    </div>

    @if (($dynamicServiceOrder->workflow_data['status'] ?? '') === 'incomplete')
        <div class="alert alert-warning">
            بانتظار استكمال المستفيد للوثائق الناقصة.
            @if (!empty($incompleteDocs['message']))
                <div class="mt-2"><strong>المطلوب:</strong> {{ $incompleteDocs['message'] }}</div>
            @endif
        </div>
    @endif

    @if ($workflowContext['isRejected'])
        <div class="alert alert-danger">
            تم رفض الطلب
            @if ($beneficiaryOrder->refused_reason)
                <div class="mt-2"><strong>السبب:</strong> {{ $beneficiaryOrder->refused_reason }}</div>
            @endif
        </div>
    @elseif ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
        @include('dynamicservices::workflows.assistance.partials.completed')
    @elseif (count($availableActions) > 0)
        <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" method="POST"
            id="assistance-workflow-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="dynamic_workflow" value="1">

            @if ($currentStep === 'first_approval')
                @include('dynamicservices::workflows.assistance.steps.social-researcher')
            @elseif ($currentStep === 'second_approval')
                @if ($isFinancial)
                    @include('dynamicservices::workflows.assistance.steps.projects-financial')
                @else
                    @include('dynamicservices::workflows.assistance.steps.projects-in-kind')
                @endif
            @elseif ($currentStep === 'third_approval' && $isFinancial)
                @include('dynamicservices::workflows.assistance.steps.finance-disbursement')
            @elseif ($currentStep === 'receive_order')
                @include('dynamicservices::workflows.assistance.steps.receive-order')
            @endif

            @include('utilities.form.textarea', [
                'name' => 'note',
                'label' => 'cruds.beneficiaryOrder.fields.note',
                'isRequired' => false,
                'value' => $beneficiaryOrder->note,
            ])

            <div id="workflow-reject-reason-wrapper" class="mb-3" style="display: none;">
                @include('utilities.form.textarea', [
                    'name' => 'reason',
                    'label' => 'cruds.beneficiaryOrder.fields.refused_reason',
                    'isRequired' => false,
                    'value' => $beneficiaryOrder->refused_reason,
                ])
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach ($availableActions as $action)
                    @php
                        $btnClass = match ($action['type'] ?? 'primary') {
                            'success' => 'btn-success',
                            'danger' => 'btn-danger',
                            'warning' => 'btn-warning',
                            'secondary' => 'btn-secondary',
                            default => 'btn-primary',
                        };
                    @endphp
                    <button type="submit" name="workflow_action" value="{{ $action['key'] }}"
                        class="btn {{ $btnClass }}"
                        @if (!empty($action['requires_reason'])) data-requires-reason="1" @endif>
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </form>
    @endif

    @include('dynamicservices::workflows.assistance.partials.history', [
        'dynamicServiceOrder' => $dynamicServiceOrder,
        'workflowContext' => $workflowContext,
    ])
@endif

@section('scripts')
    @parent
    <script>
        document.querySelectorAll('#assistance-workflow-form [data-requires-reason="1"]').forEach(function(button) {
            button.addEventListener('click', function() {
                var wrapper = document.getElementById('workflow-reject-reason-wrapper');
                if (wrapper) wrapper.style.display = 'block';
            });
        });

        var requiresTraining = document.getElementById('requires_training');
        var trainingTypeWrapper = document.getElementById('training_type_wrapper');
        if (requiresTraining && trainingTypeWrapper) {
            function toggleTrainingType() {
                trainingTypeWrapper.style.display = requiresTraining.value === 'yes' ? 'block' : 'none';
            }
            requiresTraining.addEventListener('change', toggleTrainingType);
            toggleTrainingType();
        }
    </script>
@endsection
