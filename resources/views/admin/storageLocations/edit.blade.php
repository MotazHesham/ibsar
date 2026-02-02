@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.reportManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.storageLocation.title'),
                'url' => route('admin.storage-locations.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.storageLocation.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.edit') }} {{ trans('cruds.storageLocation.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.storage-locations.update', [$storageLocation->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf

                @include('utilities.form.select', [
                    'name' => 'type',
                    'label' => 'cruds.storageLocation.fields.type',
                    'options' => \App\Models\StorageLocation::TYPE_SELECT,
                    'isRequired' => true,
                    'value' => $storageLocation->type,
                ])
                @include('utilities.form.text', [
                    'name' => 'code',
                    'label' => 'cruds.storageLocation.fields.code',
                    'isRequired' => true,
                    'value' => $storageLocation->code,
                ])
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.storageLocation.fields.name',
                    'isRequired' => true,
                    'value' => $storageLocation->name,
                ])
                @include('utilities.form.textarea', [
                    'name' => 'description',
                    'label' => 'cruds.storageLocation.fields.description',
                    'isRequired' => false,
                    'value' => $storageLocation->description,
                ])
                @include('utilities.form.select', [
                    'name' => 'parent_id',
                    'label' => 'cruds.storageLocation.fields.parent',
                    'options' => $parents,
                    'isRequired' => false,
                    'value' => $storageLocation->parent_id,
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
