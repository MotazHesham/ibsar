@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.buildingBeneficiary.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.building-beneficiaries.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.buildingBeneficiary.fields.id') }}
                        </th>
                        <td>
                            {{ $buildingBeneficiary->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.buildingBeneficiary.fields.building') }}
                        </th>
                        <td>
                            {{ $buildingBeneficiary->building->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.buildingBeneficiary.fields.beneficiary') }}
                        </th>
                        <td>
                            {{ $buildingBeneficiary->beneficiary->dob ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.buildingBeneficiary.fields.notes') }}
                        </th>
                        <td>
                            {{ $buildingBeneficiary->notes }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.building-beneficiaries.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection