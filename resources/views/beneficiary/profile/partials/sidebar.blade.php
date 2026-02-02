<div class="card custom-card overflow-hidden border">
    <div class="card-body border-bottom border-block-end-dashed">
        <div class="text-center">
            <span class="avatar avatar-xxl avatar-rounded  mb-3">
                <img src="{{ $user->photo ? $user->photo->getUrl('preview') : asset('assets/images/faces/11.jpg') }}"
                    alt="">
            </span>
            <h5 class="fw-medium mb-1">
                @if($beneficiary->profile_status == 'request_join') 
                    <i class="ri-loader-4-fill text-warning fs-14" data-bs-toggle="tooltip" title="طلب الانضمام"></i> 
                @elseif($beneficiary->profile_status == 'in_review') 
                    <i class="ri-loader-3-fill text-primary fs-14" data-bs-toggle="tooltip" title="قيد المراجعة"></i> 
                @elseif($beneficiary->profile_status == 'approved') 
                    <i class="ri-verified-badge-fill text-success fs-14" data-bs-toggle="tooltip" title="مقبول"></i> 
                @elseif($beneficiary->profile_status == 'rejected')
                    <i class="ri-indeterminate-circle-fill text-danger fs-14" data-bs-toggle="tooltip" title="مرفوض"></i> 
                @endif
                {{ $user->name }}
            </h5>
            <span class="d-block fw-medium text-muted mb-2">{{ $user->email }}</span>
            <p class="fs-12 mb-0 text-muted">
                <span class="me-3">
                    <i class="ri-map-pin-line me-1 align-middle"></i>
                    {{ $beneficiary->district->name ?? '' }}
                </span>
                <span>
                    <i class="ri-building-line me-1 align-middle"></i>
                    {{ $beneficiary->street }}</span>
            </p>
        </div>
    </div>
    <div class="d-flex mb-0 flex-wrap gap-3 p-3 border-bottom border-block-end-dashed">
        <div class="border-dashed rounded text-center flex-fill">
            <div class="main-card-icon mb-2 primary1">
                <div class="avatar avatar-sm bg-success">
                    <i class="fs-15 ti ti-check"></i>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-center align-items-end">
                <p class="fw-medium fs-20 mb-0">
                    {{ $beneficiary->beneficiaryBeneficiaryOrders->where('accept_status', 'yes')->count() }}
                </p>
                <p class="mb-1 text-muted">
                    {{ trans('cruds.beneficiary.profile.accepted_requests') }}</p>
            </div>
        </div>
        <div class="border-dashed rounded text-center flex-fill">
            <div class="main-card-icon mb-2 secondary">
                <div class="avatar avatar-sm bg-secondary">
                    <i class="fs-15 ti ti-x"></i>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-center align-items-end">
                <p class="fw-medium fs-20 mb-0">
                    {{ $beneficiary->beneficiaryBeneficiaryOrders->where('accept_status', 'no')->count() }}
                </p>
                <p class="mb-1 text-muted">
                    {{ trans('cruds.beneficiary.profile.rejected_requests') }}</p>
            </div>
        </div>
    </div>
    <div class="p-3 pb-1 d-flex flex-wrap justify-content-between">
        <div class="fw-medium fs-15 text-success">
            {{ trans('cruds.beneficiary.profile.basic_info') }}
        </div>
    </div>
    <div class="card-body border-bottom border-block-end-dashed p-0">
        <ul class="list-group list-group-flush">
            <li class="list-group-item pt-2 border-0 d-flex justify-content-between">
                <div><span class="fw-medium me-2">{{ trans('cruds.user.fields.name') }}
                        :</span><span class="text-muted">{{ $user->name }}</span></div>
                <div><span class="fw-medium me-2">{{ trans('cruds.user.fields.phone') }}
                        :</span><span class="text-muted">{{ $user->phone }}</span></div>
            </li>
            <li class="list-group-item pt-2 border-0 d-flex justify-content-between">
                <div><span class="fw-medium me-2">{{ trans('cruds.user.fields.identity_num') }}
                        :</span><span class="text-muted">{{ $user->identity_num }}</span>
                </div>
                <div><span class="fw-medium me-2">{{ trans('cruds.beneficiary.fields.nationality') }}
                        :</span><span class="text-muted">{{ $beneficiary->nationality->name ?? '-' }}</span>
                </div>
            </li>
            <li class="list-group-item pt-2 border-0 d-flex justify-content-between">
                <div><span class="fw-medium me-2">{{ trans('cruds.beneficiary.fields.marital_status') }}
                        :</span><span
                        class="badge bg-success-transparent">{{ $beneficiary->marital_status->name ?? '-' }}</span>
                </div> 
                <div><span class="fw-medium me-2">{{ trans('cruds.beneficiary.fields.profile_status') }}
                        :</span><span
                        class="badge bg-danger-transparent">{{ $beneficiary->profile_status ? \App\Models\Beneficiary::PROFILE_STATUS_SELECT[$beneficiary->profile_status] : '-' }}</span>
                </div>
            </li>
        </ul>
    </div>
</div>
