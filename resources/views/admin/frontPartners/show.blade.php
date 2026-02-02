@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontPartner.title'),
                'url' => route('admin.front-partners.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.frontPartner.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card"> 
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-partners.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.frontPartner.fields.id') }}
                            </th>
                            <td>
                                {{ $frontPartner->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontPartner.fields.name') }}
                            </th>
                            <td>
                                {{ $frontPartner->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontPartner.fields.image') }}
                            </th>
                            <td>
                                @if ($frontPartner->image)
                                    <a href="{{ $frontPartner->image->getUrl() }}" target="_blank"
                                        style="display: inline-block">
                                        <img src="{{ $frontPartner->image->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-partners.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
