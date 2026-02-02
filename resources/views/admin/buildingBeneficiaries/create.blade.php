@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.buildingBeneficiary.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.building-beneficiaries.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="building_id">{{ trans('cruds.buildingBeneficiary.fields.building') }}</label>
                <select class="form-control select2 {{ $errors->has('building') ? 'is-invalid' : '' }}" name="building_id" id="building_id" required>
                    @foreach($buildings as $id => $entry)
                        <option value="{{ $id }}" {{ old('building_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('building'))
                    <div class="invalid-feedback">
                        {{ $errors->first('building') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.buildingBeneficiary.fields.building_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="beneficiary_id">{{ trans('cruds.buildingBeneficiary.fields.beneficiary') }}</label>
                <select class="form-control select2 {{ $errors->has('beneficiary') ? 'is-invalid' : '' }}" name="beneficiary_id" id="beneficiary_id" required>
                    @foreach($beneficiaries as $id => $entry)
                        <option value="{{ $id }}" {{ old('beneficiary_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('beneficiary'))
                    <div class="invalid-feedback">
                        {{ $errors->first('beneficiary') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.buildingBeneficiary.fields.beneficiary_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="notes">{{ trans('cruds.buildingBeneficiary.fields.notes') }}</label>
                <textarea class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes">{{ old('notes') }}</textarea>
                @if($errors->has('notes'))
                    <div class="invalid-feedback">
                        {{ $errors->first('notes') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.buildingBeneficiary.fields.notes_helper') }}</span>
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