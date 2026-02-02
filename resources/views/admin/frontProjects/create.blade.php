@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontProject.title'),
                'url' => route('admin.front-projects.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.frontProject.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.frontProject.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.front-projects.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.dropzone', [
                    'name' => 'image',
                    'id' => 'image',
                    'label' => 'cruds.frontProject.fields.image',
                    'url' => route('admin.sliders.storeMedia'),
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'helperBlock' => '',
                    'model' => null,
                ])
                @include('utilities.form.text', [
                    'name' => 'title',
                    'label' => 'cruds.frontProject.fields.title',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'value' => '',
                ])
                @include('utilities.form.textarea', [
                    'name' => 'description',
                    'label' => 'cruds.frontProject.fields.description',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'value' => '',
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
