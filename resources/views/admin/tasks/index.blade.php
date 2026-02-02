@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.task.title_singular'),
                'url' => route('admin.tasks.index'),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover w-100 datatable-Task">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.task.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.task.fields.name') }}
                            </th>
                            <th>
                                {{ trans('cruds.task.fields.created_at') }}
                            </th> 
                            <th>
                                {{ trans('cruds.task.fields.status') }}
                            </th>
                            <th>
                                {{ trans('cruds.task.fields.due_date') }}
                            </th>
                            <th>
                                {{ trans('cruds.task.fields.task_priority') }}
                            </th>
                            <th>
                                {{ trans('cruds.task.fields.assigned_to') }}
                            </th>
                            <th>
                                {{ trans('cruds.task.fields.task_board') }}
                            </th> 
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead> 
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
            @can('task_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.tasks.massDestroy') }}",
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

            
            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.tasks.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    }, 
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'status',
                        name: 'status.name'
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'task_priority',
                        name: 'task_priority.name'
                    },
                    {
                        data: 'assigned_tos',
                        name: 'assigned_tos.name'
                    },
                    {
                        data: 'task_board',
                        name: 'task_board.name'
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

            let table = $('.datatable-Task:not(.ajaxTable)').DataTable(dtOverrideGlobals)
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
