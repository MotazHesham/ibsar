@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskPriority.title'),
                'url' => route('admin.task-priorities.index'),
            ],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.taskPriority.title_singular'),
                'url' => route('admin.task-priorities.show', $taskPriority->id),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h5 class="card-title mb-0">
                {{ trans('global.show') }} {{ trans('cruds.taskPriority.title') }}
            </h5>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.task-priorities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.taskPriority.fields.id') }}
                            </th>
                            <td>
                                {{ $taskPriority->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.taskPriority.fields.name') }}
                            </th>
                            <td>
                                {{ $taskPriority->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.taskPriority.fields.badge_class') }}
                            </th>
                            <td>
                                {{ App\Models\TaskPriority::BADGE_CLASS_SELECT[$taskPriority->badge_class] ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.task-priorities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
