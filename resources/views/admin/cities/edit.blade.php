@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.city.title'), 'url' => route('admin.cities.index')],
            ['title' => trans('global.edit') . ' ' . trans('cruds.city.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            @include('utilities.switchlang')
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.update', [$city->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.city.fields.name',
                    'isRequired' => true,
                    'value' => $city->getTranslation('name', currentEditingLang()),
                ])
                @include('utilities.form.multiSelect2', [
                    'name' => 'districts',
                    'label' => 'cruds.city.fields.districts',
                    'isRequired' => true,
                    'options' => $districts,
                    'value' => $city->districts->pluck('id')->toArray(),
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
