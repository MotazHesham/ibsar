@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.lettersManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.incomingLetter.title'),
                'url' => route('admin.incoming-letters.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.incomingLetter.title_singular'),
                'url' => route('admin.incoming-letters.create'),
                'permission' => 'incoming_letter_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable w-100 datatable-IncomingLetter">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.letter_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.external_number') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.letter_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.received_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.recevier') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.subject') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.priority') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.incoming_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.incomingLetter.fields.letter_organization') }}
                        </th>  
                        <th>
                            {{ trans('cruds.incomingLetter.fields.created_by') }}
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
            @can('incoming_letter_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.incoming-letters.massDestroy') }}",
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
                ajax: "{{ route('admin.incoming-letters.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'letter_number',
                        name: 'letter_number'
                    },
                    {
                        data: 'external_number',
                        name: 'external_number'
                    },
                    {
                        data: 'letter_date',
                        name: 'letter_date'
                    },
                    {
                        data: 'received_date',
                        name: 'received_date'
                    },
                    {
                        data: 'recevier_name',
                        name: 'recevier.name'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'priority',
                        name: 'priority'
                    },
                    {
                        data: 'incoming_type',
                        name: 'incoming_type'
                    },
                    {
                        data: 'letter_organization_name',
                        name: 'letter_organization.name'
                    }, 
                    {
                        data: 'created_by_name',
                        name: 'created_by.name'
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
            let table = $('.datatable-IncomingLetter').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
