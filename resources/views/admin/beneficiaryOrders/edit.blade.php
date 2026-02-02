@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrdersManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.title'),
                'url' => route('admin.beneficiary-orders.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.beneficiaryOrder.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.edit') }} {{ trans('cruds.beneficiaryOrder.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.beneficiary-orders.update', [$beneficiaryOrder->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf

                <div class="row">  
                    @include('utilities.form.select', [
                        'name' => 'service_type',
                        'label' => 'cruds.beneficiaryOrder.fields.service_type',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'options' => App\Models\Service::TYPE_SELECT,
                        'value' => $beneficiaryOrder->service_type,
                    ]) 
                    @include('utilities.form.text', [
                        'name' => 'title',
                        'label' => 'cruds.beneficiaryOrder.fields.title',
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $beneficiaryOrder->title,
                    ])
                    @include('utilities.form.textarea', [
                        'name' => 'description',
                        'label' => 'cruds.beneficiaryOrder.fields.description',
                        'isRequired' => true,
                        'grid' => 'col-md-12',
                        'editor' => true,
                        'value' => $beneficiaryOrder->description,
                    ])
                    @include('utilities.form.dropzone', [
                        'name' => 'attachment',
                        'id' => 'attachment',
                        'label' => 'cruds.beneficiaryOrder.fields.attachment',
                        'isRequired' => false,
                        'grid' => 'col-md-12',
                        'url' => route('admin.beneficiary-orders.storeMedia'),
                        'model' => $beneficiaryOrder,
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
