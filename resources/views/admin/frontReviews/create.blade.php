@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontReview.title'),
                'url' => route('admin.front-reviews.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.frontReview.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.frontReview.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.front-reviews.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    @include('utilities.form.dropzone', [
                        'name' => 'photo',
                        'id' => 'photo',
                        'label' => 'cruds.frontReview.fields.photo',
                        'url' => route('admin.front-reviews.storeMedia'),
                        'isRequired' => true,
                        'col' => 'col-md-6',
                        'helperBlock' => '',
                        'model' => null,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.frontReview.fields.name',
                        'isRequired' => true,
                        'value' => '',
                        'col' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'email',
                        'label' => 'cruds.frontReview.fields.email',
                        'isRequired' => true,
                        'value' => '',
                        'col' => 'col-md-6',
                    ]) 
                    @include('utilities.form.text', [
                        'name' => 'review',
                        'label' => 'cruds.frontReview.fields.review',
                        'isRequired' => true,
                        'value' => '',
                        'col' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'rate',
                        'type' => 'number',
                        'label' => 'cruds.frontReview.fields.rate',
                        'isRequired' => true,
                        'col' => 'col-md-6',
                        'value' => '',
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
