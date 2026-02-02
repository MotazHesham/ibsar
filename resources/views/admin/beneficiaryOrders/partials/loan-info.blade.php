<style>
    .info-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #e9ecef;
        border-color: #dee2e6;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .info-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
        line-height: 1.4;
        word-break: break-word;
    }

    .info-value .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        border: none;
    }

    .modal-header .btn-close {
        filter: invert(1);
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        border-top: 1px solid #e9ecef;
        padding: 20px 25px;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .card {
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
    }
</style>

@if (isset($beneficiaryOrder) && $beneficiaryOrder->serviceLoan)
    @php
        $serviceLoan = $beneficiaryOrder->serviceLoan;
    @endphp
    
    <div class="row g-3">
        @if ($beneficiaryOrder->serviceKeyName() == 'individual_loan')
            <div class="col-md-3">
                <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal"
                    data-bs-target="#projectInfoModal">
                    <i class="ti ti-users me-2"></i>
                    {{ trans('cruds.serviceLoan.fields.project_information') }}
                </button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#kafilInfoModal">
                    <i class="ti ti-user me-2"></i>
                    {{ trans('cruds.serviceLoan.fields.kafil_information') }}
                </button>
            </div>
        @elseif($beneficiaryOrder->serviceKeyName() == 'group_loan')
            <div class="col-md-3">
                <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal"
                    data-bs-target="#membersInfoModal">
                    <i class="ti ti-users me-2"></i>
                    {{ trans('cruds.serviceLoan.fields.members_information') }}
                </button>
            </div>
        @endif

        <div class="col-md-3">
            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                data-bs-target="#contactsDetailsModal">
                <i class="ti ti-address-book me-2"></i>
                {{ trans('cruds.serviceLoan.fields.contacts') }}
            </button>
        </div>
    </div>

    {{-- Kafil Information Modal --}}
    <div class="modal fade" id="kafilInfoModal" tabindex="-1" aria-labelledby="kafilInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kafilInfoModalLabel">
                        <i class="ti ti-user me-2"></i>
                        {{ trans('cruds.serviceLoan.fields.kafil_information') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_name') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_identity_num') }}
                                </div>
                                <div class="info-value">{{ $serviceLoan->kafil_identity_num ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.accommodation_type') }}
                                </div>
                                <div class="info-value">{{ $serviceLoan->accommodationType->name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.marital_status') }}</div>
                                <div class="info-value">{{ $serviceLoan->maritalStatus->name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">
                                    {{ trans('cruds.serviceLoan.fields.educational_qualification') }}</div>
                                <div class="info-value">{{ $serviceLoan->educationalQualification->name ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.job_type') }}</div>
                                <div class="info-value">{{ $serviceLoan->jobType->name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_district') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafilDistrict->name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_street') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_street ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_nearby_address') }}
                                </div>
                                <div class="info-value">{{ $serviceLoan->kafil_nearby_address ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_phone') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_phone ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_phone2') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_phone2 ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_work_phone') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_work_phone ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_work_address') }}
                                </div>
                                <div class="info-value">{{ $serviceLoan->kafil_work_address ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_email') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_email ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_work_name') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_work_name ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_mail_box') }}</div>
                                <div class="info-value">{{ $serviceLoan->kafil_mail_box ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-label">{{ trans('cruds.serviceLoan.fields.kafil_postal_code') }}
                                </div>
                                <div class="info-value">{{ $serviceLoan->kafil_postal_code ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ trans('global.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Project Information Modal --}}
    <div class="modal fade" id="projectInfoModal" tabindex="-1" aria-labelledby="projectInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectInfoModalLabel">
                        <i class="ti ti-users me-2"></i>
                        {{ trans('cruds.serviceLoan.fields.project_information') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $member = $serviceLoan->members->first();
                    @endphp
                    @if ($member)
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{ trans('global.name') }}</div>
                                    <div class="info-value">
                                        @if ($member->beneficiary)
                                            {{ $member->beneficiary->user->name ?? '-' }}
                                        @else
                                            {{ $member->name ?? '-' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{ trans('global.identity_number') }}
                                    </div>
                                    <div class="info-value">
                                        @if ($member->beneficiary)
                                            {{ $member->beneficiary->user->identity_num ?? '-' }}
                                        @else
                                            {{ $member->identity_number ?? '-' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">{{ trans('global.status') }}</div>
                                    <div class="info-value">
                                        @if ($member->status == 'pending')
                                            <span
                                                class="badge bg-warning">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                        @elseif($member->status == 'approved')
                                            <span
                                                class="badge bg-success">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                        @elseif($member->status == 'rejected')
                                            <span
                                                class="badge bg-danger">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $member->status ?? '-' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.member_position') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $member->member_position ? \App\Models\ServiceLoanMember::MEMBER_POSITION_SELECT[$member->member_position] : '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.project_type') }}</div>
                                    <div class="info-value">
                                        {{ $member->project_type ? \App\Models\ServiceLoanMember::PROJECT_TYPE_SELECT[$member->project_type] : '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.project_location') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $member->project_location ? \App\Models\ServiceLoanMember::PROJECT_LOCATION_SELECT[$member->project_location] : '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.district') }}</div>
                                    <div class="info-value">{{ $member->district->name ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.street') }}</div>
                                    <div class="info-value">{{ $member->street ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.project_start_date') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $member->project_start_date ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.project_years_of_experience') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $member->project_years_of_experience ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.loan') }}</div>
                                    <div class="info-value">{{ $member->amount ?? '-' }} <br>
                                        {{ trans('cruds.serviceLoan.fields.installment') }}
                                        {{ $member->installment ?? '-' }} <br>
                                        {{ trans('cruds.serviceLoan.fields.months') }}
                                        {{ $member->months ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.project_financial_source') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $member->project_financial_source ? \App\Models\ServiceLoanMember::PROJECT_FINANCIAL_SOURCE_SELECT[$member->project_financial_source] : '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.purpose_of_loan') }}
                                    </div>
                                    <div class="info-value">{{ $member->purpose_of_loan ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.has_previous_loan') }}
                                    </div>
                                    <div class="info-value">
                                        @if ($member->has_previous_loan == 'yes')
                                            <span class="badge bg-success">{{ trans('global.yes') }}</span>
                                        @elseif($member->has_previous_loan == 'no')
                                            <span class="badge bg-danger">{{ trans('global.no') }}</span>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.previous_loan_number') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $member->previous_loan_number ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-item">
                                    <div class="info-label">
                                        {{ trans('cruds.serviceLoan.fields.project_short_description') }}
                                    </div>
                                    <div class="info-value">
                                        {{ $member->project_short_description ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-users text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">{{ trans('global.no_members_found') }}</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ trans('global.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Members Information Modal --}}
    <div class="modal fade" id="membersInfoModal" tabindex="-1" aria-labelledby="membersInfoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="membersInfoModalLabel">
                        <i class="ti ti-users me-2"></i>
                        {{ trans('cruds.serviceLoan.fields.members_information') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($serviceLoan->members && $serviceLoan->members->count() > 0)
                        <div class="accordion" id="membersAccordion">
                            @foreach ($serviceLoan->members as $index => $member)
                                @php $collapseId = 'member-'.$index; @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $collapseId }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse-{{ $collapseId }}"
                                            aria-expanded="false" aria-controls="collapse-{{ $collapseId }}">
                                            {{ trans('global.member') }} #{{ $index + 1 }}
                                            <span class="ms-3 small text-muted">
                                                @if ($member->beneficiary)
                                                    {{ $member->beneficiary->user->name ?? '-' }}
                                                @else
                                                    {{ $member->name ?? '-' }}
                                                @endif
                                                •
                                                @if ($member->status == 'pending')
                                                    <span
                                                        class="badge bg-warning ms-1">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                                @elseif($member->status == 'approved')
                                                    <span
                                                        class="badge bg-success ms-1">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                                @elseif($member->status == 'rejected')
                                                    <span
                                                        class="badge bg-danger ms-1">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                                @else
                                                    <span
                                                        class="badge bg-secondary ms-1">{{ $member->status ?? '-' }}</span>
                                                @endif

                                                <span class="badge bg-dark">
                                                    {{ $member->amount ?? '-' }}
                                                </span>
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $collapseId }}" class="accordion-collapse collapse"
                                        aria-labelledby="heading-{{ $collapseId }}"
                                        data-bs-parent="#membersAccordion">
                                        <div class="accordion-body">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">{{ trans('global.name') }}</div>
                                                        <div class="info-value">
                                                            @if ($member->beneficiary)
                                                                {{ $member->beneficiary->user->name ?? '-' }}
                                                            @else
                                                                {{ $member->name ?? '-' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">{{ trans('global.identity_number') }}
                                                        </div>
                                                        <div class="info-value">
                                                            @if ($member->beneficiary)
                                                                {{ $member->beneficiary->user->identity_num ?? '-' }}
                                                            @else
                                                                {{ $member->identity_number ?? '-' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">{{ trans('global.status') }}</div>
                                                        <div class="info-value">
                                                            @if ($member->status == 'pending')
                                                                <span
                                                                    class="badge bg-warning">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                                            @elseif($member->status == 'approved')
                                                                <span
                                                                    class="badge bg-success">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                                            @elseif($member->status == 'rejected')
                                                                <span
                                                                    class="badge bg-danger">{{ \App\Models\ServiceLoanMember::STATUS_SELECT[$member->status] }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary">{{ $member->status ?? '-' }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.member_position') }}
                                                        </div>
                                                        <div class="info-value">
                                                            {{ $member->member_position ? \App\Models\ServiceLoanMember::MEMBER_POSITION_SELECT[$member->member_position] : '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.project_type') }}</div>
                                                        <div class="info-value">
                                                            {{ $member->project_type ? \App\Models\ServiceLoanMember::PROJECT_TYPE_SELECT[$member->project_type] : '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.project_location') }}
                                                        </div>
                                                        <div class="info-value">
                                                            {{ $member->project_location ? \App\Models\ServiceLoanMember::PROJECT_LOCATION_SELECT[$member->project_location] : '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.district') }}</div>
                                                        <div class="info-value">{{ $member->district->name ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.street') }}</div>
                                                        <div class="info-value">{{ $member->street ?? '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.project_start_date') }}
                                                        </div>
                                                        <div class="info-value">
                                                            {{ $member->project_start_date ?? '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.project_years_of_experience') }}
                                                        </div>
                                                        <div class="info-value">
                                                            {{ $member->project_years_of_experience ?? '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.loan') }}</div>
                                                        <div class="info-value">{{ $member->amount ?? '-' }} <br>
                                                            {{ trans('cruds.serviceLoan.fields.installment') }}
                                                            {{ $member->installment ?? '-' }} <br>
                                                            {{ trans('cruds.serviceLoan.fields.months') }}
                                                            {{ $member->months ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.project_financial_source') }}
                                                        </div>
                                                        <div class="info-value">
                                                            {{ $member->project_financial_source ? \App\Models\ServiceLoanMember::PROJECT_FINANCIAL_SOURCE_SELECT[$member->project_financial_source] : '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.purpose_of_loan') }}
                                                        </div>
                                                        <div class="info-value">{{ $member->purpose_of_loan ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.has_previous_loan') }}
                                                        </div>
                                                        <div class="info-value">
                                                            @if ($member->has_previous_loan == 'yes')
                                                                <span
                                                                    class="badge bg-success">{{ trans('global.yes') }}</span>
                                                            @elseif($member->has_previous_loan == 'no')
                                                                <span
                                                                    class="badge bg-danger">{{ trans('global.no') }}</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.previous_loan_number') }}
                                                        </div>
                                                        <div class="info-value">
                                                            {{ $member->previous_loan_number ?? '-' }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.serviceLoan.fields.project_short_description') }}
                                                        </div>
                                                        <div class="info-value">
                                                            {{ $member->project_short_description ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-users text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">{{ trans('global.no_members_found') }}</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ trans('global.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Contacts Details Modal --}}
    <div class="modal fade" id="contactsDetailsModal" tabindex="-1" aria-labelledby="contactsDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactsDetailsModalLabel">
                        <i class="ti ti-address-book me-2"></i>
                        {{ trans('cruds.serviceLoan.fields.contacts') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($serviceLoan->contacts && count(json_decode($serviceLoan->contacts)) > 0)
                        <div class="row g-3">
                            @foreach (json_decode($serviceLoan->contacts) as $index => $contact)
                                <div class="col-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">{{ trans('global.contact') }} #{{ $index + 1 }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-4">
                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">{{ trans('global.name') }}</div>
                                                        <div class="info-value">{{ $contact->name ?? '-' }}</div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">
                                                            {{ trans('cruds.beneficiaryFamily.fields.family_relationship') }}
                                                        </div>
                                                        <div class="info-value">
                                                            @php
                                                                $familyRelationship = \App\Models\FamilyRelationship::find(
                                                                    $contact->family_relationship_id ?? null,
                                                                );
                                                            @endphp
                                                            {{ $familyRelationship->name ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">{{ trans('global.phone') }}</div>
                                                        <div class="info-value">{{ $contact->phone ?? '-' }}</div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="info-item">
                                                        <div class="info-label">{{ trans('global.identity_num') }}
                                                        </div>
                                                        <div class="info-value">{{ $contact->identity_num ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="info-item">
                                                        <div class="info-label">{{ trans('global.address') }}</div>
                                                        <div class="info-value">{{ $contact->address ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-address-book text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">{{ trans('global.no_contacts_found') }}</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ trans('global.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info">
        <i class="ti ti-info-circle me-2"></i>
        {{ trans('global.no_loan_information_available') }}
    </div>
@endif
