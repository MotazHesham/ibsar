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
                'title' => trans('global.show') . ' ' . trans('cruds.taskTag.title_singular'),
                'url' => route('admin.task-tags.show', $taskTag->id),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h5 class="card-title mb-0">
                {{ trans('global.show') }} {{ trans('cruds.taskTag.title') }}
            </h5>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.task-tags.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.taskTag.fields.id') }}
                            </th>
                            <td>
                                {{ $taskTag->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.taskTag.fields.name') }}
                            </th>
                            <td>
                                {{ $taskTag->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.taskTag.fields.badge_class') }}
                            </th>
                            <td>
                                {{ App\Models\TaskTag::BADGE_CLASS_SELECT[$taskTag->badge_class] ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.task-tags.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
