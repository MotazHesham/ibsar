@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrdersManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.title'),
                'url' => route('admin.beneficiary-orders.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.beneficiaryOrder.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <form method="POST" action="{{ route('admin.beneficiary-orders.store') }}" enctype="multipart/form-data"
        id="beneficiaryOrderForm">
        @csrf
        <div class="row">
            <div class="col-md-2">
                <div class="card">

                    <div class="card-body">  
                        <div id="service-list" class="form-step">
                            @include('partials.beneficiaryOrderForm.service-list')
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card"> 
                    <div class="card-body"> 
                        <div id="service-forms" class="form-step">
                            @include('partials.beneficiaryOrderForm.service-forms')
                        </div>  
                    </div> 
                </div> 
            </div>
        </div>
    </form> 
@endsection
