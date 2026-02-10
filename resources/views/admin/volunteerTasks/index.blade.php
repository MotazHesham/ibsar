@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteerTask.title'), 'url' => '#'],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.volunteerTask.title_singular'),
                'url' => route('admin.volunteer-tasks.create'),
                'permission' => 'volunteer_task_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover ajaxTable w-100 datatable-VolunteerTask">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.volunteerTask.fields.id') }}</th>
                        <th>{{ trans('cruds.volunteerTask.fields.volunteer') }}</th>
                        <th>{{ trans('cruds.volunteerTask.fields.name') }}</th>
                        <th>{{ trans('cruds.volunteerTask.fields.address') }}</th>
                        <th>{{ trans('cruds.volunteerTask.fields.phone') }}</th>
                        <th>{{ trans('cruds.volunteerTask.fields.visit_type') }}</th>
                        <th>{{ trans('cruds.volunteerTask.fields.date') }}</th>
                        <th>{{ trans('cruds.volunteerTask.fields.status') }}</th>
                        <th>&nbsp;</th>
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
            @can('volunteer_task_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.volunteer-tasks.massDestroy') }}",
                    className: 'btn-danger-light rounded-pill',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({ selected: true }).data(), function(entry) { return entry.id });
                        if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
                        if (confirm('{{ trans('global.areYouSure') }}')) {
                            $.ajax({
                                headers: { 'x-csrf-token': _token },
                                method: 'POST',
                                url: config.url,
                                data: { ids: ids, _method: 'DELETE' }
                            }).done(function() { location.reload() })
                        }
                    }
                }
                dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.volunteer-tasks.index') }}",
                columns: [
                    { data: 'placeholder', name: 'placeholder' },
                    { data: 'id', name: 'id' },
                    { data: 'volunteer_name', name: 'volunteer.name' },
                    { data: 'name', name: 'name' },
                    { data: 'address', name: 'address' },
                    { data: 'phone', name: 'phone' },
                    { data: 'visit_type', name: 'visit_type' },
                    { data: 'date', name: 'date' },
                    { data: 'status', name: 'status' },
                    { data: 'actions', name: '{{ trans('global.actions') }}' }
                ],
                orderCellsTop: true,
                order: [[1, 'desc']],
                pageLength: 25,
            };
            $('.datatable-VolunteerTask').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });
        });
    </script>
@endsection
