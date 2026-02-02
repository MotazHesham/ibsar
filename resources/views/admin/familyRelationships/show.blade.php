@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.familyRelationship.title'),
                'url' => route('admin.family-relationships.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.familyRelationship.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.familyRelationship.title_singular') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.family-relationships.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.familyRelationship.fields.id') }}
                            </th>
                            <td>
                                {{ $familyRelationship->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.familyRelationship.fields.gender') }}
                            </th>
                            <td>
                                {{ App\Models\FamilyRelationship::GENDER_RADIO[$familyRelationship->gender] ?? '' }}
                            </td>
                        </tr>
                        @foreach (config('panel.available_languages') as $langLocale => $langName)
                            <tr>
                                <th>
                                    {{ trans('cruds.familyRelationship.fields.name') }}
                                    ({{ $langName }})
                                </th>
                                <td>
                                    {{ $familyRelationship->getTranslation('name', $langLocale) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.family-relationships.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
