@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteerTask.title'), 'url' => route('admin.volunteer-tasks.index')],
            ['title' => trans('cruds.volunteerTask.title_singular') . ' #' . $volunteerTask->id, 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.id') }}</th>
                        <td>{{ $volunteerTask->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.volunteer') }}</th>
                        <td>{{ $volunteerTask->volunteer?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.name') }}</th>
                        <td>{{ $volunteerTask->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.identity') }}</th>
                        <td>{{ $volunteerTask->identity ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.address') }}</th>
                        <td>{{ $volunteerTask->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.phone') }}</th>
                        <td>{{ $volunteerTask->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.details') }}</th>
                        <td>{{ $volunteerTask->details ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.visit_type') }}</th>
                        <td>{{ \App\Models\VolunteerTask::VISIT_TYPE_SELECT[$volunteerTask->visit_type] ?? $volunteerTask->visit_type }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.date') }}</th>
                        <td>{{ $volunteerTask->date ? $volunteerTask->date : '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.arrive_time') }}</th>
                        <td>{{ $volunteerTask->arrive_time ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.leave_time') }}</th>
                        <td>{{ $volunteerTask->leave_time ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.status') }}</th>
                        <td><span class="badge bg-{{ $volunteerTask->status === 'completed' ? 'success' : ($volunteerTask->status === 'cancelled' ? 'danger' : 'secondary') }}">{{ \App\Models\VolunteerTask::STATUS_SELECT[$volunteerTask->status] ?? $volunteerTask->status }}</span></td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.cancel_reason') }}</th>
                        <td>{{ $volunteerTask->cancel_reason ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteerTask.fields.notes') }}</th>
                        <td>{{ $volunteerTask->notes ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @can('volunteer_task_show')
        <a class="btn btn-primary-light rounded-pill btn-wave" href="{{ route('admin.volunteer-tasks.qr', $volunteerTask->id) }}">
            <i class="ri-qr-code-line"></i> {{ trans('cruds.volunteerTask.fields.qr') ?? 'QR Code' }}
        </a>
    @endcan
    <a class="btn btn-secondary-light rounded-pill btn-wave" href="{{ route('admin.volunteer-tasks.index') }}">{{ trans('global.back') }}</a>
@endsection
