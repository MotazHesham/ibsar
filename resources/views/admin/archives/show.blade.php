@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.archive.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.archives.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.id') }}
                        </th>
                        <td>
                            {{ $archive->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.archived_at') }}
                        </th>
                        <td>
                            {{ $archive->archived_at }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.archived_by') }}
                        </th>
                        <td>
                            {{ $archive->archived_by->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.archive_reason') }}
                        </th>
                        <td>
                            {{ $archive->archive_reason }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.metadata') }}
                        </th>
                        <td>
                            {{ $archive->metadata }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.archive.fields.storage_location') }}
                        </th>
                        <td>
                            {{ $archive->storage_location->code ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.archives.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection