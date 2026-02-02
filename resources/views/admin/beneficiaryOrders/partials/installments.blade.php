<div class="card">
    <div class="card-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="loanTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="installments-tab" data-bs-toggle="tab" data-bs-target="#installments"
                    type="button" role="tab" aria-controls="installments" aria-selected="true">
                    {{ trans('cruds.serviceLoanInstallment.title') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments"
                    type="button" role="tab" aria-controls="payments" aria-selected="false">
                    {{ trans('cruds.serviceLoanPayment.title') }}
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="loanTabsContent">
            <!-- Installments Tab -->
            <div class="tab-pane fade show active" id="installments" role="tabpanel" aria-labelledby="installments-tab">
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>{{ trans('cruds.serviceLoanInstallment.title') }}</h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-info">المبلغ الإجمالي:
                                {{ number_format($beneficiaryOrder->serviceLoan->amount, 2) }} ريال</span>
                            <span class="badge bg-success">المدة: {{ $beneficiaryOrder->serviceLoan->months }}
                                شهر</span>
                            <span class="badge bg-warning">القسط الشهري:
                                {{ number_format($beneficiaryOrder->serviceLoan->installment, 2) }} ريال</span>
                        </div>
                    </div>

                    @if ($beneficiaryOrder->serviceLoan->installments->count() > 0)
                        <!-- Payment Progress -->
                        <div class="mb-3">
                            @php
                                $paymentPercentage =
                                    $beneficiaryOrder->serviceLoan->amount > 0
                                        ? ($beneficiaryOrder->serviceLoan->total_paid /
                                                $beneficiaryOrder->serviceLoan->amount) *
                                            100
                                        : 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">تقدم السداد</span>
                                <span class="fw-bold">{{ number_format($paymentPercentage, 1) }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $paymentPercentage }}%" aria-valuenow="{{ $paymentPercentage }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">مدفوع:
                                    {{ number_format($beneficiaryOrder->serviceLoan->total_paid, 2) }} ريال</small>
                                <small class="text-muted">متبقي:
                                    {{ number_format($beneficiaryOrder->serviceLoan->remaining_amount, 2) }}
                                    ريال</small>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>رقم القسط</th>
                                        <th>{{ trans('cruds.serviceLoanInstallment.fields.installment') }}</th>
                                        <th>{{ trans('cruds.serviceLoanInstallment.fields.installment_date') }}</th>
                                        <th>{{ trans('cruds.serviceLoanInstallment.fields.paid_amount') }}</th>
                                        <th>{{ trans('cruds.serviceLoanInstallment.fields.payment_status') }}</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($beneficiaryOrder->serviceLoan->installments as $installment)
                                        <tr
                                            class="{{ $installment->isOverdue() ? 'table-danger' : ($installment->isPaid() ? 'table-success' : 'table-warning') }}">
                                            <td>{{ $installment->id }}</td>
                                            <td>{{ $installment->installment }}</td>
                                            <td>{{ $installment->installment_date }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <small class="text-muted">المطلوب:
                                                        {{ number_format($installment->installment_amount, 2) }}
                                                        ريال</small>
                                                    <strong>المدفوع:
                                                        {{ number_format($installment->paid_amount ?? 0, 2) }}
                                                        ريال</strong>
                                                    @if ($installment->remaining_amount > 0)
                                                        <small class="text-danger">المتبقي:
                                                            {{ number_format($installment->remaining_amount, 2) }}
                                                            ريال</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if ($installment->payment_status == 'paid')
                                                    <span class="badge bg-success">مدفوع</span>
                                                @elseif($installment->payment_status == 'pending')
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                @else
                                                    <span class="badge bg-danger">غير مدفوع</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($installment->isOverdue())
                                                    <span class="badge bg-danger">متأخر</span>
                                                @elseif($installment->isPaid())
                                                    <span class="badge bg-success">مكتمل</span>
                                                @else
                                                    <span class="badge bg-warning">قيد الانتظار</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            لا توجد أقساط مضافة بعد. سيتم إنشاء الأقساط عند تحديث حالة الطلب.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payments Tab -->
            <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>{{ trans('cruds.serviceLoanPayment.title') }}</h5>
                        @if (!$beneficiaryOrder->done)
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addPaymentModal">
                                <i class="fas fa-plus"></i> إضافة دفعة
                            </button>
                        @endif
                    </div>

                    <!-- Payment Summary -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6>إجمالي المدفوع</h6>
                                    <h4>{{ number_format($beneficiaryOrder->serviceLoan->total_paid, 2) }} ريال</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>المتبقي</h6>
                                    <h4>{{ number_format($beneficiaryOrder->serviceLoan->remaining_amount, 2) }} ريال
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6>الأقساط المدفوعة</h6>
                                    <h4>{{ $beneficiaryOrder->serviceLoan->installments->where('payment_status', 'paid')->count() }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>الأقساط المتبقية</h6>
                                    <h4>{{ $beneficiaryOrder->serviceLoan->installments->where('payment_status', '!=', 'paid')->count() }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم الدفعة</th>
                                    <th>{{ trans('cruds.serviceLoanPayment.fields.payment_method') }}</th>
                                    <th>{{ trans('cruds.serviceLoanPayment.fields.payment_reference_number') }}</th>
                                    <th>{{ trans('cruds.serviceLoanPayment.fields.paid_date') }}</th>
                                    <th>المبلغ</th>
                                    <th>{{ trans('cruds.serviceLoanPayment.fields.payment_status') }}</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($beneficiaryOrder->serviceLoan->payments()->orderBy('id', 'desc')->get() as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ \App\Models\ServiceLoanPayment::SELECT_PAYMENT_METHOD[$payment->payment_method] ?? '' }}
                                        </td>
                                        <td>{{ $payment->payment_reference_number }}</td>
                                        <td>{{ $payment->paid_date }}</td>
                                        <td>{{ number_format($payment->amount ?? 0, 2) }} ريال</td>
                                        <td>
                                            @if ($payment->payment_status == 'paid')
                                                <span class="badge bg-success">مدفوع</span>
                                            @elseif($payment->payment_status == 'pending') 
                                                <span class="badge bg-info">طلب موافقة الأخصائية</span>
                                            @elseif($payment->payment_status == 'approved_specialist')
                                                <span class="badge bg-success">تم الموافقة</span>
                                            @elseif($payment->payment_status == 'rejected')
                                                <span class="badge bg-danger"
                                                    @if ($payment->rejection_reason) title="سبب الرفض: {{ $payment->rejection_reason }}"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top" @endif>
                                                    مرفوض
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info"
                                                    onclick="viewPayment({{ $payment->id }})" title="عرض التفاصيل">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                @if (auth()->user()->user_type == 'staff')
                                                    @if ($payment->payment_status == 'pending')
                                                        <button type="button" class="btn btn-sm btn-success"
                                                            onclick="acceptSpecialist({{ $payment->id }})"
                                                            title="قبول الأخصائية">
                                                            <i class="ri-check-line"></i> قبول الأخصائية
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="rejectPayment({{ $payment->id }})"
                                                            title="رفض الدفعة">
                                                            <i class="ri-close-line"></i> رفض
                                                        </button>
                                                    @elseif ($payment->payment_status == 'approved_specialist')
                                                        @if (!$beneficiaryOrder->done)
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                onclick="acceptPayment({{ $payment->id }})"
                                                                title="قبول الدفعة">
                                                                <i class="ri-check-line"></i> قبول
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="rejectPayment({{ $payment->id }})"
                                                                title="رفض الدفعة">
                                                                <i class="ri-close-line"></i> رفض
                                                            </button>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد مدفوعات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">إضافة دفعة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPaymentForm" action="{{ route('service-loan-payments.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="service_loan_id" value="{{ $beneficiaryOrder->serviceLoan->id }}">

                <div class="modal-body">
                    <div class="row">
                        @include('utilities.form.select', [
                            'name' => 'payment_method',
                            'label' => 'cruds.serviceLoanPayment.fields.payment_method',
                            'isRequired' => true,
                            'grid' => 'col-md-6',
                            'options' => \App\Models\ServiceLoanPayment::SELECT_PAYMENT_METHOD,
                        ])
                        @include('utilities.form.text', [
                            'name' => 'payment_reference_number',
                            'label' => 'cruds.serviceLoanPayment.fields.payment_reference_number',
                            'isRequired' => true,
                            'grid' => 'col-md-6',
                        ])

                        @include('utilities.form.date', [
                            'name' => 'paid_date',
                            'id' => 'paid_date',
                            'label' => 'cruds.serviceLoanPayment.fields.paid_date',
                            'isRequired' => true,
                            'grid' => 'col-md-6',
                        ])
                        <div class="col-md-6">
                            @include('utilities.form.text', [
                                'name' => 'amount',
                                'label' => 'cruds.serviceLoanPayment.fields.amount',
                                'type' => 'number',
                                'isRequired' => true,
                                'grid' => '',
                                'helperBlock' =>
                                    'المبلغ المتبقي: ' .
                                    number_format($beneficiaryOrder->serviceLoan->remaining_amount, 2) .
                                    ' ريال',
                            ])
                            <!-- Payment Preview -->
                            <div id="payment-preview" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                <small class="text-muted">معاينة الدفعة:</small>
                                <div id="installment-preview-list"></div>
                            </div>
                        </div>
                        @include('utilities.form.textarea', [
                            'name' => 'note',
                            'label' => 'cruds.serviceLoanPayment.fields.note',
                            'isRequired' => false,
                            'grid' => 'col-md-12',
                            'editor' => false,
                        ])
                        @include('utilities.form.dropzone', [
                            'name' => 'payment_receipt',
                            'id' => 'payment_receipt',
                            'label' => 'cruds.serviceLoanPayment.fields.payment_receipt',
                            'isRequired' => false,
                            'grid' => 'col-md-12',
                            'url' => route('service-loan-payments.storeMedia'),
                        ])
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ الدفعة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Payment Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-labelledby="viewPaymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPaymentModalLabel">تفاصيل الدفعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="paymentDetails">
                <!-- Payment details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle amount validation
        const amountInput = document.getElementById('amount');
        const maxAmount = {{ $beneficiaryOrder->serviceLoan->remaining_amount ?? 0 }};

        // Check if amountInput exists
        if (!amountInput) {
            console.error('Amount input element not found');
            return;
        }

        amountInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value > maxAmount) {
                this.setCustomValidity(`المبلغ لا يمكن أن يتجاوز ${maxAmount.toFixed(2)} ريال`);
            } else {
                this.setCustomValidity('');
            }

            // Show payment preview
            if (value > 0 && value <= maxAmount) {
                showPaymentPreview(value);
            } else {
                hidePaymentPreview();
            }
        });

        // Handle form submission
        const addPaymentForm = document.getElementById('addPaymentForm');
        if (!addPaymentForm) {
            console.error('Add payment form not found');
            return;
        }

        addPaymentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const amount = parseFloat(formData.get('amount'));

            if (amount > maxAmount) {
                alert(`المبلغ لا يمكن أن يتجاوز ${maxAmount.toFixed(2)} ريال`);
                return;
            }

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn) {
                console.error('Submit button not found');
                return;
            }

            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
            submitBtn.disabled = true;

            // Submit via AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                'content');
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('خطأ في الأمان. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                return;
            }

            $.ajax({
                url: this.action,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(data) {
                    if (data.success) {
                        // Show success message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            }).then(() => {
                                // Reload page to show updated data
                                location.reload();
                            });
                        } else {
                            alert('تم بنجاح! ' + data.message);
                            location.reload();
                        }
                    } else {
                        // Show error message
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            });
                        } else {
                            alert('خطأ! ' + data.message);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء معالجة الدفعة',
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        alert('حدث خطأ أثناء معالجة الدفعة');
                    }
                },
                complete: function() {
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        });
    });

    function showPaymentPreview(amount) {
        // Validate input
        if (!amount || isNaN(amount) || amount <= 0) {
            console.warn('Invalid amount provided to showPaymentPreview:', amount);
            return;
        }

        const previewDiv = document.getElementById('payment-preview');
        const previewList = document.getElementById('installment-preview-list');

        // Check if DOM elements exist
        if (!previewDiv || !previewList) {
            console.error('Required DOM elements not found for payment preview');
            return;
        }

        // Get unpaid installments and ensure it's always an array
        let unpaidInstallments = @json($beneficiaryOrder->serviceLoan->installments->where('payment_status', '!=', 'paid') ?? collect());

        console.log('Raw unpaidInstallments:', unpaidInstallments);
        console.log('Type:', typeof unpaidInstallments);
        console.log('Is Array:', Array.isArray(unpaidInstallments));

        try {
            // Convert object to array if it's not already an array
            if (unpaidInstallments && typeof unpaidInstallments === 'object' && !Array.isArray(unpaidInstallments)) {
                unpaidInstallments = Object.values(unpaidInstallments);
            }

            // Handle null or undefined cases
            if (!unpaidInstallments) {
                unpaidInstallments = [];
            }

            // Final fallback: ensure it's always an array
            if (!Array.isArray(unpaidInstallments)) {
                unpaidInstallments = [];
            }
        } catch (error) {
            console.error('Error processing unpaidInstallments:', error);
            unpaidInstallments = [];
        }
        let remainingAmount = amount;
        let previewHTML = '';

        // Ensure unpaidInstallments is an array and has items
        if (!unpaidInstallments || !Array.isArray(unpaidInstallments) || unpaidInstallments.length === 0) {
            previewHTML = '<div class="text-muted">لا توجد أقساط متبقية</div>';
            previewList.innerHTML = previewHTML;
            previewDiv.style.display = 'block';
            return;
        }

        unpaidInstallments.forEach((installment, index) => {
            const installmentAmount = {{ $beneficiaryOrder->serviceLoan->installment ?? 0 }};
            const currentPaid = parseFloat(installment.paid_amount) || 0;
            const remainingInstallment = installmentAmount - currentPaid;

            if (remainingAmount > 0 && remainingInstallment > 0) {
                const amountToPay = Math.min(remainingAmount, remainingInstallment);
                const status = amountToPay >= remainingInstallment ? 'مكتمل' : 'جزئي';
                const statusClass = amountToPay >= remainingInstallment ? 'text-success' : 'text-warning';

                previewHTML += `
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span>القسط ${installment.id}: ${installment.installment_date}</span>
                    <span class="${statusClass}">${amountToPay.toFixed(2)} ريال - ${status}</span>
                </div>
            `;

                remainingAmount -= amountToPay;
            }
        });

        if (remainingAmount > 0) {
            previewHTML += `
            <div class="d-flex justify-content-between align-items-center py-1">
                <span class="text-info">متبقي:</span>
                        <span class="text-info">${remainingAmount.toFixed(2)} ريال</span>
            </div>
        `;
        }

        previewList.innerHTML = previewHTML;
        previewDiv.style.display = 'block';
    }

    function hidePaymentPreview() {
        const previewDiv = document.getElementById('payment-preview');
        if (previewDiv) {
            previewDiv.style.display = 'none';
        }
    }

    function viewPayment(paymentId) {
        // Validate input
        if (!paymentId || isNaN(paymentId)) {
            console.error('Invalid payment ID provided to viewPayment:', paymentId);
            return;
        }

        // Load payment details via AJAX
        fetch(`/service-loan-payments/${paymentId}`)
            .then(response => response.json())
            .then(data => {
                const paymentDetails = document.getElementById('paymentDetails');
                if (!paymentDetails) {
                    console.error('Payment details element not found');
                    return;
                }

                paymentDetails.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>طريقة الدفع:</strong> ${data.payment_method}
                    </div>
                    <div class="col-md-6">
                        <strong>رقم المرجع:</strong> ${data.payment_reference_number || 'غير محدد'}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <strong>تاريخ الدفع:</strong> ${data.paid_date}
                    </div>
                    <div class="col-md-6">
                        <strong>المبلغ:</strong> ${data.amount} ريال
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <strong>الملاحظات:</strong> ${data.note || 'لا توجد ملاحظات'}
                    </div>
                </div> 

                ${data.payment_receipt ? `<div class="row mt-2"><div class="col-12"><strong>إيصال الدفع:</strong><br><img src="${data.payment_receipt}" class="img-fluid mt-2" style="max-width: 200px;"></div></div>` : ''}
            `;

                const modalElement = document.getElementById('viewPaymentModal');
                if (modalElement && typeof bootstrap !== 'undefined') {
                    new bootstrap.Modal(modalElement).show();
                } else {
                    console.error('Modal element or Bootstrap not available');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحميل تفاصيل الدفعة');
            });
    }

    function acceptPayment(paymentId) {
        // Use SweetAlert if available, otherwise use confirm
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'تأكيد قبول الدفعة',
                text: 'هل أنت متأكد من قبول هذه الدفعة؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، قبول',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    processAcceptPayment(paymentId);
                }
            });
            return;
        }

        if (!confirm('هل أنت متأكد من قبول هذه الدفعة؟')) {
            return;
        }

        processAcceptPayment(paymentId);
    }

    function acceptSpecialist(paymentId) {
        // Use SweetAlert if available, otherwise use confirm
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'تأكيد قبول البيانات',
                text: 'هل أنت متأكد من قبول بيانات هذه الدفعة؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، قبول',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    processAcceptSpecialist(paymentId);
                }
            });
            return;
        }

        if (!confirm('هل أنت متأكد من قبول هذه الدفعة؟')) {
            return;
        }

        processAcceptSpecialist(paymentId);
    }

    function processAcceptSpecialist(paymentId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                alert('خطأ في الأمان. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                return;
            }

            // Show loading state
            const acceptBtn = document.querySelector(`button[onclick="acceptSpecialist(${paymentId})"]`);
            if (!acceptBtn) {
                console.error('Accept button not found');
                return;
            }

            const originalText = acceptBtn.innerHTML;
            acceptBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            acceptBtn.disabled = true;

            fetch(`/service-loan-payments/${paymentId}/accept-specialist`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert('تم القبول بنجاح!');
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            });
                        } else {
                            alert('خطأ: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء قبول الأخصائية',
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        alert('حدث خطأ أثناء قبول الأخصائية');
                    }
                })
                .finally(() => {
                    // Reset button state
                    acceptBtn.innerHTML = originalText;
                    acceptBtn.disabled = false;
                });
        }


        function processAcceptPayment(paymentId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                alert('خطأ في الأمان. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                return;
            }

            // Show loading state
            const acceptBtn = document.querySelector(`button[onclick="acceptPayment(${paymentId})"]`);
            if (!acceptBtn) {
                console.error('Accept button not found');
                return;
            }

            const originalText = acceptBtn.innerHTML;
            acceptBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            acceptBtn.disabled = true;

            fetch(`/service-loan-payments/${paymentId}/accept`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert('تم قبول الدفعة بنجاح!');
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            });
                        } else {
                            alert('خطأ: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء قبول الدفعة',
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        alert('حدث خطأ أثناء قبول الدفعة');
                    }
                })
                .finally(() => {
                    // Reset button state
                    acceptBtn.innerHTML = originalText;
                    acceptBtn.disabled = false;
                });
        }

        function rejectPayment(paymentId) {
            // Use SweetAlert if available, otherwise use prompt
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'رفض الدفعة',
                    text: 'يرجى إدخال سبب رفض الدفعة:',
                    input: 'text',
                    inputPlaceholder: 'سبب الرفض...',
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return 'يجب إدخال سبب الرفض';
                        }
                    },
                    showCancelButton: true,
                    confirmButtonText: 'رفض',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        processRejectPayment(paymentId, result.value);
                    }
                });
                return;
            }

            const reason = prompt('يرجى إدخال سبب رفض الدفعة:');
            if (reason === null) return; // User cancelled
            if (reason.trim() === '') {
                alert('يجب إدخال سبب الرفض');
                return;
            }

            processRejectPayment(paymentId, reason);
        }

        function processRejectPayment(paymentId, reason) {

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                alert('خطأ في الأمان. يرجى تحديث الصفحة والمحاولة مرة أخرى.');
                return;
            }

            // Show loading state
            const rejectBtn = document.querySelector(`button[onclick="rejectPayment(${paymentId})"]`);
            if (!rejectBtn) {
                console.error('Reject button not found');
                return;
            }

            const originalText = rejectBtn.innerHTML;
            rejectBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            rejectBtn.disabled = true;

            fetch(`/service-loan-payments/${paymentId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        rejection_reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            alert('تم رفض الدفعة بنجاح!');
                            location.reload();
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ!',
                                text: data.message,
                                confirmButtonText: 'حسناً'
                            });
                        } else {
                            alert('خطأ: ' + data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء رفض الدفعة',
                            confirmButtonText: 'حسناً'
                        });
                    } else {
                        alert('حدث خطأ أثناء رفض الدفعة');
                    }
                })
                .finally(() => {
                    // Reset button state
                    rejectBtn.innerHTML = originalText;
                    rejectBtn.disabled = false;
                });
        }
</script>
