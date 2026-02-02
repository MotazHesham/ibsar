@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.serviceStatus.title'),
                'url' => route('admin.service-statuses.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.serviceStatus.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.serviceStatus.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.service-statuses.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.serviceStatus.fields.id') }}
                            </th>
                            <td>
                                {{ $serviceStatus->id }}
                            </td>
                        </tr>
                        @foreach (config('panel.available_languages') as $langLocale => $langName)
                            <tr>
                                <th>
                                    {{ trans('cruds.serviceStatus.fields.name') }}
                                    ({{ $langName }})
                                </th>
                                <td>
                                    {{ $serviceStatus->getTranslation('name', $langLocale) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>
                                {{ trans('cruds.serviceStatus.fields.badge_class') }}
                            </th>
                            <td>
                                {{ App\Models\ServiceStatus::BADGE_CLASS_SELECT[$serviceStatus->badge_class] ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.serviceStatus.fields.ordering') }}
                            </th>
                            <td>
                                {{ $serviceStatus->ordering }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.service-statuses.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
