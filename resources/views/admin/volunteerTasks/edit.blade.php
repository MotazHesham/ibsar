@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteerTask.title'), 'url' => route('admin.volunteer-tasks.index')],
            ['title' => trans('global.edit') . ' ' . trans('cruds.volunteerTask.title_singular') . ' #' . $volunteerTask->id, 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">{{ trans('global.edit') }} {{ trans('cruds.volunteerTask.title_singular') }} #{{ $volunteerTask->id }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.volunteer-tasks.update', [$volunteerTask->id]) }}">
                @method('PUT')
                @csrf
                <div class="row">
                    @include('utilities.form.select', [
                        'name' => 'volunteer_id',
                        'label' => 'cruds.volunteerTask.fields.volunteer',
                        'isRequired' => true,
                        'options' => $volunteers,
                        'search' => true,
                        'value' => old('volunteer_id', $volunteerTask->volunteer_id),
                        'grid' => 'col-md-12',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.volunteerTask.fields.name',
                        'isRequired' => true,
                        'value' => $volunteerTask->name,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'address',
                        'label' => 'cruds.volunteerTask.fields.address',
                        'isRequired' => true,
                        'value' => $volunteerTask->address,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'phone',
                        'label' => 'cruds.volunteerTask.fields.phone',
                        'isRequired' => true,
                        'value' => $volunteerTask->phone,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'identity',
                        'label' => 'cruds.volunteerTask.fields.identity',
                        'isRequired' => true,
                        'value' => $volunteerTask->identity,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.textarea', [
                        'name' => 'details',
                        'label' => 'cruds.volunteerTask.fields.details',
                        'isRequired' => false,
                        'value' => $volunteerTask->details,
                        'grid' => 'col-md-12',
                    ])
                    @include('utilities.form.select', [
                        'name' => 'visit_type',
                        'label' => 'cruds.volunteerTask.fields.visit_type',
                        'isRequired' => true,
                        'options' => ['' => trans('global.pleaseSelect')] + \App\Models\VolunteerTask::VISIT_TYPE_SELECT,
                        'value' => $volunteerTask->visit_type,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.date', [
                        'name' => 'date',
                        'id' => 'date',
                        'label' => 'cruds.volunteerTask.fields.date',
                        'isRequired' => true,
                        'value' => $volunteerTask->date ? $volunteerTask->date : null,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.select', [
                        'name' => 'status',
                        'label' => 'cruds.volunteerTask.fields.status',
                        'isRequired' => false,
                        'options' => ['' => trans('global.pleaseSelect')] + \App\Models\VolunteerTask::STATUS_SELECT,
                        'value' => $volunteerTask->status,
                        'grid' => 'col-md-12',
                    ])
                    <div id="cancel_reason_wrapper" class="col-md-12" style="display: {{ in_array(old('status', $volunteerTask->status), ['rejected', 'cancelled']) ? 'block' : 'none' }};">
                        @include('utilities.form.textarea', [
                            'name' => 'cancel_reason',
                            'label' => 'cruds.volunteerTask.fields.cancel_reason',
                            'isRequired' => false,
                            'value' => $volunteerTask->cancel_reason,
                            'grid' => 'col-md-12',
                        ])
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">{{ trans('global.save') }}</button>
                    <a class="btn btn-secondary-light rounded-pill btn-wave" href="{{ route('admin.volunteer-tasks.index') }}">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            function toggleCancelReason() {
                var status = $('#status').val();
                if (status === 'rejected' || status === 'cancelled') {
                    $('#cancel_reason_wrapper').show();
                } else {
                    $('#cancel_reason_wrapper').hide();
                }
            }
            $('#status').on('change', toggleCancelReason);
            toggleCancelReason();
        });
    </script>
@endsection
