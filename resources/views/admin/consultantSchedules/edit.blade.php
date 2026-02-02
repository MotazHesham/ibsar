@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.consultantSchedule.title'), 'url' => route('admin.consultant-schedules.index')],
            [
                'title' => trans('global.edit') . ' ' . trans('cruds.consultantSchedule.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.consultantSchedule.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.consultant-schedules.update', [$consultantSchedule->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                @include('utilities.form.select', [
                    'name' => 'consultant_id',
                    'label' => 'cruds.consultantSchedule.fields.consultant',
                    'isRequired' => true,
                    'options' => $consultants,
                    'search' => true,
                    'value' => $consultantSchedule->consultant_id
                ])
                @include('utilities.form.select', [
                    'name' => 'day',
                    'label' => 'cruds.consultantSchedule.fields.day',
                    'isRequired' => true,
                    'options' => \App\Models\ConsultantSchedule::DAY_SELECT,
                    'search' => true,
                    'value' => $consultantSchedule->day
                ])
                @include('utilities.form.time', [
                    'name' => 'start_time',
                    'label' => 'cruds.consultantSchedule.fields.start_time',
                    'isRequired' => true,
                    'id' => 'start_time',
                    'value' => $consultantSchedule->start_time
                ])
                @include('utilities.form.time', [
                    'name' => 'end_time',
                    'label' => 'cruds.consultantSchedule.fields.end_time',
                    'isRequired' => true,
                    'id' => 'end_time',
                    'value' => $consultantSchedule->end_time
                ])
                @include('utilities.form.text', [
                    'name' => 'slot_duration',
                    'label' => 'cruds.consultantSchedule.fields.slot_duration',
                    'isRequired' => true,
                    'type' => 'number',
                    'value' => $consultantSchedule->slot_duration
                ])
                @include('utilities.form.select', [
                    'name' => 'attendance_type',
                    'label' => 'cruds.consultantSchedule.fields.attendance_type',
                    'isRequired' => true,
                    'options' => \App\Models\ConsultantSchedule::ATTENDANCE_TYPE_SELECT,
                    'search' => true,
                    'value' => $consultantSchedule->attendance_type
                ])
                @include('utilities.form.checkbox', [
                    'name' => 'is_active',
                    'label' => 'cruds.consultantSchedule.fields.is_active',
                    'isRequired' => false,
                    'value' => $consultantSchedule->is_active
                ])
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

 