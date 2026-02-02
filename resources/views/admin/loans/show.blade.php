@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.loan.title'),
                'url' => route('admin.loans.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.loan.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">
                {{ trans('global.show') }} {{ trans('cruds.loan.title') }}
            </h6>
        </div>

        <div class="card-body">
            <div class="form-group">
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.loans.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.loan.fields.id') }}
                            </th>
                            <td>
                                {{ $loan->id }}
                            </td>
                        </tr> 
                        <tr>
                            <th>
                                {{ trans('cruds.loan.fields.amount') }}     
                            </th>
                            <td>
                                {{ $loan->amount }}
                            </td>
                        </tr> 
                        <tr>
                            <th>
                                {{ trans('cruds.loan.fields.installment') }}     
                            </th>
                            <td>
                                {{ $loan->installment }}
                            </td>
                        </tr> 
                        <tr>
                            <th>
                                {{ trans('cruds.loan.fields.months') }}     
                            </th>
                            <td>
                                {{ $loan->months }}
                            </td>
                        </tr> 
                    </tbody>
                </table>
                <div class="form-group">
                    <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.loans.index') }}">
                        {{ trans('global.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
