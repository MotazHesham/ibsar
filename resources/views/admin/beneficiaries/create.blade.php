@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiariesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiary.title'),
                'url' => route('admin.beneficiaries.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.beneficiary.title'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.create') }} {{ trans('cruds.beneficiary.title_singular') }}
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.beneficiaries.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row gy-3">
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.user.fields.name',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'identity_num',
                        'label' => 'cruds.user.fields.identity_num',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'helperBlock' => trans('global.enter_10_digit_identity_number'),
                    ])
                    @include('utilities.form.text', [
                        'name' => 'email',
                        'label' => 'cruds.user.fields.email',
                        'isRequired' => false,
                        'type' => 'email',
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'phone',
                        'label' => 'cruds.user.fields.phone',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'helperBlock' => trans('global.enter_your_phone_number'),
                    ]) 
                    @include('utilities.form.text', [
                        'name' => 'password',
                        'label' => 'cruds.user.fields.password',
                        'isRequired' => true,
                        'type' => 'password',
                        'grid' => 'col-md-6',
                        'helperBlock' => trans('global.password_requirements'),
                    ])
                    @if(\App\Models\Region::count() > 1)
                        @include('utilities.form.select', [
                            'name' => 'region_id',
                            'label' => 'cruds.beneficiary.fields.region',
                            'isRequired' => true,
                            'options' => getRegions(),
                            'grid' => 'col-md-6', 
                            'search' => true,
                        ]) 
                    @endif
                </div>
                <div class="form-group d-flex gap-2">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                        {{ trans('global.save') }}
                    </button>
                    <button class="btn btn-secondary-light rounded-pill btn-wave" name="next" type="submit">
                        {{ trans('global.save_and_complete') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
