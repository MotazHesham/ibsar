@if (!empty($workflowContext))
    @php
        $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
        $availableActions = $workflowContext['availableActions'] ?? [];
        $currentStep = $workflowContext['currentStep'];
        $transfer = $workflowContext['transfer'] ?? [];
        $clinic = $workflowContext['clinic'] ?? [];
        $contribution = $workflowContext['contribution'] ?? [];
        $closeReasons = $workflowContext['closeReasons'] ?? [];
    @endphp

    @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

    @if (count($availableActions) > 0)
        <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="dynamic_workflow" value="1">

            @if ($currentStep === 'waiting_list')
                @include('utilities.form.text', ['name' => 'clinic_name', 'label' => 'اسم العيادة', 'isRequired' => true, 'grid' => 'col-md-6', 'value' => $transfer['clinic_name'] ?? ''])
                @include('utilities.form.select', ['name' => 'close_reason', 'label' => 'سبب الإغلاق', 'isRequired' => false, 'grid' => 'col-md-6', 'options' => $closeReasons, 'value' => ''])
            @endif

            @if ($currentStep === 'clinic_account' && empty($clinic['submitted_at']))
                @include('utilities.form.text', ['name' => 'operation_type', 'label' => 'نوع العملية', 'isRequired' => true, 'grid' => 'col-md-4', 'value' => $clinic['operation_type'] ?? ''])
                @include('utilities.form.text', ['name' => 'operation_name', 'label' => 'اسم العملية', 'isRequired' => true, 'grid' => 'col-md-4', 'value' => $clinic['operation_name'] ?? ''])
                @include('utilities.form.text', ['name' => 'operation_price', 'label' => 'السعر', 'isRequired' => true, 'grid' => 'col-md-4', 'value' => $clinic['operation_price'] ?? '', 'attributes' => 'type="number" step="0.01"'])
            @endif

            @if ($currentStep === 'clinic_account' && !empty($clinic['submitted_at']) && empty($contribution['submitted_at']))
                @include('utilities.form.text', ['name' => 'contribution_amount', 'label' => 'قيمة المساهمة', 'isRequired' => true, 'grid' => 'col-md-6', 'attributes' => 'type="number" step="0.01"'])
            @endif

            @if ($currentStep === 'perform_operation')
                @include('utilities.form.date', ['id' => 'operation_date', 'name' => 'operation_date', 'label' => 'تاريخ العملية', 'isRequired' => true, 'grid' => 'col-md-6', 'helperBlock' => ''])
                @include('utilities.form.textarea', ['name' => 'operation_summary', 'label' => 'تقرير مختصر', 'isRequired' => true])
            @endif

            <div id="workflow-reject-reason-wrapper" class="mb-3" style="display:none;">
                @include('utilities.form.textarea', ['name' => 'reason', 'label' => 'السبب', 'isRequired' => false])
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach ($availableActions as $action)
                    <button type="submit" name="workflow_action" value="{{ $action['key'] }}" class="btn btn-{{ $action['type'] ?? 'primary' }}"
                        @if (!empty($action['requires_reason'])) data-requires-reason="1" @endif>{{ $action['label'] }}</button>
                @endforeach
            </div>
        </form>
    @endif
@endif
