@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.lettersManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.lettersOrganization.title'),
                'url' => route('admin.letters-organizations.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.lettersOrganization.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.edit') }} {{ trans('cruds.lettersOrganization.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.letters-organizations.update', [$lettersOrganization->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf

                @include('utilities.form.text', [
                    'name' => 'name',
                    'label' => 'cruds.lettersOrganization.fields.name',
                    'isRequired' => true,
                    'value' => $lettersOrganization->name,
                ])
                @include('utilities.form.text', [
                    'name' => 'contact_person',
                    'label' => 'cruds.lettersOrganization.fields.contact_person',
                    'isRequired' => true,
                    'value' => $lettersOrganization->contact_person,
                ])
                @include('utilities.form.text', [
                    'name' => 'contact_phone',
                    'label' => 'cruds.lettersOrganization.fields.contact_phone',
                    'isRequired' => true,
                    'value' => $lettersOrganization->contact_phone,
                ])
                @include('utilities.form.text', [
                    'name' => 'contact_email',
                    'label' => 'cruds.lettersOrganization.fields.contact_email',
                    'isRequired' => true,
                    'value' => $lettersOrganization->contact_email,
                ])
                @include('utilities.form.text', [
                    'name' => 'address',
                    'label' => 'cruds.lettersOrganization.fields.address',
                    'isRequired' => true,
                    'value' => $lettersOrganization->address,
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
