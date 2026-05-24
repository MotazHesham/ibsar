@if (!empty($workflowContext))
    @php
        $service = $workflowContext['service'];
        $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
        $availableActions = $workflowContext['availableActions'] ?? [];
        $currentStep = $workflowContext['currentStep'];
        $categoryLabel =
            \Modules\DynamicServices\Models\DynamicService::CATEGORIES[$service->category] ?? $service->category;
        $isGroup = $service->service_type === 'group';
    @endphp

    <div class="alert alert-info py-2 mb-3">
        <small>
            <strong>{{ $isGroup ? 'التدريب الجماعي' : 'التدريب الفردي' }}</strong>
            — {{ $categoryLabel }}
        </small>
    </div>

    @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-muted">المرحلة الحالية</span>
        <span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span>
    </div>

    @if ($workflowContext['isRejected'])
        <div class="alert alert-danger">
            تم رفض الطلب
            @if ($beneficiaryOrder->refused_reason)
                <div class="mt-2"><strong>السبب:</strong> {{ $beneficiaryOrder->refused_reason }}</div>
            @endif
        </div>
    @elseif ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
        @include('dynamicservices::workflows.training.partials.completed')
    @elseif (count($availableActions) > 0)
        <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" method="POST"
            id="training-workflow-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="dynamic_workflow" value="1">

            @if (!$isGroup)
                @includeWhen($currentStep === 'evaluation_scheduling', 'dynamicservices::workflows.training.steps.individual.evaluation-scheduling')
                @includeWhen($currentStep === 'evaluation', 'dynamicservices::workflows.training.steps.individual.evaluation-form')
                @includeWhen($currentStep === 'financial_approval', 'dynamicservices::workflows.training.steps.individual.financial-approval')
                @includeWhen($currentStep === 'donation_allocation', 'dynamicservices::workflows.training.steps.individual.donation-allocation')
                @includeWhen($currentStep === 'session_scheduling', 'dynamicservices::workflows.training.steps.individual.session-scheduling')
                @includeWhen($currentStep === 'testing', 'dynamicservices::workflows.training.steps.shared.testing-form')
            @else
                @includeWhen($currentStep === 'send_meeting_schedule', 'dynamicservices::workflows.training.steps.group.meeting-schedule')
                @includeWhen($currentStep === 'start_program', 'dynamicservices::workflows.training.steps.group.start-program')
                @includeWhen($currentStep === 'testing', 'dynamicservices::workflows.training.steps.shared.testing-form')
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
                        @if (!empty($action['requires_reason'])) data-requires-reason="1" @endif
                        @if (!empty($action['session_number'])) onclick="document.getElementById('session_number_input').value='{{ $action['session_number'] }}'" @endif>
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </form>
    @endif

    @include('dynamicservices::workflows.training.partials.history', [
        'dynamicServiceOrder' => $dynamicServiceOrder,
    ])
@endif

@section('scripts')
    @parent
    <script>
        document.querySelectorAll('#training-workflow-form [data-requires-reason="1"]').forEach(function(button) {
            button.addEventListener('click', function() {
                var wrapper = document.getElementById('workflow-reject-reason-wrapper');
                if (wrapper) wrapper.style.display = 'block';
            });
        });

        var visualStatus = document.getElementById('visual_status');
        var otherWrapper = document.getElementById('visual_status_other_wrapper');
        if (visualStatus && otherWrapper) {
            function toggleVisualOther() {
                otherWrapper.style.display = visualStatus.value === 'other' ? 'block' : 'none';
            }
            visualStatus.addEventListener('change', toggleVisualOther);
            toggleVisualOther();
        }
    </script>
@endsection
