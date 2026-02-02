@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.region.title'),
                'url' => route('admin.regions.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.region.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            @include('utilities.switchlang')
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.regions.update', [$region->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.region.fields.name',
                    'isRequired' => true,
                    'value' => $region->getTranslation('name', currentEditingLang()),
                ])
                @include('utilities.form.multiSelect2', [
                    'name' => 'cities',
                    'label' => 'cruds.region.fields.cities',
                    'isRequired' => true,
                    'options' => $cities,
                    'value' => $region->cities->pluck('id')->toArray(),
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
