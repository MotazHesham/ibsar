@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.jobType.title'),
                'url' => route('admin.job-types.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.jobType.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.jobType.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.job-types.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.jobType.fields.name',
                    'isRequired' => true,
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
