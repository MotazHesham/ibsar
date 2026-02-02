@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.disabilityType.title'),
                'url' => route('admin.disability-types.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.disabilityType.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.disabilityType.title') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.disability-types.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.disabilityType.fields.id') }}
                            </th>
                            <td>
                                {{ $disabilityType->id }}
                            </td>
                        </tr>
                        @foreach (config('panel.available_languages') as $langLocale => $langName)
                            <tr>
                                <th>
                                    {{ trans('cruds.disabilityType.fields.name') }}
                                    ({{ $langName }})
                                </th>
                                <td>
                                    {{ $disabilityType->getTranslation('name', $langLocale) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.disability-types.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
