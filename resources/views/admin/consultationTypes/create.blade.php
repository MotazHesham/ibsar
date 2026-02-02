@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.consultationType.title'), 'url' => route('admin.consultation-types.index')],
            [
                'title' => trans('global.create') . ' ' . trans('cruds.consultationType.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.consultationType.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.consultation-types.store') }}" enctype="multipart/form-data">
                @csrf
                
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.consultationType.fields.name',
                    'isRequired' => true,
                    'type' => 'text'
                ])
                <div class="form-group">
                    <button class="btn btn-danger" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection 