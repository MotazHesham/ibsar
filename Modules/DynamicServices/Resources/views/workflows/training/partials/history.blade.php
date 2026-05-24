@if (!empty($dynamicServiceOrder->workflow_data['history']))
    <details class="mt-4">
        <summary class="text-muted" style="cursor: pointer;">سجل المراحل</summary>
        <ul class="list-group list-group-flush mt-2">
            @foreach (array_reverse($dynamicServiceOrder->workflow_data['history']) as $entry)
                <li class="list-group-item px-0 py-2 small">
                    <span class="text-muted">{{ $entry['at'] ?? '' }}</span>
                    — {{ $entry['action'] ?? '' }}
                    @if (!empty($entry['reason']))
                        ({{ $entry['reason'] }})
                    @endif
                </li>
            @endforeach
        </ul>
    </details>
@endif
