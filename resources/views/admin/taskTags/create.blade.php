@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskTag.title'),
                'url' => route('admin.task-tags.index'),
            ],
            [
                'title' => trans('global.create') . ' ' . trans('cruds.taskTag.title_singular'),
                'url' => route('admin.task-tags.create'),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h5 class="card-title mb-0">
                {{ trans('global.create') }} {{ trans('cruds.taskTag.title_singular') }}
            </h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.task-tags.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.taskTag.fields.name',
                    'isRequired' => true,
                ])
                @include('utilities.form.select', [
                    'name' => 'badge_class',
                    'label' => 'cruds.taskTag.fields.badge_class',
                    'isRequired' => true,
                    'options' => App\Models\TaskTag::BADGE_CLASS_SELECT,
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
