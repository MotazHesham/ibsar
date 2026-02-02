@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskPriority.title'),
                'url' => route('admin.task-priorities.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.taskPriority.title_singular'),
                'url' => route('admin.task-priorities.create'),
                'permission' => 'task_priority_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover w-100 datatable-TaskPriority">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.taskPriority.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.taskPriority.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.taskPriority.fields.badge_class') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskPriorities as $key => $taskPriority)
                            <tr data-entry-id="{{ $taskPriority->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $taskPriority->id ?? '' }}
                                </td>
                                <td>
                                    {{ $taskPriority->name ?? '' }}
                                </td>
                                <td>
                                    {{ App\Models\TaskPriority::BADGE_CLASS_SELECT[$taskPriority->badge_class] ?? '' }}
                                </td>
                                <td>
                                    @include('partials.datatablesActions', [
                                        'viewGate' => 'task_priority_show',
                                        'editGate' => 'task_priority_edit',
                                        'deleteGate' => 'task_priority_delete',
                                        'crudRoutePart' => 'task-priorities',
                                        'row' => $taskPriority,
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
            @can('task_priority_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.task-priorities.massDestroy') }}",
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
            let table = $('.datatable-TaskPriority:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
