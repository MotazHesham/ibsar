@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.consultantSchedule.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.consultantSchedule.title'),
                'url' => route('admin.consultant-schedules.index'),
            ],
        ]; 
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover ajaxTable w-100 datatable-ConsultantSchedule">
                <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>
                            {{ trans('cruds.consultantSchedule.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.consultantSchedule.fields.consultant') }}
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
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons) 

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: {
                    url: "{{ route('admin.consultant-schedules.index') }}"
                },
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'consultant',
                        name: 'consultant'
                    },
                    {
                        data: 'day',
                        name: 'day'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'slot_duration',
                        name: 'slot_duration'
                    },
                    {
                        data: 'attendance_type',
                        name: 'attendance_type'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active'
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
            };
            let table = $('.datatable-ConsultantSchedule').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
@endsection 