@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.course.title'),
                'url' => route('admin.courses.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.course.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title"> 
                {{ trans('global.create') }} {{ trans('cruds.course.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'title',
                        'label' => 'cruds.course.fields.title',
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                    ])
                    @include('utilities.form.select', [
                        'name' => 'attend_type',
                        'label' => 'cruds.course.fields.attend_type',
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                        'options' => App\Models\Course::ATTEND_TYPE_SELECT,
                    ])
                    @include('utilities.form.select', [
                        'name' => 'certificate',
                        'label' => 'cruds.course.fields.certificate',
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                        'options' => App\Models\Course::CERTIFICATE_SELECT,
                    ])
                    @include('utilities.form.textarea', [
                        'name' => 'description',
                        'label' => 'cruds.course.fields.description',
                        'isRequired' => true,
                        'grid' => 'col-md-12',
                        'editor' => true,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'trainer',
                        'label' => 'cruds.course.fields.trainer',
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                    ])
                    @include('utilities.form.date', [
                        'name' => 'start_at',
                        'id' => 'start_at',
                        'label' => 'cruds.course.fields.start_at',
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                    ])
                    @include('utilities.form.date', [
                        'name' => 'end_at',
                        'id' => 'end_at',
                        'label' => 'cruds.course.fields.end_at',
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                    ])
                    @include('utilities.form.textarea', [
                        'name' => 'short_description',
                        'label' => 'cruds.course.fields.short_description',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.dropzone', [
                        'name' => 'photo',
                        'id' => 'photo',
                        'label' => 'cruds.course.fields.photo',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'url' => route('admin.courses.storeMedia'),
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
