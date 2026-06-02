@php
    use Modules\DynamicServices\Helpers\AssistanceWorkflowHistoryHelper;

    $steps = $workflowContext['steps'] ?? [];
    $historyEntries = $dynamicServiceOrder->workflow_data['history'] ?? [];
@endphp

@if (!empty($historyEntries))
    <details class="mt-4">
        <summary class="text-muted fw-medium" style="cursor: pointer;">سجل المراحل</summary>
        <ul class="list-group list-group-flush mt-2">
            @foreach (array_reverse($historyEntries) as $entry)
                @php
                    $details = AssistanceWorkflowHistoryHelper::formatDetails($entry);
                @endphp
                <li class="list-group-item px-0 py-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
                        <span class="badge bg-primary-transparent">
                            {{ AssistanceWorkflowHistoryHelper::stepLabel($steps, $entry['step'] ?? '') }}
                        </span>
                        <span class="text-muted small">{{ $entry['at'] ?? '' }}</span>
                    </div>
                    <div class="fw-medium">{{ AssistanceWorkflowHistoryHelper::actionLabel($entry['action'] ?? '') }}</div>
                    <div class="text-muted small mt-1">
                        بواسطة: {{ AssistanceWorkflowHistoryHelper::actorName($entry['by'] ?? null) }}
                    </div>
                    @if (!empty($details))
                        <ul class="small text-muted mb-0 mt-2 ps-3">
                            @foreach ($details as $detail)
                                <li>{{ $detail }}</li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </details>
@endif
