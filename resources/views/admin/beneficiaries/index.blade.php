@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiariesManagment.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.beneficiary.title'), 'url' => '#'],
            ['title' => trans('cruds.beneficiary.extra.'.request('status','approved')), 'url' => '#'],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.beneficiary.title_singular'),
                'url' => route('admin.beneficiaries.create'),
                'permission' => 'beneficiary_create',
            ],
            [
                'title' => 'Import CSV',
                'url' => route('admin.beneficiaries.import'),
                'permission' => 'beneficiary_create',
                'class' => 'btn-success',
                'icon' => 'fas fa-upload',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card"> 
        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable w-100 datatable-Beneficiary">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.beneficiary.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiary.fields.user') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.phone') }}
                        </th>
                        <th>
                            {{ trans('cruds.user.fields.identity_num') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiary.fields.specialist') }}
                        </th>
                        @if(request('status') == 'pending' || request('status') == 'all')
                            <th>
                                {{ trans('cruds.beneficiary.fields.profile_status') }}
                            </th>
                        @endif
                        <th>
                            {{ trans('cruds.user.fields.approved') }}
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
            @can('beneficiary_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.beneficiaries.massDestroy') }}",
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
                ajax: {
                    url: "{{ route('admin.beneficiaries.index') }}",
                    data: {
                        status: "{{ request('status','approved') }}",
                        benef_search: "{{ request()->get('benef_search') }}"
                    }
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
                        data: 'user_name',
                        name: 'user.name'
                    },
                    {
                        data: 'user.phone',
                        name: 'user.phone'
                    },
                    {
                        data: 'user.identity_num',
                        name: 'user.identity_num'
                    },
                    {
                        data: 'specialist_name',
                        name: 'specialist.name'
                    },
                    @if(request('status') == 'pending' || request('status') == 'all') 
                        {
                            data: 'profile_status',
                            name: 'profile_status'
                        },
                    @endif
                    {
                        data: 'user.approved',
                        name: 'user.approved'
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
            let table = $('.datatable-Beneficiary').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
