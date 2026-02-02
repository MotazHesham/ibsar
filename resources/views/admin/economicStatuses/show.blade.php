@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.economicStatus.title'),
                'url' => route('admin.economic-statuses.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.economicStatus.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.economicStatus.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.economic-statuses.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.economicStatus.fields.id') }}
                            </th>
                            <td>
                                {{ $economicStatus->id }}
                            </td>
                        </tr>
                        @foreach (config('panel.available_languages') as $langLocale => $langName)
                            <tr>
                                <th>
                                    {{ trans('cruds.economicStatus.fields.name') }}
                                    ({{ $langName }})
                                </th>
                                <td>
                                    {{ $economicStatus->getTranslation('name', $langLocale) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>
                                {{ trans('cruds.economicStatus.fields.type') }}
                            </th>
                            <td>
                                {{ App\Models\EconomicStatus::TYPE_SELECT[$economicStatus->type] ?? '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.economic-statuses.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
