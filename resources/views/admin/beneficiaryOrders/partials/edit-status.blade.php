<ul class="list-group list-group-flush border rounded-3 mt-3 shadow-sm">
    <li class="list-group-item p-3">
        <div class="card shadow-sm">
            <div class="ribbon-2 ribbon-secondary ribbon-left">
                <span class="ribbon-text">{{ trans('cruds.beneficiaryOrder.fields.status') }}</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-end gap-2 justify-content-between align-items-center flex-wrap mb-3">
                    <span></span>
                    <span
                        class="badge bg-{{ $beneficiaryOrder->status->badge_class ?? 'primary' }}-transparent">{{ $beneficiaryOrder->status->name ?? '' }}</span>
                </div>
            </div>
        </div>
        <div class="mt-5">
            @if ($beneficiaryOrder->done)
                <div class="text-center p-4">
                    <span class="avatar avatar-xl avatar-rounded bg-success-transparent svg-success">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256">
                            <rect width="256" height="256" fill="none" />
                            <circle cx="128" cy="128" r="96" opacity="0.2" />
                            <polyline points="88 136 112 160 168 104" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                            <circle cx="128" cy="128" r="96" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="16" />
                        </svg>
                    </span>
                    <h3 class="mt-2">Successful <span class="fs-14 align-middle">&#127881;</span></h3>
                    
                    @if($beneficiaryOrder->signature)
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewSignatureModal">
                                <i class="ri-eye-line me-1"></i>{{ trans('global.view_signature') }}
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            
            @if($beneficiaryOrder->service_type == 'loan' && $beneficiaryOrder->serviceLoan->status == 'loan_paid')
                @include('admin.beneficiaryOrders.partials.installments')
            @elseif(!$beneficiaryOrder->done)
                <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" class="p-3"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="signature" id="signatureData" value="{{ $beneficiaryOrder->signature ?? '' }}">
                    <div class="row">
                        @if (!$beneficiaryOrder->accept_status)
                            @include('utilities.form.radio', [
                                'name' => 'accept_status',
                                'label' => 'cruds.beneficiaryOrder.fields.accept_status',
                                'isRequired' => true,
                                'options' => [
                                    'yes' => 'الطلب مقبول',
                                    'no' => 'الطلب مرفوض',
                                ],
                                'value' => $beneficiaryOrder->accept_status,
                                'grid' => 'col-md-6',
                            ])
                            <div class="col-md-12" id="refused_reason_wrapper" style="display: none;">
                                @include('utilities.form.textarea', [
                                    'name' => 'refused_reason',
                                    'label' => 'cruds.beneficiaryOrder.fields.refused_reason',
                                    'isRequired' => false,
                                    'value' => $beneficiaryOrder->refused_reason,
                                ])
                            </div>
                        @else
                            @include('utilities.form.select', [
                                'name' => 'status_id',
                                'label' => 'cruds.beneficiaryOrder.fields.status',
                                'isRequired' => false,
                                'grid' => 'col-md-6',
                                'options' => $statuses,
                                'value' => $beneficiaryOrder->status_id,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'specialist_id',
                                'label' => 'cruds.beneficiaryOrder.fields.specialist',
                                'isRequired' => false,
                                'grid' => 'col-md-6',
                                'options' => $specialists,
                                'search' => true,
                                'value' => $beneficiaryOrder->specialist_id,
                            ])
                            @include('utilities.form.textarea', [
                                'name' => 'note',
                                'label' => 'cruds.beneficiaryOrder.fields.note',
                                'isRequired' => false,
                                'value' => $beneficiaryOrder->note,
                            ]) 
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary  w-20 mt-3">
                        {{ trans('global.update') }}
                    </button>
                    @if($beneficiaryOrder->service_type == 'loan' && $beneficiaryOrder->accept_status == 'yes')
                        <button type="button" class="btn btn-success w-20 mt-3" id="loan-pay-btn">
                            {{ trans('cruds.beneficiaryOrder.extra.loan_pay') }}
                        </button>
                        <div id="installment-date-wrapper" style="display: none;" class="mt-3"> 
                            @include('utilities.form.date', [
                                'name' => 'installment_date',
                                'id' => 'installment_date',
                                'label' => 'cruds.serviceLoanInstallment.fields.installment_date',
                                'isRequired' => false,
                                'grid' => '',
                            ])
                            <button type="submit" class="btn btn-primary w-20 mt-3" name="loan-pay" value="1">
                                {{ trans('global.finish') }}
                            </button>
                        </div>
                    @else
                        @if($beneficiaryOrder->accept_status == 'yes') 
                            <button @if(getSetting('enable_get_signature_from_beneficiary') == 'yes') data-bs-toggle="modal" data-bs-target="#signatureModal" type="button" @else type="submit" @endif class="btn btn-secondary  w-20 mt-3" name="finish" value="1">
                                {{ trans('global.finish') }} {{ trans('cruds.beneficiaryOrder.title_singular') }}
                            </button>
                        @endif
                    @endif
                    
                    @include('admin.beneficiaryOrders.partials.signature-modal') 
                </form> 
            @endif
            
            <!-- View Signature Modal -->
            @if($beneficiaryOrder->signature)
                <div class="modal fade" id="viewSignatureModal" tabindex="-1" aria-labelledby="viewSignatureModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewSignatureModalLabel">
                                    <i class="ri-eye-line me-2"></i>{{ trans('global.view_signature') }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <div class="signature-view-container">
                                    <img src="{{ $beneficiaryOrder->signature }}" alt="Signature" class="signature-display-image" style="max-width: 100%; max-height: 400px; border: 1px solid #ddd; border-radius: 8px;">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="ri-close-line me-1"></i>{{ trans('global.close') }}
                                </button>
                                <a href="{{ route('admin.beneficiary-orders.signature-download', $beneficiaryOrder->id) }}" target="_blank" class="btn btn-primary">
                                    <i class="ri-download-line me-1"></i>{{ trans('global.download') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </li>
</ul>

@section('scripts')
    @parent
    <script>
        handleRejectionReasonToggle('{{ $beneficiaryOrder->accept_status }}');

        function handleRejectionReasonToggle(value) {
            var RejectionReasonWrapper = document.getElementById('refused_reason_wrapper');
            if(RejectionReasonWrapper){
                if (value === 'no') {
                    RejectionReasonWrapper.style.display = 'block';
                } else {
                    RejectionReasonWrapper.style.display = 'none';
                    document.getElementById('refused_reason').value = '';
                }
            }
        }
        var StatusSelect = document.getElementsByName('accept_status');
        if (StatusSelect.length > 0) {
            StatusSelect.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    handleRejectionReasonToggle(this.value);
                });
            });
        }
        var loanPayBtn = document.getElementById('loan-pay-btn');
        if (loanPayBtn) {
            loanPayBtn.addEventListener('click', function() {
                document.getElementById('installment-date-wrapper').style.display = 'block';
            });
        }
    </script>
@endsection
