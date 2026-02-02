@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontAchievement.title'),
                'url' => route('admin.front-achievements.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.frontAchievement.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.frontAchievement.title') }}
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-achievements.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.frontAchievement.fields.id') }}
                            </th>
                            <td>
                                {{ $frontAchievement->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontAchievement.fields.icon') }}
                            </th>
                            <td>
                                @if ($frontAchievement->icon)
                                    <a href="{{ $frontAchievement->icon->getUrl() }}" target="_blank">
                                        <img src="{{ $frontAchievement->icon->getUrl('thumb') }}"
                                            alt="{{ $frontAchievement->title }}" >
                                    </a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontAchievement.fields.title') }}
                            </th>
                            <td>
                                {{ $frontAchievement->title }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontAchievement.fields.achievement') }}
                            </th>
                            <td>
                                {{ $frontAchievement->achievement }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-achievements.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
