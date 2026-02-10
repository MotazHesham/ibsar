@if (count($volunteerTasks) > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{{ trans('cruds.volunteerTask.fields.id') }}</th>
                    <th>{{ trans('cruds.volunteerTask.fields.name') }}</th>
                    <th>{{ trans('cruds.volunteerTask.fields.visit_type') }}</th>
                    <th>{{ trans('cruds.volunteerTask.fields.date') }}</th>
                    <th>{{ trans('cruds.volunteerTask.fields.status') }}</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($volunteerTasks as $volunteerTask)
                    <tr>
                        <td>{{ $volunteerTask->id }}</td>
                        <td>{{ $volunteerTask->name }}</td>
                        <td>{{ \App\Models\VolunteerTask::VISIT_TYPE_SELECT[$volunteerTask->visit_type] ?? $volunteerTask->visit_type }}</td>
                        <td>{{ $volunteerTask->date ? $volunteerTask->date->format('Y-m-d') : '-' }}</td>
                        <td><span class="badge bg-{{ $volunteerTask->status === 'completed' ? 'success' : ($volunteerTask->status === 'cancelled' ? 'danger' : 'secondary') }}">{{ \App\Models\VolunteerTask::STATUS_SELECT[$volunteerTask->status] ?? $volunteerTask->status }}</span></td>
                        <td>
                            @can('volunteer_task_show')
                                <a class="btn btn-sm btn-primary-light" href="{{ route('admin.volunteer-tasks.show', $volunteerTask->id) }}">
                                    <i class="ri-eye-line"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted mb-0">{{ trans('global.no_entries_in_table') }}</p>
@endif
