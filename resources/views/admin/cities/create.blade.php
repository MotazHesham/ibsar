@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.city.title'), 'url' => route('admin.cities.index')],
            ['title' => trans('global.create') . ' ' . trans('cruds.city.title_singular'), 'url' => '#'],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.city.title_singular'),
                'url' => route('admin.cities.create'),
                'permission' => 'city_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.create') }} {{ trans('cruds.city.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.city.fields.name',
                    'isRequired' => true,
                ])
                @include('utilities.form.multiSelect2', [
                    'name' => 'districts',
                    'label' => 'cruds.city.fields.districts',
                    'isRequired' => true,
                    'options' => $districts,
                ])
                <div class="form-group">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
