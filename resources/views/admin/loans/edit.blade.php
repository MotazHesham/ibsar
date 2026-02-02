@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.loan.title'),
                'url' => route('admin.loans.index'),
            ],
            ['title' => trans('global.edit') . ' ' . trans('cruds.loan.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card"> 
        <div class="card-body">
            <form method="POST" action="{{ route('admin.loans.update', [$loan->id]) }}" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="lang" value="{{ currentEditingLang() }}" id="">
                @include('utilities.form.text', [
                    'name' => 'amount',
                    'label' => 'cruds.loan.fields.amount',
                    'type' => 'number',
                    'isRequired' => true,
                    'value' => $loan->amount,
                ])
                @include('utilities.form.text', [
                    'name' => 'installment',
                    'label' => 'cruds.loan.fields.installment',
                    'type' => 'number',
                    'isRequired' => true,
                    'value' => $loan->installment,
                ])
                @include('utilities.form.text', [       
                    'name' => 'months',
                    'label' => 'cruds.loan.fields.months',
                    'type' => 'number',
                    'isRequired' => true,
                    'value' => $loan->months,
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
