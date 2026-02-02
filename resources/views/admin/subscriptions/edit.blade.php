@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.frontendSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.subscription.title'),
                'url' => route('admin.subscriptions.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.subscription.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header">
            {{ trans('global.edit') }} {{ trans('cruds.subscription.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.subscriptions.update', [$subscription->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                @include('utilities.form.text', [
                    'name' => 'email',
                    'label' => 'cruds.subscription.fields.email',
                    'isRequired' => true,
                    'value' => $subscription->email,
                ])
                <div class="form-group">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
