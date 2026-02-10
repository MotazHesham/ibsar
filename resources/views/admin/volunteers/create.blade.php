@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteer.title'), 'url' => route('admin.volunteers.index')],
            ['title' => trans('global.add') . ' ' . trans('cruds.volunteer.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">{{ trans('global.create') }} {{ trans('cruds.volunteer.title_singular') }}</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.volunteers.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.volunteer.fields.name',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'identity_num',
                        'label' => 'cruds.volunteer.fields.identity_num',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'email',
                        'label' => 'cruds.volunteer.fields.email',
                        'type' => 'email',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'phone_number',
                        'label' => 'cruds.volunteer.fields.phone_number',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'interest',
                        'label' => 'cruds.volunteer.fields.interest',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'initiative_name',
                        'label' => 'cruds.volunteer.fields.initiative_name',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'prev_experience',
                        'label' => 'cruds.volunteer.fields.prev_experience',
                        'isRequired' => false,
                        'grid' => 'col-md-12',
                    ])
                    @include('utilities.form.dropzone', [
                        'name' => 'cv',
                        'id' => 'cv',
                        'label' => 'cruds.volunteer.fields.cv',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                        'url' => route('admin.volunteers.storeMedia'),
                    ])
                    @include('utilities.form.dropzone', [
                        'name' => 'photo',
                        'id' => 'photo',
                        'label' => 'cruds.volunteer.fields.photo',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'url' => route('admin.volunteers.storeMedia'),
                    ])
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">{{ trans('global.save') }}</button>
                    <a class="btn btn-secondary-light rounded-pill btn-wave" href="{{ route('admin.volunteers.index') }}">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
