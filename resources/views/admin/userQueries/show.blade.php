@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.userQuery.title'),
                'url' => route('admin.user-queries.index'),
            ],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.userQuery.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.userQuery.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.user-queries.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.userQuery.fields.id') }}
                            </th>
                            <td>
                                {{ $userQuery->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.userQuery.fields.question') }}
                            </th>
                            <td>
                                {{ $userQuery->question }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.userQuery.fields.answer') }}
                            </th>
                            <td>
                                {{ $userQuery->answer }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.userQuery.fields.user') }}
                            </th>
                            <td>
                                {{ $userQuery->user->name ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.user-queries.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
