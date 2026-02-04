@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => '#'],
            ['title' => trans('global.list') . ' ' . trans('cruds.donation.title'), 'url' => '#'],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.donation.title_singular'),
                'url' => route('admin.donations.create'),
                'permission' => 'donation_create',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable w-100 datatable-Donation">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>{{ trans('cruds.donation.fields.id') }}</th>
                        <th>{{ trans('cruds.donation.fields.donator') }}</th>
                        <th>{{ trans('cruds.donation.fields.project') }}</th>
                        <th>{{ trans('cruds.donation.fields.donation_type') }}</th>
                        <th>{{ trans('cruds.donation.fields.total_amount') }}</th>
                        <th>{{ trans('cruds.donation.fields.donated_at') }}</th>
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

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                aaSorting: [],
                ajax: "{{ route('admin.donations.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'donator_name',
                        name: 'donator.name'
                    },
                    {
                        data: 'project_name',
                        name: 'project.name'
                    },
                    {
                        data: 'donation_type',
                        name: 'donation_type'
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount'
                    },
                    {
                        data: 'donated_at',
                        name: 'donated_at'
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
            let table = $('.datatable-Donation').DataTable(dtOverrideGlobals);
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
        });
    </script>
@endsection

