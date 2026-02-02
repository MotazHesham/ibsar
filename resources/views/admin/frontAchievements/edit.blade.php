@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontAchievement.title'),
                'url' => route('admin.front-achievements.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.frontAchievement.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.frontAchievement.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.front-achievements.update', [$frontAchievement->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                @include('utilities.form.dropzone', [
                    'name' => 'icon',
                    'id' => 'icon',
                    'label' => 'cruds.frontAchievement.fields.icon',
                    'url' => route('admin.front-achievements.storeMedia'),
                    'isRequired' => true,
                    'helperBlock' => '',
                    'model' => $frontAchievement,
                ])
                @include('utilities.form.text', [
                    'name' => 'title',
                    'label' => 'cruds.frontAchievement.fields.title',
                    'isRequired' => true,
                    'value' => $frontAchievement->title,
                ])
                @include('utilities.form.text', [
                    'name' => 'achievement',
                    'label' => 'cruds.frontAchievement.fields.achievement',
                    'isRequired' => true,
                    'value' => $frontAchievement->achievement,
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
