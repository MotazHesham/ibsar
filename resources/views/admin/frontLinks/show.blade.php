@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontLink.title'),
                'url' => route('admin.front-links.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.frontLink.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-links.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.frontLink.fields.id') }}
                            </th>
                            <td>
                                {{ $frontLink->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontLink.fields.name') }}
                            </th>
                            <td>
                                {{ $frontLink->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontLink.fields.link') }}
                            </th>
                            <td>
                                {{ $frontLink->link }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontLink.fields.position') }}
                            </th>
                            <td>
                                {{ App\Models\FrontLink::POSITION_SELECT[$frontLink->position] ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-links.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
