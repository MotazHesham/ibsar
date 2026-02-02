@extends('layouts.master-beneficiary')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrder.extra.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.extra.title'),
                'url' => route('beneficiary.beneficiary-orders.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.beneficiaryOrder.extra.title_singular'), 'url' => '#'],
        ];
        $page_title = trans('global.create') . ' ' . trans('cruds.beneficiaryOrder.extra.title_singular');
    @endphp
    @include('partials.breadcrumb')
    
    <form method="POST" action="{{ route('beneficiary.beneficiary-orders.store') }}" enctype="multipart/form-data"
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
        <div class="form-group mt-3">
            <button class="btn btn-primary-light rounded-pill btn-wave btn-block" type="submit" id="submitBtn">
                {{ trans('global.save') }}
            </button>
        </div>
    </form> 
@endsection
