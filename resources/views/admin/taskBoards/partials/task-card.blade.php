
<div class="card custom-card" data-task-id="{{ $task->id }}" style="border: 1px dashed rgba(var(--{{ $taskStatus->badge_class }}-rgb), 0.5)">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="task-badges">
                <span class="badge badge-task bg-success-transparent"># {{ $task->id }}</span>
                @foreach ($task->tags as $tag)
                    <span class="{{ $tag->badge_class }}">{{ $tag->name }}</span>
                @endforeach 
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-light" type="button" id="dropdownMenuButton1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ri-more-2-fill"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="{{ route('admin.tasks.show', $task->id) }}">{{ trans('global.view') }}</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="editTask({{ $task->id }})">{{ trans('global.edit') }}</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="deleteRecord('{{ route('admin.tasks.destroy', $task->id) }}', {{ $task->id }})">{{ trans('global.delete') }}</a></li>
                </ul>
            </div>
        </div>
        <h6 class="fw-medium mb-1 fs-15">{{ $task->name }}</h6>
        <p class="kanban-task-description">{{ $task->short_description }}</p>
        @if($task->task_priority)
            <div class="kanban-card-footer">
                <span class="{{ $task->task_priority->badge_class }}">{{ $task->task_priority->name }}</span> 
            </div>
        @endif 
    </div>
    <div class="p-3 border-top border-block-start-dashed">
        <div class="d-flex align-items-center flex-row-reverse justify-content-between">
            <div class="avatar-list-stacked">
                @foreach ($task->assigned_tos as $assigned_to)
                    @include('utilities.user-avatar', ['user' => $assigned_to])
                @endforeach
            </div> 
        </div>
    </div>
</div> 