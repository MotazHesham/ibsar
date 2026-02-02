@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.userManagement.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.user.title'), 'url' => '#'],
            ['title' => trans('global.create') . ' ' . trans('cruds.user.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    @include('utilities.form.text', [
                        'name' => 'name',
                        'label' => 'cruds.user.fields.name',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'email',
                        'label' => 'cruds.user.fields.email',
                        'isRequired' => true,
                        'type' => 'email',
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'username',
                        'label' => 'cruds.user.fields.username',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'phone',
                        'label' => 'cruds.user.fields.phone',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'phone_2',
                        'label' => 'cruds.user.fields.phone_2',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'identity_num',
                        'label' => 'cruds.user.fields.identity_num',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'password',
                        'label' => 'cruds.user.fields.password',
                        'isRequired' => true,
                        'type' => 'password',
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.select', [
                        'name' => 'section_id',
                        'label' => 'cruds.user.fields.section',
                        'isRequired' => true,
                        'options' => $sections,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.select', [
                        'name' => 'employee_type',
                        'label' => 'cruds.user.fields.employee_type',
                        'isRequired' => false,
                        'options' => ['' => trans('global.pleaseSelect')] + \App\Models\User::EMPLOYEE_TYPE_SELECT,
                        'grid' => 'col-md-6',
                    ])
                    <div class="col-md-6" id="marital_statuses_wrapper" style="display: none;"> 
                        @include('utilities.form.multiselect2', [
                            'name' => 'marital_statuses',
                            'label' => 'cruds.user.fields.marital_statuses',
                            'isRequired' => false,
                            'options' => $maritalStatuses,
                        ])
                    </div>
                    @include('utilities.form.multiselect2', [
                        'name' => 'roles',
                        'label' => 'cruds.user.fields.roles',
                        'isRequired' => true,
                        'options' => $roles,
                        'grid' => 'col-md-12',
                    ])
                </div>
                <div class="form-group">
                    <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                        {{ trans('global.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#employee_type').change(function() {
                if ($(this).val() == 'specialist') {
                    $('#marital_statuses_wrapper').show();
                } else {
                    $('#marital_statuses_wrapper').hide();
                }
            });
        });
    </script>
@endsection
