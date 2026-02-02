@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.consultant.title'), 'url' => route('admin.consultants.index')],
            [
                'title' => trans('global.create') . ' ' . trans('cruds.consultant.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header">
            {{ trans('global.create') }} {{ trans('cruds.consultant.title_singular') }}
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.consultants.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.select', [
                    'name' => 'consultation_type_id',
                    'label' => 'cruds.consultant.fields.consultation_type',
                    'isRequired' => true,
                    'options' => $consultationTypes,
                    'search' => true,
                ])
                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.consultant.fields.name',
                    'isRequired' => true,
                    'type' => 'text',
                ])
                @include('utilities.form.text', [
                    'name' => 'national_id',
                    'label' => 'cruds.consultant.fields.national_id',
                    'isRequired' => true,
                    'type' => 'text',
                ])
                @include('utilities.form.text', [
                    'name' => 'phone_number',
                    'label' => 'cruds.consultant.fields.phone_number',
                    'isRequired' => true,
                    'type' => 'text',
                ])
                @include('utilities.form.text', [
                    'name' => 'academic_degree',
                    'label' => 'cruds.consultant.fields.academic_degree',
                    'isRequired' => true,
                    'type' => 'text',
                ])
                @include('utilities.form.dropzone-multiple', [
                    'name' => 'documents',
                    'id' => 'documents',
                    'label' => 'cruds.consultant.fields.documents',
                    'isRequired' => false,
                    'url' => route('admin.consultants.storeMedia'),
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
