@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontLink.title'),
                'url' => route('admin.front-links.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.frontLink.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.frontLink.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.front-links.update', [$frontLink->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.frontLink.fields.name',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'value' => $frontLink->name,
                ])
                @include('utilities.form.text', [
                    'name' => 'link',
                    'label' => 'cruds.frontLink.fields.link',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'value' => $frontLink->link,
                ])
                @include('utilities.form.select', [
                    'name' => 'position',
                    'label' => 'cruds.frontLink.fields.position',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'options' => App\Models\FrontLink::POSITION_SELECT,
                    'value' => $frontLink->position,
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
