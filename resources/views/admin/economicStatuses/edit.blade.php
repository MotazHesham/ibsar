@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.economicStatus.title'),
                'url' => route('admin.economic-statuses.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.economicStatus.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            @include('utilities.switchlang')
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.economic-statuses.update', [$economicStatus->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.economicStatus.fields.name',
                    'isRequired' => true,
                    'value' => $economicStatus->getTranslation('name', currentEditingLang()),
                ])
                @include('utilities.form.select', [
                    'name' => 'type',
                    'label' => 'cruds.economicStatus.fields.type',
                    'isRequired' => true,
                    'options' => App\Models\EconomicStatus::TYPE_SELECT,
                    'value' => $economicStatus->type,
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
