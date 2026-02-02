@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.accommodationEntity.title'),
                'url' => route('admin.accommodation-entities.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.accommodationEntity.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.show') }} {{ trans('cruds.accommodationEntity.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.accommodation-entities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.accommodationEntity.fields.id') }}
                            </th>
                            <td>
                                {{ $accommodationEntity->id }}
                            </td>
                        </tr>
                        @foreach (config('panel.available_languages') as $langLocale => $langName)
                            <tr>
                                <th>
                                    {{ trans('cruds.accommodationEntity.fields.name') }}
                                    ({{ $langName }})
                                </th>
                                <td>
                                    {{ $accommodationEntity->getTranslation('name', $langLocale) }}
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th>
                                {{ trans('cruds.accommodationEntity.fields.type') }}
                            </th>
                            <td>
                                {{ $accommodationEntity->type ? \App\Models\AccommodationEntity::$TYPE_SELECT[$accommodationEntity->type] : '' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.accommodation-entities.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection 