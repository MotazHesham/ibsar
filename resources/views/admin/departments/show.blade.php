@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.userManagement.title'), 'url' => '#'],
            ['title' => trans('cruds.department.title'), 'url' => route('admin.departments.index')],
            ['title' => trans('global.show') . ' ' . trans('cruds.department.title_singular')],
        ];
    @endphp

    @include('partials.breadcrumb')

    <div class="card"> 
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.departments.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.department.fields.id') }}
                            </th>
                            <td>
                                {{ $department->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.department.fields.name') }}
                            </th>
                            <td>
                                {{ $department->name }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.departments.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
