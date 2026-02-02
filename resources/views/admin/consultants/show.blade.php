@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.consultant.title'), 'url' => route('admin.consultants.index')],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.consultant.title_singular'),
                'url' => '#',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    {{ trans('global.show') }} {{ trans('cruds.consultant.title_singular') }}
                </div>

                <div class="card-body">
                    <div class="mb-2">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $consultant->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.consultation_type') }}
                                    </th>
                                    <td>
                                        {{ $consultant->consultationType->name ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.name') }}
                                    </th>
                                    <td>
                                        {{ $consultant->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.national_id') }}
                                    </th>
                                    <td>
                                        {{ $consultant->national_id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.phone_number') }}
                                    </th>
                                    <td>
                                        {{ $consultant->phone_number }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.academic_degree') }}
                                    </th>
                                    <td>
                                        {{ $consultant->academic_degree }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.documents') }}
                                    </th>
                                    <td>
                                        @foreach ($consultant->getMedia('documents') as $media)
                                            <span class="badge bg-primary">
                                                <a href="{{ $media->getUrl() }}" class="text-white" target="_blank">
                                                    {{ trans('global.view_file') }}
                                                </a>
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.created_at') }}
                                    </th>
                                    <td>
                                        {{ $consultant->created_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.consultant.fields.updated_at') }}
                                    </th>
                                    <td>
                                        {{ $consultant->updated_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <a class="btn btn-default" href="{{ route('admin.consultants.index') }}">
                            {{ trans('global.back_to_list') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">

                    <nav class="mb-3">
                        <div class="nav nav-tabs">
                            <a class="nav-link active" data-bs-toggle="tab" href="#schedules">
                                {{ trans('cruds.consultantSchedule.title_singular') }}
                            </a>
                        </div>
                    </nav>
                    <div class="tab-content">
                        <div class="tab-pane active" role="tabpanel" id="schedules">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th width="10">

                                            </th>
                                            <th>
                                                {{ trans('cruds.consultantSchedule.fields.id') }}
                                            </th>
                                            <th>
                                                {{ trans('cruds.consultantSchedule.fields.day') }}
                                            </th>
                                            <th>
                                                {{ trans('cruds.consultantSchedule.fields.start_time') }}
                                            </th>
                                            <th>
                                                {{ trans('cruds.consultantSchedule.fields.end_time') }}
                                            </th>
                                            <th>
                                                {{ trans('cruds.consultantSchedule.fields.slot_duration') }}
                                            </th>
                                            <th>
                                                {{ trans('cruds.consultantSchedule.fields.attendance_type') }}
                                            </th>
                                            <th>
                                                {{ trans('cruds.consultantSchedule.fields.is_active') }}
                                            </th>
                                            <th>
                                                &nbsp;
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($consultant->schedules as $key => $schedule)
                                            <tr data-entry-id="{{ $schedule->id }}">
                                                <td>

                                                </td>
                                                <td>
                                                    {{ $schedule->id ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $schedule->day ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $schedule->start_time ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $schedule->end_time ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $schedule->slot_duration ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $schedule->attendance_type ? \App\Models\ConsultantSchedule::ATTENDANCE_TYPE_SELECT[$schedule->attendance_type] : '' }}
                                                </td>
                                                <td>
                                                    <div class="custom-toggle-switch toggle-md ms-2">
                                                        <input
                                                            onchange="updateStatuses(this, 'is_active', 'App\\Models\\ConsultantSchedule')"
                                                            value="{{ $schedule->id }}" id="is_active-{{ $schedule->id }}"
                                                            type="checkbox" {{ $schedule->is_active ? 'checked' : '' }}>
                                                        <label for="is_active-{{ $schedule->id }}"
                                                            class="label-success mb-2"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    @include('partials.datatablesActions', [
                                                        'crudRoutePart' => 'consultant-schedules',
                                                        'row' => $schedule,
                                                        'editGate' => 'consultant_schedule_edit',
                                                        'deleteGate' => false,
                                                        'viewGate' => false,
                                                    ]) 
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('consultant_schedule_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.consultant-schedules.massDestroy') }}",
                    className: 'btn-danger',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).nodes(), function(entry) {
                            return $(entry).data('entry-id')
                        });

                        if (ids.length === 0) {
                            alert('{{ trans('global.datatables.zero_selected') }}')

                            return
                        }

                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                    headers: {
                                        'x-csrf-token': _token
                                    },
                                    method: 'POST',
                                    url: config.url,
                                    data: {
                                        ids: ids,
                                        _method: 'DELETE'
                                    }
                                })
                                .done(function() {
                                    location.reload()
                                })
                        }
                    }
                }
                dtButtons.push(deleteButton)
            @endcan

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-ConsultantSchedule:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
