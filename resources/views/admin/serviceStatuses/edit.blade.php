@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.serviceStatus.title'),
                'url' => route('admin.service-statuses.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.serviceStatus.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            @include('utilities.switchlang')
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.service-statuses.update', [$serviceStatus->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.serviceStatus.fields.name',
                    'isRequired' => true,
                    'value' => $serviceStatus->getTranslation('name', currentEditingLang()),
                ])
                @include('utilities.form.select', [
                    'name' => 'badge_class',
                    'label' => 'cruds.serviceStatus.fields.badge_class',
                    'isRequired' => true,
                    'options' => App\Models\ServiceStatus::BADGE_CLASS_SELECT,
                    'value' => $serviceStatus->badge_class,
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
