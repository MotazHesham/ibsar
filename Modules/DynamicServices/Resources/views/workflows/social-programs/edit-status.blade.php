@if (!empty($workflowContext))
    @php
        $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
        $availableActions = $workflowContext['availableActions'] ?? [];
        $currentStep = $workflowContext['currentStep'];
        $targetCount = $workflowContext['targetCount'] ?? 0;
        $registeredCount = $workflowContext['registeredCount'] ?? 0;
        $programDetails = $workflowContext['programDetails'] ?? [];
    @endphp

    <div class="alert alert-info py-2 mb-3">
        <small><strong>برنامج اجتماعي</strong> — العدد المستهدف: {{ $targetCount ?: 'غير محدد' }} | المسجلون: {{ $registeredCount }}</small>
    </div>

    @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

    @if ($workflowContext['isRejected'])
        <div class="alert alert-danger">تم رفض الطلب</div>
    @elseif ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
        <div class="alert alert-success">تم إرسال تفاصيل البرنامج للمستفيدين</div>
        @if (!empty($programDetails['message']))
            <div class="card mt-2"><div class="card-body small">{{ $programDetails['message'] }}</div></div>
        @endif
    @elseif (count($availableActions) > 0)
        <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" method="POST" id="social-workflow-form">
            @csrf @method('PUT')
            <input type="hidden" name="dynamic_workflow" value="1">

            @if ($currentStep === 'send_program_details')
                <div class="card mb-3">
                    <div class="card-header py-2"><h6 class="mb-0">إرسال تفاصيل البرنامج</h6></div>
                    <div class="card-body">
                        @if ($targetCount > 0 && $registeredCount < $targetCount)
                            <div class="alert alert-warning">لم يكتمل العدد المستهدف بعد ({{ $registeredCount }}/{{ $targetCount }})</div>
                        @endif
                        @include('utilities.form.textarea', [
                            'name' => 'program_details_message',
                            'label' => 'تفاصيل البرنامج للمستفيدين',
                            'isRequired' => true,
                            'value' => $programDetails['message'] ?? '',
                        ])
                    </div>
                </div>
            @endif

            <div id="workflow-reject-reason-wrapper" class="mb-3" style="display:none;">
                @include('utilities.form.textarea', ['name' => 'reason', 'label' => 'سبب الرفض', 'isRequired' => false])
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($availableActions as $action)
                    <button type="submit" name="workflow_action" value="{{ $action['key'] }}"
                        class="btn btn-{{ $action['type'] ?? 'primary' }}"
                        @if (!empty($action['requires_reason'])) data-requires-reason="1" @endif>
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </form>
    @endif
@endif

@section('scripts') @parent
<script>
document.querySelectorAll('#social-workflow-form [data-requires-reason]').forEach(b => b.addEventListener('click', () => {
    document.getElementById('workflow-reject-reason-wrapper').style.display = 'block';
}));
</script>
@endsection
