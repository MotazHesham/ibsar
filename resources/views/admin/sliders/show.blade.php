@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.slider.title'),
                'url' => route('admin.sliders.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.slider.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.sliders.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.slider.fields.id') }}
                            </th>
                            <td>
                                {{ $slider->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.slider.fields.image') }}
                            </th>
                            <td>
                                @if ($slider->image)
                                    <a href="{{ $slider->image->getUrl() }}" target="_blank" style="display: inline-block">
                                        <img src="{{ $slider->image->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.slider.fields.title') }}
                            </th>
                            <td>
                                {{ $slider->title }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.slider.fields.sub_title') }}
                            </th>
                            <td>
                                {{ $slider->sub_title }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.slider.fields.publish') }}
                            </th>
                            <td>
                                <input type="checkbox" disabled="disabled" {{ $slider->publish ? 'checked' : '' }}>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.slider.fields.button_name') }}
                            </th>
                            <td>
                                {{ $slider->button_name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.slider.fields.button_link') }}
                            </th>
                            <td>
                                {{ $slider->button_link }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.sliders.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
