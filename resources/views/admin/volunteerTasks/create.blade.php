@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteerTask.title'), 'url' => route('admin.volunteer-tasks.index')],
            ['title' => trans('global.add') . ' ' . trans('cruds.volunteerTask.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">{{ trans('global.create') }} {{ trans('cruds.volunteerTask.title_singular') }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.volunteer-tasks.store') }}">
                @csrf
                <div class="row">
                    @include('utilities.form.select', [
                        'name' => 'volunteer_id',
                        'label' => 'cruds.volunteerTask.fields.volunteer',
                        'isRequired' => true,
                        'options' => $volunteers,
                        'search' => true,
                        'grid' => 'col-md-12',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.volunteerTask.fields.name',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'address',
                        'label' => 'cruds.volunteerTask.fields.address',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'phone',
                        'label' => 'cruds.volunteerTask.fields.phone',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'identity',
                        'label' => 'cruds.volunteerTask.fields.identity',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.textarea', [
                        'name' => 'details',
                        'label' => 'cruds.volunteerTask.fields.details',
                        'isRequired' => false,
                        'grid' => 'col-md-12',
                    ])
                    @include('utilities.form.select', [
                        'name' => 'visit_type',
                        'label' => 'cruds.volunteerTask.fields.visit_type',
                        'isRequired' => true,
                        'options' => ['' => trans('global.pleaseSelect')] + \App\Models\VolunteerTask::VISIT_TYPE_SELECT,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.date', [
                        'name' => 'date',
                        'id' => 'date',
                        'label' => 'cruds.volunteerTask.fields.date',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">{{ trans('global.save') }}</button>
                    <a class="btn btn-secondary-light rounded-pill btn-wave" href="{{ route('admin.volunteer-tasks.index') }}">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
