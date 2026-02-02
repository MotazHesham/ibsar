@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.accommodationEntity.title'),
                'url' => route('admin.accommodation-entities.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.accommodationEntity.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            @include('utilities.switchlang')
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.accommodation-entities.update', [$accommodationEntity->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.accommodationEntity.fields.name',
                    'isRequired' => true,
                    'value' => $accommodationEntity->getTranslation('name', currentEditingLang()),
                ])
                @include('utilities.form.select', [
                    'name' => 'type',
                    'label' => 'cruds.accommodationEntity.fields.type',
                    'isRequired' => true,
                    'options' => \App\Models\AccommodationEntity::$TYPE_SELECT,
                    'value' => $accommodationEntity->type,
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