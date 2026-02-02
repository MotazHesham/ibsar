@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.archive.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.archives.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="archived_at">{{ trans('cruds.archive.fields.archived_at') }}</label>
                <input class="form-control datetime {{ $errors->has('archived_at') ? 'is-invalid' : '' }}" type="text" name="archived_at" id="archived_at" value="{{ old('archived_at') }}">
                @if($errors->has('archived_at'))
                    <div class="invalid-feedback">
                        {{ $errors->first('archived_at') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.archive.fields.archived_at_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="archived_by_id">{{ trans('cruds.archive.fields.archived_by') }}</label>
                <select class="form-control select2 {{ $errors->has('archived_by') ? 'is-invalid' : '' }}" name="archived_by_id" id="archived_by_id" required>
                    @foreach($archived_bies as $id => $entry)
                        <option value="{{ $id }}" {{ old('archived_by_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('archived_by'))
                    <div class="invalid-feedback">
                        {{ $errors->first('archived_by') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.archive.fields.archived_by_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="archive_reason">{{ trans('cruds.archive.fields.archive_reason') }}</label>
                <textarea class="form-control {{ $errors->has('archive_reason') ? 'is-invalid' : '' }}" name="archive_reason" id="archive_reason">{{ old('archive_reason') }}</textarea>
                @if($errors->has('archive_reason'))
                    <div class="invalid-feedback">
                        {{ $errors->first('archive_reason') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.archive.fields.archive_reason_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="metadata">{{ trans('cruds.archive.fields.metadata') }}</label>
                <textarea class="form-control {{ $errors->has('metadata') ? 'is-invalid' : '' }}" name="metadata" id="metadata">{{ old('metadata') }}</textarea>
                @if($errors->has('metadata'))
                    <div class="invalid-feedback">
                        {{ $errors->first('metadata') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.archive.fields.metadata_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="storage_location_id">{{ trans('cruds.archive.fields.storage_location') }}</label>
                <select class="form-control select2 {{ $errors->has('storage_location') ? 'is-invalid' : '' }}" name="storage_location_id" id="storage_location_id">
                    @foreach($storage_locations as $id => $entry)
                        <option value="{{ $id }}" {{ old('storage_location_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('storage_location'))
                    <div class="invalid-feedback">
                        {{ $errors->first('storage_location') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.archive.fields.storage_location_helper') }}</span>
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