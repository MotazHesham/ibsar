@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.district.title'),
                'url' => route('admin.districts.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.district.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            @include('utilities.switchlang')
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.districts.update', [$district->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.district.fields.name',
                    'isRequired' => true,
                    'value' => $district->getTranslation('name', currentEditingLang()),
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
