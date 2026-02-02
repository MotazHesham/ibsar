@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontReview.title'),
                'url' => route('admin.front-reviews.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.frontReview.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.frontReview.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.front-reviews.update', [$frontReview->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
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
                        'model' => $frontReview,    
                    ])
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.frontReview.fields.name',
                        'isRequired' => true,
                        'value' => $frontReview->name,
                        'col' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'email',
                        'label' => 'cruds.frontReview.fields.email',
                        'isRequired' => true,
                        'value' => $frontReview->email,
                        'col' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'review',
                        'label' => 'cruds.frontReview.fields.review',
                        'isRequired' => true,
                        'value' => $frontReview->review,
                        'col' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'rate',
                        'type' => 'number',
                        'label' => 'cruds.frontReview.fields.rate',
                        'isRequired' => true,
                        'col' => 'col-md-6',
                        'value' => $frontReview->rate,
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
