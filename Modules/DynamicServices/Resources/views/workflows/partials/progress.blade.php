@php
    $steps = $workflowContext['steps'] ?? [];
    $currentStep = $workflowContext['currentStep'] ?? null;
    $stepKeys = $workflowContext['stepKeys'] ?? array_keys($steps);
    $currentIndex = array_search($currentStep, $stepKeys, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
@endphp

<div class="mb-4">
    <h6 class="text-muted mb-3">مراحل سير العمل</h6>
    <div class="d-flex flex-wrap gap-2">
        @foreach ($stepKeys as $index => $stepKey)
            @php
                $isPast = $index < $currentIndex;
                $isCurrent = $stepKey === $currentStep;
                $badgeClass = $isCurrent ? 'bg-primary' : ($isPast ? 'bg-success' : 'bg-light text-muted');
            @endphp
            <span class="badge {{ $badgeClass }} px-3 py-2">
                {{ $index + 1 }}. {{ $steps[$stepKey] ?? $stepKey }}
            </span>
        @endforeach
    </div>
</div>
