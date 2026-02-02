@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontProject.title'),
                'url' => route('admin.front-projects.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.frontProject.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card"> 
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-projects.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.frontProject.fields.id') }}
                            </th>
                            <td>
                                {{ $frontProject->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontProject.fields.title') }}
                            </th>
                            <td>
                                {{ $frontProject->title }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontProject.fields.description') }}
                            </th>
                            <td>
                                {{ $frontProject->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontProject.fields.image') }}
                            </th>
                            <td>
                                @if ($frontProject->image)
                                    <a href="{{ $frontProject->image->getUrl() }}" target="_blank"
                                        style="display: inline-block">
                                        <img src="{{ $frontProject->image->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-projects.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
