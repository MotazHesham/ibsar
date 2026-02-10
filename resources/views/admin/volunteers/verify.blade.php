@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteer.title'), 'url' => route('admin.volunteers.index')],
            ['title' => trans('global.verify') . ' ' . trans('cruds.volunteer.title_singular') . ' #' . $volunteer->id, 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">{{ trans('global.verify') }} {{ trans('cruds.volunteer.title_singular') }} #{{ $volunteer->id }}</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>{{ trans('cruds.volunteer.fields.name') }}:</strong> {{ $volunteer->name }}<br>
                <strong>{{ trans('cruds.volunteer.fields.email') }}:</strong> {{ $volunteer->email }}
            </div>
            <form method="POST" action="{{ route('admin.volunteers.verify_submit') }}">
                @csrf
                <input type="hidden" name="id" value="{{ $volunteer->id }}">
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'password',
                        'label' => 'cruds.volunteer.fields.password',
                        'type' => 'password',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'password_confirmation',
                        'label' => 'cruds.volunteer.fields.password_confirmation',
                        'type' => 'password',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">{{ trans('global.verify') }}</button>
                    <a class="btn btn-secondary-light rounded-pill btn-wave" href="{{ route('admin.volunteers.index') }}">{{ trans('global.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
