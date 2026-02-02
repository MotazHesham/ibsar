@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.consultationType.title'), 'url' => route('admin.consultation-types.index')],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.consultationType.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.consultationType.title_singular') }}
        </div>

        <div class="card-body">
            <div class="mb-2">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.consultationType.fields.id') }}
                            </th>
                            <td>
                                {{ $consultationType->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultationType.fields.name') }}
                            </th>
                            <td>
                                {{ $consultationType->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultationType.fields.created_at') }}
                            </th>
                            <td>
                                {{ $consultationType->created_at }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.consultationType.fields.updated_at') }}
                            </th>
                            <td>
                                {{ $consultationType->updated_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <a class="btn btn-default" href="{{ route('admin.consultation-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>

            <nav class="mb-3">
                <div class="nav nav-tabs">

                </div>
            </nav>
            <div class="tab-content">

            </div>
        </div>
    </div>
@endsection 