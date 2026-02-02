@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrdersManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.title'),
                'url' => route('admin.beneficiary-orders.index'),
            ],
        ];
        $buttons = [
            [
                'title' => trans('global.add') . ' ' . trans('cruds.beneficiaryOrder.title_singular'),
                'url' => route('admin.beneficiary-orders.create', ['service_type' => 'social']),
                'permission' => 'beneficiary_order_create',
            ],
            [
                'title' => 'Import CSV',
                'url' => '#',
                'permission' => 'beneficiary_create',
                'class' => 'btn-success',
                'icon' => 'fas fa-upload',
                'onclick' => 'importCSVBeneficiaryOrders()',
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
                            {{ trans('cruds.beneficiaryOrder.fields.beneficiary') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.beneficiaryOrder.fields.service_type') }}
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


    <div class="modal fade" id="importCSVModal" tabindex="-1" aria-labelledby="importCSVModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importCSVModalLabel">
                        {{ trans('global.import') }} {{ trans('cruds.beneficiaryOrder.title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="{{ route('admin.beneficiary-orders.import') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <select name="service_type" id="service_type" class="form-control">
                                        <option value="">{{ trans('global.select') }}
                                            {{ trans('cruds.service.title_singular') }}</option>
                                        @foreach (\App\Models\Service::TYPE_SELECT as $key => $service) 
                                            <option value="{{ $key }}">{{ $service }}</option> 
                                        @endforeach 
                                    </select>
                                </div>
                                <div class="form-group mb-3" style="display: none;" id="service_id_container">
                                    <select name="service_id" id="service_id" class="form-control">
                                        <option value="">{{ trans('global.select') }}
                                            {{ trans('cruds.service.title_singular') }}</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="file" name="csv_file" id="csv_file" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">{{ trans('global.import') }}</button>
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">{{ trans('global.close') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        function importCSVBeneficiaryOrders() {
            $('#importCSVModal').modal('show');
        }
        $('#service_type').change(function() {
            if ($(this).val() != '') {
                $('#service_id_container').show();
                $.ajax({
                    url: "{{ route('admin.services.services_by_type') }}",
                    type: 'GET',
                    data: {
                        service_type: $(this).val()
                    },
                    success: function(response) {
                        console.log('Services response:', response);
                        $('#service_id').empty();
                        $('#service_id').append(
                            '<option value="">{{ trans('global.select') }} {{ trans('cruds.service.title_singular') }}</option>'
                            );

                        if (Array.isArray(response)) {
                            for (let i = 0; i < response.length; i++) {
                                $('#service_id').append('<option value="' + response[i].id + '">' +
                                    response[i].title + '</option>');
                            }
                        } else {
                            console.error('Expected array but got:', typeof response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Response:', xhr.responseText);
                        $('#service_id').empty();
                        $('#service_id').append(
                            '<option value="">{{ trans('global.select') }} {{ trans('cruds.service.title_singular') }}</option>'
                            );
                    }
                });
            } else {
                $('#service_id_container').hide();
                $('#service_id').empty();
                $('#service_id').append(
                    '<option value="">{{ trans('global.select') }} {{ trans('cruds.service.title_singular') }}</option>'
                    );
            }
        });
    </script>
    <script>
        $(function() {
            console.log('DataTable initialization started');

            // Check if jQuery and DataTable are loaded
            if (typeof $ === 'undefined') {
                console.error('jQuery is not loaded');
                return;
            }

            if (typeof $.fn.DataTable === 'undefined') {
                console.error('DataTable is not loaded');
                return;
            }
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('beneficiary_order_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                    text: deleteButtonTrans,
                    url: "{{ route('admin.beneficiary-orders.massDestroy') }}",
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
                    url: "{{ route('admin.beneficiary-orders.index') }}",
                    data: {
                        status: "{{ request('status', 'current') }}"
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
                        data: 'beneficiary_user_name',
                        name: 'beneficiary.user.name'
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
