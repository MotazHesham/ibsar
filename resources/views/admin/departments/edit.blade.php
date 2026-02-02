@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.userManagement.title'), 'url' => '#'],
            ['title' => trans('cruds.department.title'), 'url' => route('admin.departments.index')],
            ['title' => trans('global.edit') . ' ' . trans('cruds.department.title_singular')],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.department.title_singular'),
                'url' => route('admin.departments.create'),
                'permission' => 'department_create',
            ],
        ];
    @endphp

    @include('partials.breadcrumb')


    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.department.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.departments.update', [$department->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf

                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.department.fields.name',
                    'isRequired' => true,
                    'value' => $department->name,
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
