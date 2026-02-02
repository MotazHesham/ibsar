@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.familyRelationship.title'),
                'url' => route('admin.family-relationships.index'),
            ],
            [
                'title' => trans('global.create') . ' ' . trans('cruds.familyRelationship.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.familyRelationship.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.family-relationships.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.familyRelationship.fields.name',
                    'isRequired' => true,
                ])
                @include('utilities.form.radio', [
                    'name' => 'gender',
                    'label' => 'cruds.familyRelationship.fields.gender',
                    'isRequired' => true,
                    'options' => App\Models\FamilyRelationship::GENDER_RADIO,
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
