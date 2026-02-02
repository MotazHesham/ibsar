@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryCategory.title'),
                'url' => route('admin.beneficiary-categories.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.beneficiaryCategory.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.beneficiaryCategory.title_singular') }}
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.beneficiary-categories.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.beneficiaryCategory.fields.name',
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

