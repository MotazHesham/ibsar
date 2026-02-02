@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.consultantSchedule.title'), 'url' => route('admin.consultant-schedules.index')],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.consultantSchedule.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.consultantSchedule.title_singular') }}
        </div>

        <div class="card-body">
            <div class="mb-2">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.id') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.consultant') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->consultant->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.day') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->day }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.start_time') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->start_time }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.end_time') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->end_time }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.slot_duration') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->slot_duration }} {{ trans('global.minutes') }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.attendance_type') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->attendance_type }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.is_active') }}
                            </th>
                            <td>
                                @if($consultantSchedule->is_active)
                                    <span class="badge bg-success">{{ trans('global.yes') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ trans('global.no') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.created_at') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->created_at }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultantSchedule.fields.updated_at') }}
                            </th>
                            <td>
                                {{ $consultantSchedule->updated_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <a class="btn btn-default" href="{{ route('admin.consultant-schedules.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>

            <nav class="mb-3">
                <div class="nav nav-tabs">

                </div>
            </nav>
            <div class="tab-content">

            </div>
        </div>
    </div>
@endsection 