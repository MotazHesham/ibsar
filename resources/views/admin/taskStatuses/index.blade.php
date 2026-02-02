@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskStatus.title'),
                'url' => route('admin.task-statuses.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.arrange') . ' ' . trans('cruds.taskStatus.title'),
                'url' => '#',
                'onclick' => 'showAjaxModal("' . route('admin.arrange') . '",{model: "App\\\Models\\\TaskStatus", name: "name", orderColumn: "ordering"})',
                'class' => 'btn-teal',
                'permission' => 'task_status_access',
            ],
            [
                'title' => trans('global.add') . ' ' . trans('cruds.taskStatus.title_singular'),
                'url' => route('admin.task-statuses.create'),
                'permission' => 'task_status_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover w-100 datatable-TaskStatus">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.taskStatus.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.taskStatus.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.taskStatus.fields.badge_class') }}
                            </th> 
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskStatuses as $key => $taskStatus)
                            <tr data-entry-id="{{ $taskStatus->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $taskStatus->id ?? '' }}
                                </td>
                                <td>
                                    {{ $taskStatus->name ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\TaskStatus::BADGE_CLASS_SELECT[$taskStatus->badge_class] ?? '' }}
                                </td> 
                                <td>
                                    @include('partials.datatablesActions', [
                                        'viewGate' => 'task_status_show',
                                        'editGate' => 'task_status_edit',
                                        'deleteGate' => 'task_status_delete',
                                        'crudRoutePart' => 'task-statuses',
                                        'row' => $taskStatus,
                                    ])
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('task_status_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.task-statuses.massDestroy') }}",
                    className: 'btn-danger-light rounded-pill',
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
                pageLength: 25,
            });
            let table = $('.datatable-TaskStatus:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
