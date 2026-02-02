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
                </div>
            @else 
                @if($beneficiaryOrder->service_type == 'loan' && $beneficiaryOrder->serviceLoan->status == 'loan_paid')
                    @include('admin.beneficiaryOrders.partials.installments') 
                @else
                    @if ($beneficiaryOrder->accept_status == null)
                        <div class="alert alert-warning">
                            <i class="ri-alert-line"></i>
                            الطلب قيد المراجعة
                        </div>
                    @elseif ($beneficiaryOrder->accept_status == 'no')
                        <div class="alert alert-danger">
                            <i class="ri-alert-line"></i>
                            الطلب مرفوض
                            <br>
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiaryOrder.fields.refused_reason') }}
                                : </span>
                            {{ $beneficiaryOrder->refused_reason }}
                        </div>
                    @elseif ($beneficiaryOrder->accept_status == 'yes')
                        <div class="alert alert-success">
                            <i class="ri-alert-line"></i>
                            الطلب مقبول وقيد المتابعة
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </li>
</ul>

