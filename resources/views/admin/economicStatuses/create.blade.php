@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.economicStatus.title'),
                'url' => route('admin.economic-statuses.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.economicStatus.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.economicStatus.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.economic-statuses.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.economicStatus.fields.name',
                    'isRequired' => true,
                ])
                @include('utilities.form.select', [
                    'name' => 'type',
                    'label' => 'cruds.economicStatus.fields.type',
                    'isRequired' => true,
                    'options' => App\Models\EconomicStatus::TYPE_SELECT,
                ])
                @include('utilities.form.select', [
                    'name' => 'data_type',
                    'label' => 'cruds.economicStatus.fields.data_type',
                    'isRequired' => true,
                    'options' => App\Models\EconomicStatus::DATA_TYPE_SELECT,
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
