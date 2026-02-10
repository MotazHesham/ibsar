@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteer.title'), 'url' => route('admin.volunteers.index')],
            ['title' => trans('cruds.volunteer.title_singular') . ' #' . $volunteer->id, 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.id') }}</th>
                        <td>{{ $volunteer->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.name') }}</th>
                        <td>{{ $volunteer->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.identity_num') }}</th>
                        <td>{{ $volunteer->identity_num }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.email') }}</th>
                        <td>{{ $volunteer->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.phone_number') }}</th>
                        <td>{{ $volunteer->phone_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.interest') }}</th>
                        <td>{{ $volunteer->interest ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.initiative_name') }}</th>
                        <td>{{ $volunteer->initiative_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.prev_experience') }}</th>
                        <td>{{ $volunteer->prev_experience ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.approved') }}</th>
                        <td>{{ $volunteer->approved ? trans('global.yes') : trans('global.no') }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.photo') }}</th>
                        <td>
                            @if ($volunteer->photo)
                                <a href="{{ $volunteer->photo->getUrl() }}" target="_blank">
                                    <img src="{{ $volunteer->photo->getUrl('thumb') }}" width="50" height="50">
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ trans('cruds.volunteer.fields.cv') }}</th>
                        <td>
                            @if ($volunteer->cv)
                                <a href="{{ $volunteer->cv->getUrl() }}" target="_blank" class="btn btn-sm btn-primary-light">{{ trans('global.download') }}</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ trans('cruds.volunteerTask.title') }}
        </div>
        <div class="card-body">
            @include('admin.volunteers.relationships.volunteerVolunteerTasks', ['volunteerTasks' => $volunteer->volunteerTasks])
        </div>
    </div>
@endsection
