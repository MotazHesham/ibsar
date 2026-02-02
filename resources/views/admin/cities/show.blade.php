@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.city.title'), 'url' => route('admin.cities.index')],
            ['title' => trans('global.show') . ' ' . trans('cruds.city.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.city.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.cities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.city.fields.id') }}
                            </th>
                            <td>
                                {{ $city->id }}
                            </td>
                        </tr>
                        @foreach (config('panel.available_languages') as $langLocale => $langName)
                            <tr>
                                <th>
                                    {{ trans('cruds.city.fields.name') }}
                                    ({{ $langName }})
                                </th>
                                <td>
                                    {{ $city->getTranslation('name', $langLocale) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>
                                {{ trans('cruds.city.fields.districts') }}
                            </th>
                            <td>
                                @foreach ($city->districts as $key => $districts)
                                    <span class="badge bg-info">{{ $districts->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.cities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
