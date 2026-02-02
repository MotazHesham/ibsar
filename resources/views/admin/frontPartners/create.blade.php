@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontPartner.title'),
                'url' => route('admin.front-partners.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.frontPartner.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.frontPartner.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.front-partners.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.dropzone', [
                    'name' => 'image',
                    'id' => 'image',
                    'label' => 'cruds.frontPartner.fields.image',
                    'url' => route('admin.front-partners.storeMedia'),
                    'isRequired' => true,
                    'helperBlock' => '',
                    'model' => null,
                ])
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.frontPartner.fields.name',
                    'isRequired' => true,
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
