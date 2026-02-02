@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.reportManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.storageLocation.title'),
                'url' => route('admin.storage-locations.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.storageLocation.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.storageLocation.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.storage-locations.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.storageLocation.fields.id') }}
                            </th>
                            <td>
                                {{ $storageLocation->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.storageLocation.fields.type') }}
                            </th>
                            <td>
                                {{ App\Models\StorageLocation::TYPE_SELECT[$storageLocation->type] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.storageLocation.fields.code') }}
                            </th>
                            <td>
                                {{ $storageLocation->code }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.storageLocation.fields.name') }}
                            </th>
                            <td>
                                {{ $storageLocation->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.storageLocation.fields.description') }}
                            </th>
                            <td>
                                {{ $storageLocation->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.storageLocation.fields.parent') }}
                            </th>
                            <td>
                                {{ $storageLocation->parent->code ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.storage-locations.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
