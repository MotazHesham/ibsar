@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.building.title'),
                'url' => route('admin.buildings.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.building.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.building.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.buildings.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.id') }}
                            </th>
                            <td>
                                {{ $building->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.name') }}
                            </th>
                            <td>
                                {{ $building->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.number_of_floors') }}
                            </th>
                            <td>
                                {{ $building->number_of_floors }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.number_of_apartments') }}
                            </th>
                            <td>
                                {{ $building->number_of_apartments }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.address') }}
                            </th>
                            <td>
                                {{ $building->address }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.latitude') }}
                            </th>
                            <td>
                                {{ $building->latitude }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.longitude') }}
                            </th>
                            <td>
                                {{ $building->longitude }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.apartment_specifications') }}
                            </th>
                            <td>
                                {{ $building->apartment_specifications }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.building.fields.support_services') }}
                            </th>
                            <td>
                                {{ $building->support_services }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.buildings.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
