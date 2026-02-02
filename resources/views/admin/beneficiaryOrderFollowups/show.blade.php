@extends('layouts.master')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.beneficiaryOrderFollowup.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.beneficiary-order-followups.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryOrderFollowup.fields.id') }}
                        </th>
                        <td>
                            {{ $beneficiaryOrderFollowup->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryOrderFollowup.fields.beneficiary_followup') }}
                        </th>
                        <td>
                            {{ $beneficiaryOrderFollowup->beneficiary_followup->service_type ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryOrderFollowup.fields.comment') }}
                        </th>
                        <td>
                            {!! $beneficiaryOrderFollowup->comment !!}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryOrderFollowup.fields.attachments') }}
                        </th>
                        <td>
                            @foreach($beneficiaryOrderFollowup->attachments as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.beneficiaryOrderFollowup.fields.user') }}
                        </th>
                        <td>
                            {{ $beneficiaryOrderFollowup->user->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.beneficiary-order-followups.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection