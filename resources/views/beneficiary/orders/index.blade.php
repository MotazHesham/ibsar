@extends('layouts.master-beneficiary')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrder.extra.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.extra.title'),
                'url' => route('beneficiary.beneficiary-orders.index'),
            ],
        ];
        $page_title = trans('cruds.beneficiaryOrder.extra.title');
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.beneficiaryOrder.extra.title_singular'),
                'url' => route('beneficiary.beneficiary-orders.create', ['service_type' => request()->get('service_type')]),
                'icon' => 'ri-add-line',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable w-100 datatable-BeneficiaryOrder">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.service_type') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.accept_status') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.specialist') }}
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
                ajax: "{{ route('beneficiary.beneficiary-orders.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'service_type',
                        name: 'service_type'
                    },
                    {
                        data: 'accept_status',
                        name: 'accept_status'
                    },
                    {
                        data: 'status_name',
                        name: 'status.name'
                    },
                    {
                        data: 'specialist_name',
                        name: 'specialist.name'
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
            let table = $('.datatable-BeneficiaryOrder').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
