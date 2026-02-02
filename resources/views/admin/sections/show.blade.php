@extends('layouts.master')
@section('content')
@php
    $breadcrumbs = [
        ['title' => trans('cruds.userManagement.title'), 'url' => '#'],
        ['title' => trans('cruds.section.title'), 'url' => route('admin.sections.index')],
        ['title' => trans('global.show') . ' ' . trans('cruds.section.title_singular'), 'url' => '#'],
    ];
@endphp
@include('partials.breadcrumb')

<div class="card">
    <div class="card-header p-3">
        <h6 class="card-title">
            {{ trans('global.show') }} {{ trans('cruds.section.title') }}
        </h6>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.sections.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.section.fields.id') }}
                        </th>
                        <td>
                            {{ $section->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.section.fields.name') }}
                        </th>
                        <td>
                            {{ $section->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.section.fields.department') }}
                        </th>
                        <td>
                            {{ $section->department->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.sections.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection