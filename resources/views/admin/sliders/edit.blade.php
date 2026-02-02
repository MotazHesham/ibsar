@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.slider.title'),
                'url' => route('admin.sliders.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.slider.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.slider.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.sliders.update', [$slider->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <div class="row">

                    @include('utilities.form.dropzone', [
                        'name' => 'image',
                        'id' => 'image',
                        'label' => 'cruds.slider.fields.image',
                        'url' => route('admin.sliders.storeMedia'),
                        'isRequired' => true,
                        'grid' => 'col-md-12',
                        'helperBlock' => '',
                        'model' => $slider, 
                    ])
                    @include('utilities.form.text', [
                        'name' => 'title',
                        'label' => 'cruds.slider.fields.title',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $slider->title,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'sub_title',
                        'label' => 'cruds.slider.fields.sub_title',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                        'value' => $slider->sub_title,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'button_name',
                        'label' => 'cruds.slider.fields.button_name',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                        'value' => $slider->button_name,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'button_link',
                        'label' => 'cruds.slider.fields.button_link',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                        'value' => $slider->button_link,
                    ])
                </div>
                <div class="form-group">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
