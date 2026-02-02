@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskBoard.title'),
                'url' => route('admin.task-boards.index'),
            ],
            [
                'title' => trans('global.edit') . ' ' . trans('cruds.taskBoard.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.edit') }} {{ trans('cruds.taskBoard.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.task-boards.update', [$taskBoard->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.taskBoard.fields.name',
                    'isRequired' => true,
                    'value' => $taskBoard->name,
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
