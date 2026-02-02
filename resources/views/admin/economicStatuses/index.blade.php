@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.generalSetting.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.economicStatus.title'),
                'url' => route('admin.economic-statuses.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.arrange') . ' ' . trans('cruds.economicStatus.title'),
                'url' => '#',
                'onclick' => 'showAjaxModal("' . route('admin.arrange') . '",{model: "App\\\Models\\\EconomicStatus", name: "name", orderColumn: "order_level"})',
                'class' => 'btn-teal',
                'permission' => 'economic_status_access',
            ],
            [
                'title' => trans('global.add') . ' ' . trans('cruds.economicStatus.title_singular'),
                'url' => route('admin.economic-statuses.create'),
                'permission' => 'economic_status_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable w-100 datatable-EconomicStatus">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.economicStatus.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.economicStatus.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.economicStatus.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.economicStatus.fields.data_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.economicStatus.fields.is_required') }}
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
            @can('economic_status_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.economic-statuses.massDestroy') }}",
                    className: 'btn-danger-light rounded-pill',
                    action: function(e, dt, node, config) {
                        var ids = $.map(dt.rows({
                            selected: true
                        }).data(), function(entry) {
                            return entry.id
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
                ajax: "{{ route('admin.economic-statuses.index') }}",
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
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'data_type',
                        name: 'data_type'
                    },
                    {
                        data: 'is_required',
                        name: 'is_required'
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
            let table = $('.datatable-EconomicStatus').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
