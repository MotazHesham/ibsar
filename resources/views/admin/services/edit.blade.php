@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.service.title'),
                'url' => route('admin.services.list'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.service.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.edit') }} {{ trans('cruds.service.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.services.update', [$service->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'title',
                        'label' => 'cruds.service.fields.title',
                        'isRequired' => true,
                        'value' => $service->title,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'quantity',
                        'label' => 'cruds.service.fields.quantity',
                        'isRequired' => true,
                        'type' => 'number',
                        'value' => $service->quantity,
                    ])
                    @include('utilities.form.date', [
                        'name' => 'start_date',
                        'id' => 'start_date',
                        'label' => 'cruds.service.fields.start_date',
                        'isRequired' => true,
                        'value' => $service->start_date,
                    ])
                    @include('utilities.form.date', [
                        'name' => 'end_date',
                        'id' => 'end_date',
                        'label' => 'cruds.service.fields.end_date',
                        'isRequired' => true,
                        'value' => $service->end_date,
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
