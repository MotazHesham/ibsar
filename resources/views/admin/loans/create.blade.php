@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.loan.title'),
                'url' => route('admin.loans.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.loan.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="cart-title">
                {{ trans('global.create') }} {{ trans('cruds.loan.title_singular') }}
            </h6>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.loans.store') }}" enctype="multipart/form-data">
                @csrf
                @include('utilities.form.text', [
                    'name' => 'amount',
                    'label' => 'cruds.loan.fields.amount',
                    'type' => 'number',
                    'isRequired' => true,
                ])
                @include('utilities.form.text', [
                    'name' => 'installment',
                    'label' => 'cruds.loan.fields.installment',
                    'type' => 'number',
                    'isRequired' => true,
                ])
                @include('utilities.form.text', [       
                    'name' => 'months',
                    'label' => 'cruds.loan.fields.months',
                    'type' => 'number',
                    'isRequired' => true,
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
