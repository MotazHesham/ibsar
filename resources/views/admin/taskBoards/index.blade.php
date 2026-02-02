@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.taskManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.taskBoard.title'),
                'url' => route('admin.task-boards.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.taskBoard.title_singular'),
                'url' => route('admin.task-boards.create'),
                'permission' => 'task_board_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover w-100 datatable-TaskBoard">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.taskBoard.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.taskBoard.fields.name') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskBoards as $key => $taskBoard)
                            <tr data-entry-id="{{ $taskBoard->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $taskBoard->id ?? '' }}
                                </td>
                                <td>
                                    {{ $taskBoard->name ?? '' }}
                                </td>
                                <td>
                                    @include('partials.datatablesActions', [
                                        'viewGate' => 'task_board_show',
                                        'editGate' => 'task_board_edit',
                                        'deleteGate' => 'task_board_delete',
                                        'crudRoutePart' => 'task-boards',
                                        'row' => $taskBoard,
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
            @can('task_board_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.task-boards.massDestroy') }}",
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
            let table = $('.datatable-TaskBoard:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
