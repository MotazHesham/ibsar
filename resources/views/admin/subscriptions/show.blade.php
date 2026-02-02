@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.subscription.title'),
                'url' => route('admin.subscriptions.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.subscription.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card"> 
        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.subscriptions.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.subscription.fields.id') }}
                            </th>
                            <td>
                                {{ $subscription->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.subscription.fields.email') }}
                            </th>
                            <td>
                                {{ $subscription->email }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.subscriptions.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
