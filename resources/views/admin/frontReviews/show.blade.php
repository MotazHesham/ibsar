@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.frontReview.title'),
                'url' => route('admin.front-reviews.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.frontReview.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-reviews.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.frontReview.fields.id') }}
                            </th>
                            <td>
                                {{ $frontReview->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontReview.fields.photo') }}
                            </th>
                            <td>
                                @if ($frontReview->photo)
                                    <a href="{{ $frontReview->photo->getUrl() }}" target="_blank"
                                        style="display: inline-block">
                                        <img src="{{ $frontReview->photo->getUrl('thumb') }}">
                                    </a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontReview.fields.name') }}
                            </th>
                            <td>
                                {{ $frontReview->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontReview.fields.email') }}
                            </th>
                            <td>
                                {{ $frontReview->email }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontReview.fields.review') }}
                            </th>
                            <td>
                                {{ $frontReview->review }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.frontReview.fields.rate') }}
                            </th>
                            <td>
                                {{ $frontReview->rate }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.front-reviews.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
