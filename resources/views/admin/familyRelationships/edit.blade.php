@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.familyRelationship.title'),
                'url' => route('admin.family-relationships.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.familyRelationship.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            @include('utilities.switchlang')
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.family-relationships.update', [$familyRelationship->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.familyRelationship.fields.name',
                    'isRequired' => true,
                    'value' => $familyRelationship->getTranslation('name', currentEditingLang()),
                ])
                @include('utilities.form.radio', [
                    'name' => 'gender',
                    'label' => 'cruds.familyRelationship.fields.gender',
                    'isRequired' => true,
                    'options' => App\Models\FamilyRelationship::GENDER_RADIO,
                    'value' => $familyRelationship->gender,
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
