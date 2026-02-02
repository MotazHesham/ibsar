@forelse($activityLogs as $log)
    @php
        $attributes = [];
        if (isset($log->properties['attributes'])) {
            $attributes = $log->properties['attributes'];
        } elseif (isset($log->properties['old'])) {
            $attributes = $log->properties['old'];
        }
    @endphp
    <li>
        <div>
            <span class="avatar avatar-sm bg-primary-transparent avatar-rounded profile-timeline-avatar">
                @if($log->causer)
                    @include('utilities.user-avatar', ['user' => $log->causer])
                @else
                    <i class="ri-user-line"></i>
                @endif
            </span>
            <div class="d-flex gap-2 flex-wrap mb-2">
                <p class="mb-0">
                    {!! $log->description !!}
                </p>
                <span class="text-end ms-auto fs-11 text-muted">{{ $log->created_at->format('d,M Y - H:i') }}</span>
            </div>
            <div class="profile-activity-media text-muted mb-2">
                <div class='row'>
                    @foreach ($attributes as $key => $value)
                        @php
                            $transKey = 'cruds.' . lcfirst(class_basename($log->subject_type)) . '.fields.' . $key;
                            $label = trans()->has($transKey) ? trans($transKey) : $key;
                        @endphp
                        @if (!is_array($value) && $value)
                            <div class='col-md-3 mb-2'>
                                <strong>{{ $label }}:</strong> {!! $value !!}
                            </div>
                        @else
                            @if (isset($value['name']))
                                @if (!is_array($value['name']) && $value['name'])
                                    <div class='col-md-3 mb-2'>
                                        <strong>{{ $label }}:</strong> {!! $value['name'] !!}
                                    </div>
                                @elseif(isset($value['name'][app()->getLocale()]))
                                    <div class='col-md-3 mb-2'>
                                        <strong>{{ $label }}:</strong> {!! $value['name'][app()->getLocale()] !!}
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </li>
@empty
    <li>
        <div class="text-center text-muted">
            <p>No activity logs found</p>
        </div>
    </li>
@endforelse 

@section('scripts')
@parent
<script>
    const timeline = document.getElementById('activity-timeline');
    document.addEventListener('DOMContentLoaded', function() {
        // Create loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'text-center py-3';
        loadingIndicator.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
        loadingIndicator.style.display = 'none';
        timeline.after(loadingIndicator); 

        // Create intersection observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !state.isLoading && state.hasMorePages) { 
                    loadMoreActivities(timeline, observer, loadingIndicator);
                }
            });
        }, {
            root: timeline,
            threshold: 0.5
        });
        // Initial observation
        observeLastItem(observer); 
    });

    // Observe the last activity item
    function observeLastItem(observer) {
        const items = timeline.querySelectorAll('li'); 
        if (items.length > 0) { 
            // Unobserve previous last item if it exists
            const previousLastItem = items[items.length - 2];
            if (previousLastItem) {
                observer.unobserve(previousLastItem);
            }
            // Observe new last item
            observer.observe(items[items.length - 1]);
        }
    }

</script>
@endsection 
