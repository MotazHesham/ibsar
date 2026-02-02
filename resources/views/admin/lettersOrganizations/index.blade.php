@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.lettersManagment.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.lettersOrganization.title'), 'url' => '#'],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.lettersOrganization.title_singular'),
                'url' => route('admin.letters-organizations.create'),
                'permission' => 'letters_organization_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable w-100 datatable-LettersOrganization">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.lettersOrganization.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.lettersOrganization.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.lettersOrganization.fields.contact_person') }}
                        </th>
                        <th>
                            {{ trans('cruds.lettersOrganization.fields.contact_phone') }}
                        </th>
                        <th>
                            {{ trans('cruds.lettersOrganization.fields.contact_email') }}
                        </th>
                        <th>
                            {{ trans('cruds.lettersOrganization.fields.address') }}
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
            @can('letters_organization_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.letters-organizations.massDestroy') }}",
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
                ajax: "{{ route('admin.letters-organizations.index') }}",
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
                        data: 'contact_person',
                        name: 'contact_person'
                    },
                    {
                        data: 'contact_phone',
                        name: 'contact_phone'
                    },
                    {
                        data: 'contact_email',
                        name: 'contact_email'
                    },
                    {
                        data: 'address',
                        name: 'address'
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
            let table = $('.datatable-LettersOrganization').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
