@extends('layouts.master-beneficiary')
@section('content')
    @php
        $page_title = trans('global.edit') . ' ' . trans('cruds.beneficiaryOrder.extra.title');
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3"> 
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('beneficiary.beneficiary-orders.update', [$beneficiaryOrder->id]) }}"
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
                        'url' => route('beneficiary.beneficiary-orders.storeMedia'),
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
