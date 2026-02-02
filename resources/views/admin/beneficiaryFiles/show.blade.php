@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.beneficiaryFile.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.beneficiary-files.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryFile.fields.id') }}
                        </th>
                        <td>
                            {{ $beneficiaryFile->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryFile.fields.beneficiary') }}
                        </th>
                        <td>
                            {{ $beneficiaryFile->beneficiary->dob ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryFile.fields.name') }}
                        </th>
                        <td>
                            {{ $beneficiaryFile->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryFile.fields.file') }}
                        </th>
                        <td>
                            @if($beneficiaryFile->file)
                                <a href="{{ $beneficiaryFile->file->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryFile.fields.required_document') }}
                        </th>
                        <td>
                            {{ $beneficiaryFile->required_document->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.beneficiary-files.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection