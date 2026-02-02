@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.requiredDocument.title'),
                'url' => route('admin.required-documents.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.requiredDocument.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.requiredDocument.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.required-documents.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.requiredDocument.fields.name',
                    'isRequired' => true,
                ])
                @include('utilities.form.select', [
                    'name' => 'marital_status_id',
                    'label' => 'cruds.requiredDocument.fields.marital_status',
                    'isRequired' => false,
                    'options' => $maritalStatuses,
                    'helperBlock' => 'cruds.requiredDocument.fields.marital_status_helper',
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
