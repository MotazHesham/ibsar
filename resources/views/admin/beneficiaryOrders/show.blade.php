@extends('layouts.master')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/signature-capture.css') }}">
@endsection
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrdersManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.title'),
                'url' => route('admin.beneficiary-orders.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.beneficiaryOrder.title_singular'), 'url' => '#'],
        ];
        $pageTitle =
            trans('global.show') . ' ' . trans('cruds.beneficiaryOrder.title_singular') . ' #' . $beneficiaryOrder->id;
    @endphp
    @include('partials.breadcrumb')

    <!-- Start::row-1 -->
    <div class="row">
        <div class="col-xxl-6">
            @include('admin.beneficiaryOrders.partials.info')

            {{-- Funding Summary & Allocations --}}
            <div class="mt-3">
                <div class="card custom-card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">{{ trans('cruds.donationAllocation.title') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="fw-semibold">{{ trans('cruds.donationAllocation.fields.allocated_amount') }}</div>
                                <div class="text-muted">
                                    {{ number_format($fundingSummary['total_allocated'] ?? 0, 2) }}
                                </div>
                            </div>
                        </div>

                        @can('donation_allocation_create')
                            <form method="POST"
                                action="{{ route('admin.beneficiary-orders.allocate-donation', $beneficiaryOrder->id) }}">
                                @csrf
                                <div class="row">
                                    @php
                                        $projectsOptions = collect($donationsData)
                                            ->pluck('project_name', 'project_id')
                                            ->filter()
                                            ->unique();
                                    @endphp

                                    @include('utilities.form.select', [
                                        'name' => 'allocation_project_id',
                                        'label' => 'cruds.project.title_singular',
                                        'options' => $projectsOptions,
                                        'isRequired' => false,
                                        'grid' => 'col-md-6',
                                        'id' => 'allocation_project_id',
                                        'search' => true,
                                    ])

                                    @include('utilities.form.select', [
                                        'name' => 'donation_id',
                                        'label' => 'cruds.donationAllocation.fields.donation',
                                        'options' => [],
                                        'isRequired' => true,
                                        'grid' => 'col-md-6',
                                        'id' => 'donation_id',
                                        'search' => true,
                                    ])

                                    <div class="col-md-6 mb-3" id="money-allocation-wrapper">
                                        @include('utilities.form.text', [
                                            'name' => 'allocated_amount',
                                            'label' => 'cruds.donationAllocation.fields.allocated_amount',
                                            'type' => 'number',
                                            'isRequired' => true,
                                            'grid' => '',
                                            'attributes' => 'step="0.01"',
                                        ])
                                    </div>

                                    <div class="col-md-6 mb-3 d-none" id="item-allocation-wrapper">
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <label class="form-label" for="allocation_item">
                                                    {{ trans('cruds.donation.fields.items') }}
                                                </label>
                                                <select id="allocation_item" class="form-select"></select>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label" for="item_quantity">
                                                    {{ trans('cruds.donationItem.fields.quantity') }}
                                                </label>
                                                <input type="number" class="form-control" id="item_quantity" step="0.01">
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">
                                                    {{ trans('cruds.donationItem.fields.total_price') }}
                                                </label>
                                                <div id="item_total_display" class="form-control-plaintext">0.00</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div id="donation-details" class="text-muted small"></div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ trans('cruds.donationAllocation.extra.allocate') }}
                                </button>
                            </form>
                        @endcan

                        @if ($beneficiaryOrder->donationAllocations->count())
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('cruds.donationAllocation.fields.donation') }}</th>
                                            <th>{{ trans('cruds.donationAllocation.fields.donator') }}</th>
                                            <th>{{ trans('cruds.donationAllocation.fields.allocated_amount') }}</th>
                                            <th>{{ trans('cruds.donation.fields.donated_at') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($beneficiaryOrder->donationAllocations as $allocation)
                                            <tr>
                                                <td>#{{ $allocation->donation_id }}</td>
                                                <td>{{ optional($allocation->donation->donator)->name }}</td>
                                                <td>{{ number_format($allocation->allocated_amount, 2) }}</td>
                                                <td>{{ optional($allocation->donation)->donated_at }}</td>
                                                <td class="text-center">
                                                    @can('donation_allocation_delete')
                                                        <form method="POST"
                                                            action="{{ route('admin.beneficiary-orders.donation-allocations.destroy', [$beneficiaryOrder->id, $allocation->id]) }}"
                                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6">
            @if($beneficiaryOrder->dynamicServiceOrder && $beneficiaryOrder->dynamicServiceOrder->dynamicService && in_array($beneficiaryOrder->dynamicServiceOrder->dynamicService->category, ['training', 'assistance', 'social_programs', 'surgical_procedures', 'detection_center']) && $beneficiaryOrder->dynamicServiceOrder->workflow)
                <div class="mb-2">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">إدارة سير العمل</h6>
                            <p class="text-muted">الحالة الحالية: <span class="badge bg-primary">{{ $beneficiaryOrder->dynamicServiceOrder->workflow->status_label }}</span></p>
                            <a href="{{ route('admin.dynamic-service-workflows.show', $beneficiaryOrder->dynamicServiceOrder) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-cog"></i> إدارة سير العمل
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            <div class="mb-2">
                @include('admin.beneficiaryOrders.partials.edit-status')
            </div>
            <div class="card custom-card justify-content-between">
                <div class="card-header">

                    <ul class="nav nav-tabs tab-style-7 scaleX profile-settings-tab" id="myTab4" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link border border-dashed rounded-bottom-0 px-3 active" id="followups-tab"
                                data-bs-toggle="tab" data-bs-target="#followups-tab-pane" type="button" role="tab"
                                aria-controls="followups-tab-pane" aria-selected="true">
                                {{ trans('cruds.beneficiaryOrder.extra.followups') }}
                            </button>
                        </li>
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link border border-dashed rounded-bottom-0 px-3" id="activity-tab"
                                data-bs-toggle="tab" data-bs-target="#activity-tab-pane" type="button" role="tab"
                                aria-controls="activity-tab-pane" aria-selected="false" tabindex="-1">
                                {{ trans('cruds.beneficiaryOrder.extra.activity') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <div class="tab-pane show active overflow-hidden p-0 border-0" id="followups-tab-pane" role="tabpanel"
                        aria-labelledby="followups-tab" tabindex="0">
                        @include('admin.beneficiaryOrders.partials.followups')
                    </div>
                    <div class="tab-pane overflow-hidden p-0 border-0" id="activity-tab-pane" role="tabpanel"
                        aria-labelledby="activity-tab" tabindex="0">
                        <ul class="list-unstyled profile-timeline" id="activity-timeline" style="max-height: 35rem;">
                            @include('partials.activity', ['activityLogs' => $activityLogs])
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--End::row-1 --> 

@endsection


@section('scripts')
    <script src="{{ asset('js/signature-capture.js') }}"></script>
    <script>
        const donationsData = @json($donationsData);

        function populateDonationsByProject(projectId) {
            const select = document.getElementById('donation_id');
            if (!select) return;

            select.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = '{{ trans('global.pleaseSelect') }}';
            select.appendChild(placeholder);

            Object.keys(donationsData).forEach(key => {
                const d = donationsData[key];
                if (projectId && String(d.project_id) !== String(projectId)) {
                    return;
                }
                const option = document.createElement('option');
                option.value = d.id;
                option.textContent = `#${d.id} - ${d.donator ?? ''} (${d.remaining_amount.toFixed(2)})`;
                select.appendChild(option);
            });
        }

        function renderDonationDetails(id) {
            const container = document.getElementById('donation-details');
            if (!container || !donationsData[id]) {
                container.innerHTML = '';
                return;
            }
            const d = donationsData[id];
            let html =
                `<div><strong>{{ trans('cruds.donation.fields.donator') }}:</strong> ${d.donator ?? '-'}</div>` +
                `<div><strong>{{ trans('cruds.donation.fields.donation_type') }}:</strong> ${d.type}</div>` +
                `<div><strong>{{ trans('cruds.donation.fields.total_amount') }}:</strong> ${d.remaining_amount.toFixed(2)} {{ trans('global.remaining') }}</div>`;

            if (d.items && d.items.length) {
                html += '<div class="mt-1"><strong>{{ trans('cruds.donation.fields.items') }}:</strong><ul class="mb-0">';
                d.items.forEach(item => {
                    html += `<li>${item.item_name} - ${item.quantity} x ${item.unit_price.toFixed(2)}</li>`;
                });
                html += '</ul></div>';
            }
            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const projectSelect = document.getElementById('allocation_project_id');
            const donationSelect = document.getElementById('donation_id');
            const moneyWrapper = document.getElementById('money-allocation-wrapper');
            const itemWrapper = document.getElementById('item-allocation-wrapper');
            const itemSelect = document.getElementById('allocation_item');
            const qtyInput = document.getElementById('item_quantity');
            const totalDisplay = document.getElementById('item_total_display');
            const amountInput = document.querySelector('input[name="allocated_amount"]');

            function updateMode(donationId) {
                if (!donationId || !donationsData[donationId]) {
                    moneyWrapper.classList.remove('d-none');
                    itemWrapper.classList.add('d-none');
                    return;
                }
                const d = donationsData[donationId];
                if (d.donation_type === 'items') {
                    moneyWrapper.classList.add('d-none');
                    itemWrapper.classList.remove('d-none');

                    itemSelect.innerHTML = '';
                    if (d.items && d.items.length) {
                        d.items.forEach((item, index) => {
                            const option = document.createElement('option');
                            option.value = index;
                            option.textContent = `${item.item_name} (${item.quantity} x ${item.unit_price.toFixed(2)})`;
                            itemSelect.appendChild(option);
                        });
                    }
                    qtyInput.value = '';
                    totalDisplay.textContent = '0.00';
                    if (amountInput) amountInput.value = '';
                } else {
                    moneyWrapper.classList.remove('d-none');
                    itemWrapper.classList.add('d-none');
                }
            }

            function recalcItemAmount() {
                const donationId = donationSelect.value;
                if (!donationId || !donationsData[donationId]) return;
                const d = donationsData[donationId];
                const index = itemSelect.value;
                if (!d.items || !d.items[index]) return;
                const item = d.items[index];
                const qty = parseFloat(qtyInput.value) || 0;
                const total = qty * item.unit_price;
                totalDisplay.textContent = total.toFixed(2);
                if (amountInput) amountInput.value = total.toFixed(2);
            }

            if (projectSelect) {
                projectSelect.addEventListener('change', function() {
                    populateDonationsByProject(this.value);
                    renderDonationDetails('');
                    updateMode('');
                });
            }

            if (donationSelect) {
                populateDonationsByProject(projectSelect ? projectSelect.value : null);
                donationSelect.addEventListener('change', function() {
                    renderDonationDetails(this.value);
                    updateMode(this.value);
                });
            }

            if (itemSelect && qtyInput) {
                itemSelect.addEventListener('change', recalcItemAmount);
                qtyInput.addEventListener('input', recalcItemAmount);
            }
        });

        new SimpleBar(document.getElementById('activity-timeline'), {
            autoHide: true
        });
        new SimpleBar(document.getElementById('wrapper-order-followups-to-scroll'), {
            autoHide: true
        });

        // State management object
        const state = {
            currentPage: 1,
            isLoading: false,
            hasMorePages: '{{ $activityLogs->hasMorePages() ? true : false }}'
        };

        function loadMoreActivities(timeline, observer, loadingIndicator) {
            if (state.isLoading || !state.hasMorePages) return;
            const beneficiaryOrderId = '{{ $beneficiaryOrder->id }}';

            state.isLoading = true;
            loadingIndicator.style.display = 'block';
            state.currentPage++;

            $.ajax({
                url: `/admin/beneficiary-orders/${beneficiaryOrderId}`,
                type: 'GET',
                data: {
                    page: state.currentPage
                },
                success: function(response) {
                    $('#activity-timeline .simplebar-content').append(response.html);
                    loadingIndicator.style.display = 'none';
                    if (!response.hasMorePages) {
                        // No more content
                        state.hasMorePages = false;
                        loadingIndicator.style.display = 'none';
                        // Unobserve the last item since we won't need to load more
                        const items = timeline.querySelectorAll('li');
                        if (items.length > 0) {
                            observer.unobserve(items[items.length - 1]);
                        }
                    } else {
                        observeLastItem(observer);
                    }
                    state.isLoading = false;
                },
                error: function(xhr, status, error) {
                    console.error('Error loading more activities:', error);
                    loadingIndicator.style.display = 'none';
                    state.isLoading = false;
                }
            });
        }
    </script>
@endsection
