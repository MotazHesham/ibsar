@if (!empty($workflowContext))
    @php
        $service = $workflowContext['service'];
        $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
        $availableActions = $workflowContext['availableActions'] ?? [];
        $categoryLabel =
            \Modules\DynamicServices\Models\DynamicService::CATEGORIES[$service->category] ?? $service->category;
    @endphp

    <div class="alert alert-info py-2 mb-3">
        <small>
            <strong>نوع الخدمة:</strong> {{ $categoryLabel }}
            @if ($service->service_type)
                — <strong>التصنيف الفرعي:</strong> {{ $service->service_type }}
            @endif
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
        <div class="text-center p-4">
            <span class="avatar avatar-xl avatar-rounded bg-success-transparent svg-success">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                    <rect width="256" height="256" fill="none" />
                    <circle cx="128" cy="128" r="96" opacity="0.2" />
                    <polyline points="88 136 112 160 168 104" fill="none" stroke="currentColor"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                    <circle cx="128" cy="128" r="96" fill="none" stroke="currentColor"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                </svg>
            </span>
            <h3 class="mt-2">تم إنهاء الطلب بنجاح</h3>
        </div>
    @elseif (count($availableActions) > 0)
        <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" method="POST"
            id="dynamic-workflow-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="dynamic_workflow" value="1">

            <div class="row">
                @include('utilities.form.textarea', [
                    'name' => 'note',
                    'label' => 'cruds.beneficiaryOrder.fields.note',
                    'isRequired' => false,
                    'value' => $beneficiaryOrder->note,
                ])
            </div>

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

    @include('dynamicservices::workflows.partials.history', [
        'dynamicServiceOrder' => $dynamicServiceOrder,
        'workflowContext' => $workflowContext,
    ])
@endif

@section('scripts')
    @parent
    <script>
        document.querySelectorAll('#dynamic-workflow-form [data-requires-reason="1"]').forEach(function(button) {
            button.addEventListener('click', function(event) {
                var wrapper = document.getElementById('workflow-reject-reason-wrapper');
                if (wrapper) {
                    wrapper.style.display = 'block';
                }
            });
        });
    </script>
@endsection
