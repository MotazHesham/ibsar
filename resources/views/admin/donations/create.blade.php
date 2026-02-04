@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.donationManagement.title'), 'url' => '#'],
            ['title' => trans('global.add') . ' ' . trans('cruds.donation.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.donations.store') }}" id="donation-form">
                @csrf
                <div class="row">
                    @include('utilities.form.select', [
                        'name' => 'donator_id',
                        'label' => 'cruds.donation.fields.donator',
                        'options' => $donators,
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                    ])

                    @include('utilities.form.select', [
                        'name' => 'project_id',
                        'label' => 'cruds.donation.fields.project',
                        'options' => $projects,
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                    ])

                    @include('utilities.form.select', [
                        'name' => 'donation_type',
                        'label' => 'cruds.donation.fields.donation_type',
                        'options' => $donationTypes,
                        'isRequired' => true,
                        'grid' => 'col-md-4',
                        'id' => 'donation_type',
                    ])

                    @include('utilities.form.text', [
                        'name' => 'total_amount',
                        'label' => 'cruds.donation.fields.total_amount',
                        'type' => 'number',
                        'isRequired' => false,
                        'grid' => 'col-md-4 money-only',
                        'attributes' => 'step="0.01"',
                    ])

                    @include('utilities.form.date', [
                        'name' => 'donated_at',
                        'id' => 'donated_at',
                        'label' => 'cruds.donation.fields.donated_at', 
                        'isRequired' => true,
                        'grid' => 'col-md-4', 
                    ])

                    @include('utilities.form.textarea', [
                        'name' => 'notes',
                        'label' => 'cruds.donation.fields.notes',
                        'isRequired' => false,
                        'grid' => 'col-md-12',
                    ]) 
                </div>

                <div class="items-section d-none">
                    <hr>
                    <h5 class="mb-3">{{ trans('cruds.donation.fields.items') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="items-table">
                            <thead>
                                <tr>
                                    <th>{{ trans('cruds.donationItem.fields.item_name') }}</th>
                                    <th style="width: 120px">{{ trans('cruds.donationItem.fields.quantity') }}</th>
                                    <th style="width: 140px">{{ trans('cruds.donationItem.fields.unit_price') }}</th>
                                    <th style="width: 140px">{{ trans('cruds.donationItem.fields.total_price') }}</th>
                                    <th style="width: 60px"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">
                                        {{ trans('cruds.donation.fields.total_amount') }}
                                    </th>
                                    <th>
                                        <span id="items-total">0.00</span>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-item-row">
                        {{ trans('cruds.donationItem.extra.add_row') }}
                    </button>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" type="submit">
                        {{ trans('global.save') }}
                    </button>
                    <a class="btn btn-secondary" href="{{ route('admin.donations.index') }}">
                        {{ trans('global.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        function toggleDonationType() {
            const type = $('#donation_type').val();
            if (type === 'items') {
                $('.items-section').removeClass('d-none');
                $('.money-only').addClass('d-none');
            } else {
                $('.items-section').addClass('d-none');
                $('.money-only').removeClass('d-none');
            }
        }

        function recalcRow($row) {
            const qty = parseFloat($row.find('.item-qty').val()) || 0;
            const price = parseFloat($row.find('.item-price').val()) || 0;
            const total = qty * price;
            $row.find('.item-total').text(total.toFixed(2));
        }

        function recalcTotal() {
            let sum = 0;
            $('#items-table tbody tr').each(function() {
                const rowTotal = parseFloat($(this).find('.item-total').text()) || 0;
                sum += rowTotal;
            });
            $('#items-total').text(sum.toFixed(2));
        }

        function addItemRow() {
            const index = $('#items-table tbody tr').length;
            const row = `
                <tr>
                    <td>
                        <input type="text" name="items[${index}][item_name]" class="form-control" />
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${index}][quantity]" class="form-control item-qty" />
                    </td>
                    <td>
                        <input type="number" step="0.01" name="items[${index}][unit_price]" class="form-control item-price" />
                    </td>
                    <td>
                        <span class="item-total">0.00</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-item-row">
                            <i class="bi bi-x"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#items-table tbody').append(row);
        }

        $(function() {
            toggleDonationType();

            $('#donation_type').on('change', function() {
                toggleDonationType();
            });

            $('#add-item-row').on('click', function() {
                addItemRow();
            });

            $('#items-table').on('input', '.item-qty, .item-price', function() {
                const $row = $(this).closest('tr');
                recalcRow($row);
                recalcTotal();
            });

            $('#items-table').on('click', '.remove-item-row', function() {
                $(this).closest('tr').remove();
                recalcTotal();
            });
        });
    </script>
@endsection

