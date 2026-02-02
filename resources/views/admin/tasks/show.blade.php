@extends('layouts.master')
@section('content')

    @php
        $breadcrumbs = [
            ['title' => trans('cruds.task.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.task.title'),
                'url' => route('admin.tasks.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.task.title_singular')],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        <div class="col-xl-6">
            <div class="card custom-card">
                <div class="card-header justify-content-between align-items-center">
                    <div class="card-title">{{ trans('global.show') }} {{ trans('cruds.task.title_singular') }} #
                        {{ $task->id }}</div>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 mb-4 align-items-center">
                        <div class="fs-15 fw-medium">{{ trans('cruds.task.fields.name') }} :</div>
                        <h5 class="fw-medium mb-0">
                            {{ $task->name }} <span class="badge bg-success-transparent fs-10 fw-medium">
                                {{ trans('cruds.task.fields.created_at') }} :
                                {{ $task->created_at->format('d-m-Y h:i A') }} </span>
                        </h5>
                    </div>
                    <div class="fs-15 fw-medium mb-2">{{ trans('cruds.task.fields.short_description') }} :</div>
                    <p class="text-muted mb-4">{{ $task->short_description }}</p>
                    <div class="row gy-3 mb-4">
                        <div class="col-xl-12">
                            <div class="fs-15 fw-medium mb-2">{{ trans('cruds.task.fields.description') }} :</div>
                            {!! $task->description !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card custom-card">
                <div class="card-header justify-content-between">
                    <ul class="nav nav-tabs tab-style-7 scaleX profile-settings-tab" id="myTab4" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link border border-dashed rounded-bottom-0 px-3 active" id="task-activity"
                                data-bs-toggle="tab" data-bs-target="#task-activity-pane" type="button" role="tab"
                                aria-controls="task-activity-pane" aria-selected="true">{{ trans('cruds.task.show.activity') }}</button>
                        </li> 
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <div class="tab-pane show active overflow-hidden p-0 border-0" id="task-activity-pane" role="tabpanel"
                        aria-labelledby="task-activity" tabindex="0">
                        <ul class="list-unstyled profile-timeline" id="activity-timeline" style="max-height: 35rem;">
                            @include('partials.activity', compact('activityLogs'))
                        </ul>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card custom-card overflow-hidden">
                <div class="card-header">
                    <div class="card-title">
                        {{ trans('cruds.task.fields.attachment') }}
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @if ($task->attachment)
                            @foreach ($task->attachment as $attachment)
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <div class="lh-1">
                                            <span class="avatar avatar-rounded p-2 bg-light">
                                                @if ($attachment->type == 'image')
                                                    <img src="{{ $attachment->getUrl('thumb') }}" alt="">
                                                @else
                                                    <div class="avatar avatar-sm text-primary">
                                                        <i class="ti ti-file-description fs-24"></i>
                                                    </div>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <a href="{{ $attachment->getUrl() }}" target="_blank"><span
                                                    class="d-block fw-medium">{{ $attachment->name }}</span></a>
                                            <span
                                                class="d-block text-muted fs-12 fw-normal">{{ formatFileSize($attachment->size) }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="flex-fill">
                                        <span
                                            class="d-block text-muted fs-12 fw-normal">{{ trans('global.no_data_found') }}</span>
                                    </div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="card custom-card overflow-hidden">
                <div class="card-header">
                    <div class="card-title">
                        {{ trans('cruds.task.show.information') }}
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap">
                            <tbody>
                                <tr>
                                    <td><span class="fw-medium">{{ trans('cruds.task.fields.id') }} :</span></td>
                                    <td># {{ $task->id }}</td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">{{ trans('cruds.task.fields.tag') }} :</span></td>
                                    <td>
                                        @foreach ($task->tags as $tag)
                                            <span class="{{ $tag->badge_class }}">{{ $tag->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">{{ trans('cruds.task.fields.task_board') }} :</span></td>
                                    <td>
                                        {{ $task->task_board->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ trans('cruds.task.fields.assigned_by') }} :</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2 lh-1">
                                                @include('utilities.user-avatar', [
                                                    'user' => $task->assigned_by,
                                                ])
                                            </div>
                                            <span class="d-block fs-14 fw-medium">{{ $task->assigned_by->name }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">{{ trans('cruds.task.fields.status') }} :</span></td>
                                    <td>
                                        <span
                                            class="fw-medium text-{{ $task->status->badge_class ?? '' }}">{{ $task->status->name ?? '' }}</span>
                                    </td>
                                </tr>
                                @if ($task->task_priority)
                                    <tr>
                                        <td><span class="fw-medium">{{ trans('cruds.task.fields.task_priority') }}
                                                :</span></td>
                                        <td>
                                            <span
                                                class="{{ $task->task_priority->badge_class }}">{{ $task->task_priority->name }}</span>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><span class="fw-medium">{{ trans('cruds.task.fields.due_date') }} :</span></td>
                                    <td>
                                        {{ $task->due_date }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">{{ trans('cruds.task.fields.assigned_to') }} :</span></td>
                                    <td>
                                        <div class="avatar-list-stacked">
                                            @foreach ($task->assigned_tos as $assigned_to)
                                                @include('utilities.user-avatar', ['user' => $assigned_to])
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="fw-medium">{{ trans('cruds.task.fields.updated_at') }} :</span></td>
                                    <td>
                                        <span class="text-success fw-medium">{{ $task->updated_at->format('d-m-Y h:i A') }}</span> 
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    new SimpleBar(document.getElementById('activity-timeline'), {
        autoHide: true
    }); 
    // State management object
    const state = {
        currentPage: 1,
        isLoading: false,
        hasMorePages:'{{ $activityLogs->hasMorePages()  ? true : false }}'
    };
        
    function loadMoreActivities(timeline, observer, loadingIndicator,) { 
        if (state.isLoading || !state.hasMorePages) return; 
        const taskId = '{{ $task->id }}';
        
        state.isLoading = true;
        loadingIndicator.style.display = 'block';
        state.currentPage++;

        $.ajax({
            url: `/admin/tasks/${taskId}`,
            type: 'GET',
            data: {
                page: state.currentPage
            },
            success: function(response) { 
                $('#activity-timeline .simplebar-content').append(response.html);
                loadingIndicator.style.display = 'none';

                if (!response.hasMorePages) {
                    // No more content
                    state.hasMorePages = false;
                    loadingIndicator.style.display = 'none';
                    // Unobserve the last item since we won't need to load more
                    const items = timeline.querySelectorAll('li');
                    if (items.length > 0) {
                        observer.unobserve(items[items.length - 1]);
                    }
                }else{ 
                    observeLastItem(observer);
                }
                    
                state.isLoading = false;
            },
            error: function(xhr, status, error) {
                console.error('Error loading more activities:', error);
                loadingIndicator.style.display = 'none';
                state.isLoading = false;
            }
        });
    } 
    </script> 
@endsection