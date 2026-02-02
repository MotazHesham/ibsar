@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.service.title'),
                'url' => route('admin.services.list'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.service.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.service.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'title',
                        'label' => 'cruds.service.fields.title',
                        'isRequired' => true,
                    ]) 
                    @include('utilities.form.text', [
                        'name' => 'quantity',
                        'label' => 'cruds.service.fields.quantity',
                        'isRequired' => false,
                        'type' => 'number',
                    ])
                    @include('utilities.form.date', [
                        'name' => 'start_date',
                        'id' => 'start_date',
                        'label' => 'cruds.service.fields.start_date',
                        'isRequired' => false,
                    ])
                    @include('utilities.form.date', [
                        'name' => 'end_date',
                        'id' => 'end_date',
                        'label' => 'cruds.service.fields.end_date',
                        'isRequired' => false,
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
