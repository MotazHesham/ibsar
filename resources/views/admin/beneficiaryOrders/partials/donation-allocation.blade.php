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
                            'label' => 'cruds.project.fields.name',
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

                        {{-- Single allocated_amount field that will be used for both money and items --}}
                        <input type="hidden" name="allocated_amount" id="allocated_amount" value="" required>

                        <div class="col-md-6 mb-3" id="money-allocation-wrapper">
                            <label class="form-label" for="money_allocated_amount">
                                {{ trans('cruds.donationAllocation.fields.allocated_amount') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   id="money_allocated_amount" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0.01"
                                   placeholder="{{ trans('cruds.donationAllocation.fields.allocated_amount') }}">
                        </div>

                        <div class="col-md-6 mb-3 d-none" id="item-allocation-wrapper">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label" for="allocation_item">
                                        {{ trans('cruds.donation.fields.items') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="allocation_item" name="allocation_item" class="form-select" required>
                                        <option value="">{{ trans('global.pleaseSelect') }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="item_quantity">
                                        {{ trans('cruds.donationItem.fields.quantity') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" id="item_quantity" name="item_quantity" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">
                                        {{ trans('cruds.donationAllocation.fields.allocated_amount') }}
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
                                <th>{{ trans('cruds.donation.fields.donation_type') }}</th>
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
                                    <td>
                                        @if(optional($allocation->donation)->donation_type === 'items')
                                            <span class="badge bg-info">{{ trans('cruds.donation.donation_type.items') }}</span>
                                        @else
                                            <span class="badge bg-success">{{ trans('cruds.donation.donation_type.money') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @if(optional($allocation->donation)->donation_type === 'items' && $allocation->items->count() > 0)
                                                <button type="button" 
                                                    class="btn btn-sm btn-outline-info me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#allocationItemsModal"
                                                    onclick="showAllocationItems({{ $allocation->id }})"
                                                    title="{{ trans('cruds.donationAllocation.extra.show_items') }}">
                                                    <i class="bi bi-box-seam"></i> {{ trans('cruds.donationAllocation.extra.show_items') }}
                                                </button>
                                            @endif
                                            @can('donation_allocation_delete')
                                                <form method="POST"
                                                    action="{{ route('admin.beneficiary-orders.donation-allocations.destroy', [$beneficiaryOrder->id, $allocation->id]) }}"
                                                    onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
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

{{-- Modal for showing allocation items --}}
<div class="modal fade" id="allocationItemsModal" tabindex="-1" aria-labelledby="allocationItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allocationItemsModalLabel">
                    {{ trans('cruds.donationAllocation.extra.allocation_items') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="allocation-items-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ trans('global.loading') }}...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('global.close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('stack-scripts')
    <script>
        const donationsData = @json($donationsData);
        
        // Debug: Log donations data to console
        console.log('=== DONATIONS DATA LOADED ===');
        console.log('Donations Data:', donationsData);
        console.log('Donations Data keys:', Object.keys(donationsData));
        console.log('Total donations:', Object.keys(donationsData).length);
        
        // Log each donation's type
        Object.keys(donationsData).forEach(key => {
            const d = donationsData[key];
            console.log(`Donation ${d.id}: type="${d.donation_type}", items=${d.items ? d.items.length : 0}`);
        });

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
                    const remainingQty = item.remaining_quantity !== undefined ? item.remaining_quantity : item.quantity;
                    html += `<li>${item.item_name} - Available: ${remainingQty} x ${item.unit_price.toFixed(2)}</li>`;
                });
                html += '</ul></div>';
            }
            container.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== DONATION ALLOCATION INITIALIZATION ===');
            
            const projectSelect = document.getElementById('allocation_project_id');
            const donationSelect = document.getElementById('donation_id');
            const moneyWrapper = document.getElementById('money-allocation-wrapper');
            const itemWrapper = document.getElementById('item-allocation-wrapper');
            const itemSelect = document.getElementById('allocation_item');
            const qtyInput = document.getElementById('item_quantity');
            const totalDisplay = document.getElementById('item_total_display');
            const moneyAmountInput = document.getElementById('money_allocated_amount');
            const allocatedAmountInput = document.getElementById('allocated_amount');
            const form = document.querySelector('form[action*="allocate-donation"]');

            // Log all found elements
            console.log('Elements found:', {
                projectSelect: !!projectSelect,
                donationSelect: !!donationSelect,
                moneyWrapper: !!moneyWrapper,
                itemWrapper: !!itemWrapper,
                itemSelect: !!itemSelect,
                qtyInput: !!qtyInput,
                moneyAmountInput: !!moneyAmountInput,
                allocatedAmountInput: !!allocatedAmountInput,
                form: !!form
            });

            // Log initial state of wrappers
            if (moneyWrapper) console.log('Money wrapper classes:', moneyWrapper.className);
            if (itemWrapper) console.log('Item wrapper classes:', itemWrapper.className);

            function updateMode(donationId) {
                console.log('=== updateMode CALLED ===');
                console.log('Donation ID:', donationId);
                console.log('Donation ID type:', typeof donationId);
                console.log('DonationsData keys:', Object.keys(donationsData));
                
                const pleaseSelectText = '{{ trans('global.pleaseSelect') }}';
                if (!donationId || !donationsData[donationId]) {
                    console.log('No donation selected or donation not found in data');
                    console.log('DonationId value:', donationId);
                    console.log('Donation exists in data:', !!donationsData[donationId]);
                    if (donationId) {
                        console.log('Available donation IDs:', Object.keys(donationsData).map(k => ({key: k, id: donationsData[k].id})));
                    }
                    moneyWrapper.classList.remove('d-none');
                    itemWrapper.classList.add('d-none');
                    // Clear item inputs
                    if (itemSelect) itemSelect.innerHTML = '<option value="">' + pleaseSelectText + '</option>';
                    if (qtyInput) qtyInput.value = '';
                    if (totalDisplay) totalDisplay.textContent = '0.00';
                    if (allocatedAmountInput) allocatedAmountInput.value = '';
                    // Make money input required, item inputs not required
                    if (moneyAmountInput) moneyAmountInput.required = true;
                    if (itemSelect) itemSelect.required = false;
                    if (qtyInput) qtyInput.required = false;
                    return;
                }
                const d = donationsData[donationId];
                console.log('=== DONATION DATA FOUND ===');
                console.log('Full donation data:', d);
                console.log('Donation type:', d.donation_type);
                console.log('Donation type check (=== "items"):', d.donation_type === 'items');
                console.log('Items array:', d.items);
                console.log('Items length:', d.items ? d.items.length : 0);
                
                if (d.donation_type === 'items') {
                    console.log('Setting mode to ITEMS');
                    console.log('Money wrapper before:', moneyWrapper.className);
                    console.log('Item wrapper before:', itemWrapper.className);
                    
                    moneyWrapper.classList.add('d-none');
                    itemWrapper.classList.remove('d-none');
                    
                    console.log('Money wrapper after:', moneyWrapper.className);
                    console.log('Item wrapper after:', itemWrapper.className);
                    console.log('Money wrapper has d-none:', moneyWrapper.classList.contains('d-none'));
                    console.log('Item wrapper has d-none:', itemWrapper.classList.contains('d-none'));

                    // Populate items dropdown
                    if (itemSelect) {
                        console.log('Populating items dropdown...');
                        itemSelect.innerHTML = '<option value="">' + pleaseSelectText + '</option>';
                        console.log('Items for donation:', d.items);
                        console.log('Items is array:', Array.isArray(d.items));
                        if (d.items && d.items.length > 0) {
                            console.log('Processing', d.items.length, 'items');
                            d.items.forEach((item, index) => {
                                console.log(`Item ${index}:`, item);
                                const remainingQty = item.remaining_quantity !== undefined ? item.remaining_quantity : item.quantity;
                                const option = document.createElement('option');
                                option.value = index;
                                option.textContent = `${item.item_name} (Available: ${remainingQty} x ${item.unit_price.toFixed(2)})`;
                                option.dataset.itemId = item.item_id;
                                option.dataset.remainingQty = remainingQty;
                                option.dataset.unitPrice = item.unit_price;
                                itemSelect.appendChild(option);
                                console.log(`Added option ${index}:`, option.textContent);
                            });
                            console.log('Populated', d.items.length, 'items in dropdown');
                            console.log('Dropdown now has', itemSelect.options.length, 'options');
                        } else {
                            console.warn('No items found for donation:', donationId, d);
                            console.warn('Items value:', d.items);
                            console.warn('Items length:', d.items ? d.items.length : 'N/A');
                        }
                    } else {
                        console.error('itemSelect element not found!');
                    }
                    // Reset values
                    if (qtyInput) {
                        qtyInput.value = '';
                        qtyInput.required = true;
                        qtyInput.disabled = false;
                        // Ensure name attribute exists
                        if (!qtyInput.hasAttribute('name')) {
                            qtyInput.setAttribute('name', 'item_quantity');
                        }
                    }
                    if (totalDisplay) totalDisplay.textContent = '0.00';
                    if (allocatedAmountInput) allocatedAmountInput.value = '';
                    if (moneyAmountInput) {
                        moneyAmountInput.value = '';
                        moneyAmountInput.required = false;
                        moneyAmountInput.disabled = true; // Disable money input for items
                    }
                    // Make item inputs required, money input not required
                    if (itemSelect) {
                        itemSelect.required = true;
                        itemSelect.disabled = false;
                        // Ensure name attribute exists
                        if (!itemSelect.hasAttribute('name')) {
                            itemSelect.setAttribute('name', 'allocation_item');
                        }
                    }
                } else {
                    console.log('Setting mode to MONEY (donation_type:', d.donation_type, ')');
                    moneyWrapper.classList.remove('d-none');
                    itemWrapper.classList.add('d-none');
                    // Clear item inputs
                    if (itemSelect) {
                        itemSelect.innerHTML = '<option value="">' + pleaseSelectText + '</option>';
                        itemSelect.required = false;
                        itemSelect.disabled = false; // Keep enabled but not required
                        // Restore name attribute if it was removed
                        if (!itemSelect.hasAttribute('name')) {
                            itemSelect.setAttribute('name', 'allocation_item');
                        }
                    }
                    if (qtyInput) {
                        qtyInput.value = '';
                        qtyInput.required = false;
                        qtyInput.disabled = false; // Keep enabled but not required
                        // Restore name attribute if it was removed
                        if (!qtyInput.hasAttribute('name')) {
                            qtyInput.setAttribute('name', 'item_quantity');
                        }
                    }
                    if (totalDisplay) totalDisplay.textContent = '0.00';
                    if (allocatedAmountInput) allocatedAmountInput.value = '';
                    // Make money input required, item inputs not required
                    if (moneyAmountInput) {
                        moneyAmountInput.required = true;
                        moneyAmountInput.disabled = false;
                    }
                }
                console.log('=== updateMode COMPLETE ===');
            }

            function recalcItemAmount() {
                const donationId = donationSelect ? donationSelect.value : '';
                if (!donationId || !donationsData[donationId]) {
                    if (totalDisplay) totalDisplay.textContent = '0.00';
                    if (allocatedAmountInput) allocatedAmountInput.value = '';
                    return;
                }
                const d = donationsData[donationId];
                const index = itemSelect ? itemSelect.value : '';
                if (!d.items || !d.items[index] || index === '') {
                    if (totalDisplay) totalDisplay.textContent = '0.00';
                    if (allocatedAmountInput) allocatedAmountInput.value = '';
                    return;
                }
                const item = d.items[index];
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                const remainingQty = selectedOption ? parseFloat(selectedOption.dataset.remainingQty || item.remaining_quantity || item.quantity) : (item.remaining_quantity || item.quantity);
                const unitPrice = selectedOption ? parseFloat(selectedOption.dataset.unitPrice || item.unit_price) : item.unit_price;
                
                let qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
                
                // Validate quantity doesn't exceed available
                if (qty > remainingQty) {
                    alert('Quantity exceeds available: ' + remainingQty);
                    if (qtyInput) qtyInput.value = remainingQty;
                    qty = remainingQty;
                }
                
                const total = qty * unitPrice;
                if (totalDisplay) totalDisplay.textContent = total.toFixed(2);
                if (allocatedAmountInput) allocatedAmountInput.value = total > 0 ? total.toFixed(2) : '';
            }

            // Update allocated_amount when money input changes
            if (moneyAmountInput) {
                moneyAmountInput.addEventListener('input', function() {
                    if (allocatedAmountInput) {
                        allocatedAmountInput.value = this.value || '';
                    }
                });
            }

            // Form submission handler to ensure correct allocated_amount is sent
            if (form) {
                form.addEventListener('submit', function(e) {
                    const donationId = donationSelect ? donationSelect.value : '';
                    if (!donationId || !donationsData[donationId]) return;
                    
                    const d = donationsData[donationId];
                    console.log('Form submission - donation type:', d.donation_type);
                    
                    if (d.donation_type === 'items') {
                        // For items, ensure allocated_amount is set from calculated total
                        if (!allocatedAmountInput || !allocatedAmountInput.value || parseFloat(allocatedAmountInput.value) <= 0) {
                            e.preventDefault();
                            alert('Please select an item and specify quantity');
                            return false;
                        }
                        // Disable money input so it's not submitted
                        if (moneyAmountInput) {
                            moneyAmountInput.disabled = true;
                        }
                        // Ensure item fields are enabled
                        if (itemSelect) itemSelect.disabled = false;
                        if (qtyInput) qtyInput.disabled = false;
                    } else {
                        // For money, ensure allocated_amount is set from money input
                        if (!moneyAmountInput || !moneyAmountInput.value || parseFloat(moneyAmountInput.value) <= 0) {
                            e.preventDefault();
                            alert('Please specify the allocated amount');
                            return false;
                        }
                        if (allocatedAmountInput) {
                            allocatedAmountInput.value = moneyAmountInput.value;
                        }
                        // Disable item fields so they're not submitted
                        if (itemSelect) {
                            itemSelect.disabled = true;
                            itemSelect.removeAttribute('name'); // Remove name so it's not submitted
                        }
                        if (qtyInput) {
                            qtyInput.disabled = true;
                            qtyInput.removeAttribute('name'); // Remove name so it's not submitted
                        }
                    }
                });
            }

            if (projectSelect) {
                projectSelect.addEventListener('change', function() {
                    populateDonationsByProject(this.value);
                    renderDonationDetails('');
                    updateMode('');
                });
            }

            if (donationSelect) {
                console.log('Donation select found, setting up...');
                console.log('Donation select element:', donationSelect);
                console.log('Donation select tagName:', donationSelect.tagName);
                console.log('Donation select id:', donationSelect.id);
                console.log('Donation select classes:', donationSelect.className);
                
                populateDonationsByProject(projectSelect ? projectSelect.value : null);
                
                // Wait for Select2 to initialize if it's being used
                const initSelect2Handler = () => {
                    if (window.jQuery && donationSelect.classList.contains('select2')) {
                        console.log('Select2 class detected, waiting for initialization...');
                        
                        // Wait a bit for Select2 to initialize
                        setTimeout(() => {
                            const $select = jQuery(donationSelect);
                            const select2Instance = $select.data('select2');
                            console.log('Select2 instance:', select2Instance);
                            
                            if (select2Instance) {
                                console.log('Select2 is initialized, attaching select2:select event');
                                
                                // Remove any existing handlers to avoid duplicates
                                $select.off('select2:select');
                                
                                // Attach Select2 event
                                $select.on('select2:select', function(e) {
                                    const selectedValue = e.params.data.id;
                                    console.log('=== SELECT2 CHANGE EVENT FIRED ===');
                                    console.log('Selected value:', selectedValue);
                                    console.log('Selected value type:', typeof selectedValue);
                                    console.log('Event params:', e.params);
                                    renderDonationDetails(selectedValue);
                                    updateMode(selectedValue);
                                });
                                
                                // Also listen for select2:change
                                $select.on('select2:change', function(e) {
                                    const selectedValue = $select.val();
                                    console.log('=== SELECT2 CHANGE EVENT (change) FIRED ===');
                                    console.log('Selected value:', selectedValue);
                                    renderDonationDetails(selectedValue);
                                    updateMode(selectedValue);
                                });
                            } else {
                                console.warn('Select2 not initialized yet, retrying...');
                                setTimeout(initSelect2Handler, 100);
                            }
                        }, 100);
                    } else {
                        console.log('Not using Select2, attaching native change event');
                        // Attach native change event
                        donationSelect.addEventListener('change', function() {
                            console.log('=== NATIVE CHANGE EVENT FIRED ===');
                            console.log('Selected value:', this.value);
                            console.log('Selected value type:', typeof this.value);
                            console.log('Selected text:', this.options[this.selectedIndex]?.text);
                            renderDonationDetails(this.value);
                            updateMode(this.value);
                        });
                    }
                };
                
                // Initialize mode based on current selection
                console.log('Initial donation select value:', donationSelect.value);
                updateMode(donationSelect.value);
                
                // Initialize event handlers
                initSelect2Handler();
                
                console.log('Event listeners setup initiated');
            } else {
                console.error('Donation select element not found!');
                // Initialize mode if no donation select exists
                updateMode('');
            }

            if (itemSelect && qtyInput) {
                itemSelect.addEventListener('change', recalcItemAmount);
                qtyInput.addEventListener('input', recalcItemAmount);
            }
        });

        // Function to show allocation items in modal
        function showAllocationItems(allocationId) {
            console.log('Showing items for allocation:', allocationId);
            const modalContent = document.getElementById('allocation-items-content');
            const modal = new bootstrap.Modal(document.getElementById('allocationItemsModal'));

            // Get allocation data from the page
            @php
                $allocationData = [];
                foreach ($beneficiaryOrder->donationAllocations->load('items.donationItem') as $allocation) {
                    $items = [];
                    foreach ($allocation->items as $item) {
                        $items[] = [
                            'item_name' => $item->donationItem->item_name,
                            'allocated_quantity' => $item->allocated_quantity,
                            'unit_price' => $item->donationItem->unit_price,
                            'allocated_amount' => $item->allocated_amount,
                        ];
                    }
                    $allocationData[$allocation->id] = [
                        'id' => $allocation->id,
                        'donation_id' => $allocation->donation_id,
                        'allocated_amount' => $allocation->allocated_amount,
                        'items' => $items,
                    ];
                }
            @endphp 
            const allocationData = @json($allocationData);

            const allocation = allocationData[allocationId];
            
            if (!allocation || !allocation.items || allocation.items.length === 0) {
                modalContent.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        لا توجد عناصر مخصصة
                    </div>
                `;
                modal.show();
                return;
            }
            
            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ trans('cruds.donationItem.fields.item_name') }}</th>
                                <th>{{ trans('cruds.donationItem.fields.quantity') }}</th>
                                <th>{{ trans('cruds.donationItem.fields.unit_price') }}</th>
                                <th>{{ trans('cruds.donationAllocation.fields.allocated_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            allocation.items.forEach(item => {
                html += `
                    <tr>
                        <td>${item.item_name}</td>
                        <td>${parseFloat(item.allocated_quantity).toFixed(2)}</td>
                        <td>${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td><strong>${parseFloat(item.allocated_amount).toFixed(2)}</strong></td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">المجموع:</th>
                                <th>${parseFloat(allocation.allocated_amount).toFixed(2)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
            
            modalContent.innerHTML = html;
            modal.show();
        }
    </script>
@endpush
