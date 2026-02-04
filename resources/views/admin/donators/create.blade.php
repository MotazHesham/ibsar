@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => '#'],
            ['title' => trans('global.add') . ' ' . trans('cruds.donator.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.donators.store') }}">
                @csrf
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.donator.fields.name',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])

                    @include('utilities.form.text', [
                        'name' => 'email',
                        'label' => 'cruds.donator.fields.email',
                        'type' => 'email',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                    ])

                    @include('utilities.form.text', [
                        'name' => 'phone',
                        'label' => 'cruds.donator.fields.phone',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                    ])

                    @include('utilities.form.textarea', [
                        'name' => 'notes',
                        'label' => 'cruds.donator.fields.notes',
                        'isRequired' => false,
                        'grid' => 'col-md-12',
                    ])
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">
                        {{ trans('global.save') }}
                    </button>
                    <a class="btn btn-secondary" href="{{ route('admin.donators.index') }}">
                        {{ trans('global.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

