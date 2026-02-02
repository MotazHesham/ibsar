@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskStatus.title'),
                'url' => route('admin.task-statuses.index'),
            ],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.taskStatus.title_singular'),
                'url' => route('admin.task-statuses.show', $taskStatus->id),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h5 class="card-title mb-0">
                {{ trans('global.show') }} {{ trans('cruds.taskStatus.title') }}
            </h5>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.task-statuses.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.taskStatus.fields.id') }}
                            </th>
                            <td>
                                {{ $taskStatus->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.taskStatus.fields.name') }}
                            </th>
                            <td>
                                {{ $taskStatus->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.taskStatus.fields.badge_class') }}
                            </th>
                            <td>
                                {{ App\Models\TaskStatus::BADGE_CLASS_SELECT[$taskStatus->badge_class] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.taskStatus.fields.ordering') }}
                            </th>
                            <td>
                                {{ $taskStatus->ordering }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.task-statuses.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
