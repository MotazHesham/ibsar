<form method="POST" action="{{ route('admin.dynamic-service-workflows.transition', $workflow) }}"
    id="workflowTransitionForm">
    @csrf

    @php
        $category = $workflow->dynamicServiceOrder->dynamicService->category ?? null;
    @endphp

    @if($category === 'assistance')
        @include('admin.dynamic-service-workflows.partials.workflow-form-assistance')
    @elseif($category === 'social_programs')
        @include('admin.dynamic-service-workflows.partials.workflow-form-social-programs')
    @else
        @include('admin.dynamic-service-workflows.partials.workflow-form-training')
    @endif

    <div class="mb-3">
        <label class="form-label">ملاحظات:</label>
        <textarea name="notes" class="form-control" rows="3"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
</form>
