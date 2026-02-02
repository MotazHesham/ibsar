@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.building.title'),
                'url' => route('admin.buildings.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.building.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.edit') }} {{ trans('cruds.building.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.buildings.update', [$building->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="row">

                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.building.fields.name',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $building->name,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'number_of_floors',
                        'label' => 'cruds.building.fields.number_of_floors',
                        'isRequired' => true,
                        'type' => 'number',
                        'grid' => 'col-md-6',
                        'value' => $building->number_of_floors,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'number_of_apartments',
                        'label' => 'cruds.building.fields.number_of_apartments',
                        'isRequired' => true,
                        'type' => 'number',
                        'grid' => 'col-md-6',
                        'value' => $building->number_of_apartments,
                    ])
                    @include('utilities.form.text', [
                        'name' => 'address',
                        'label' => 'cruds.building.fields.address',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $building->address,
                    ])
                    @include('utilities.form.map', [
                        'name' => 'map',
                        'label' => 'cruds.building.fields.map', 
                        'grid' => 'col-md-12',
                        'isRequired' => false, 
                        'latitude' => $building->latitude ?? '',
                        'longitude' => $building->longitude ?? '',
                    ])
                    @include('utilities.form.textarea', [
                        'name' => 'apartment_specifications',
                        'label' => 'cruds.building.fields.apartment_specifications',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $building->apartment_specifications,
                    ])
                    @include('utilities.form.textarea', [
                        'name' => 'support_services',
                        'label' => 'cruds.building.fields.support_services',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $building->support_services,
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
