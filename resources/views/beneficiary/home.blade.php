@extends('layouts.master-beneficiary')

@section('styles')
<style>
    .pending-loan-card {
        border-left: 4px solid #ffc107;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .pending-loan-card .card-header {
        background-color: #fff3cd;
        border-bottom: 1px solid #ffeaa7;
    }
    
    .pending-loan-card .card-title {
        color: #856404;
        font-weight: 600;
    }
    
    .loan-actions {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    
    .loan-info-section {
        background-color: #ffffff;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .loan-info-section h6 {
        color: #495057;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }

    .quick-access-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .quick-access-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endsection

@section('content')
    <!-- Start::page-header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div> 
            <h1 class="page-title fw-medium fs-18 mb-0">{{ trans('global.dashboard') }}</h1>
        </div>
    </div>
    <!-- End::page-header -->

    <!-- Start::quick-access cards (same as sidebar) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('beneficiary.home') }}" class="text-decoration-none">
                <div class="card custom-card border-0 shadow-sm h-100 quick-access-card">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-house-door fs-1 text-primary mb-2 d-block"></i>
                        <span class="side-menu__label fw-medium text-default">{{ trans('global.dashboard') }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('beneficiary.profile.show') }}" class="text-decoration-none">
                <div class="card custom-card border-0 shadow-sm h-100 quick-access-card">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-person fs-1 text-primary mb-2 d-block"></i>
                        <span class="side-menu__label fw-medium text-default">{{ trans('global.profile') }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('admin.chats.index') }}" class="text-decoration-none">
                <div class="card custom-card border-0 shadow-sm h-100 quick-access-card">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-chat-left-text fs-1 text-primary mb-2 d-block"></i>
                        <span class="side-menu__label fw-medium text-default">{{ trans('cruds.chat.title') }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('beneficiary.beneficiary-orders.create', ['service_type' => 'social']) }}" class="text-decoration-none">
                <div class="card custom-card border-0 shadow-sm h-100 quick-access-card">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-plus fs-1 text-primary mb-2 d-block"></i>
                        <span class="side-menu__label fw-medium text-default">{{ trans('global.add') }} {{ trans('cruds.beneficiaryOrder.extra.title_singular') }}</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <a href="{{ route('beneficiary.beneficiary-orders.index') }}" class="text-decoration-none">
                <div class="card custom-card border-0 shadow-sm h-100 quick-access-card">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-file-earmark-text fs-1 text-primary mb-2 d-block"></i>
                        <span class="side-menu__label fw-medium text-default">{{ trans('global.list') }} {{ trans('cruds.beneficiaryOrder.extra.title') }}</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <!-- End::quick-access cards -->
    
    @if($beneficiary->profile_status == 'request_join')
        <div class="alert alert-warning">
            <i class="ri-loader-4-fill text-warning fs-14" data-bs-toggle="tooltip" title="طلب الانضمام"></i> 
            {{ trans('cruds.beneficiary.extra.request_join_text') }}
        </div>
    @elseif($beneficiary->profile_status == 'in_review')
        <div class="alert alert-primary">
            <i class="ri-loader-3-fill text-primary fs-14" data-bs-toggle="tooltip" title="قيد المراجعة"></i> 
            {{ trans('cruds.beneficiary.extra.in_review_text') }}
        </div> 
    @elseif($beneficiary->profile_status == 'rejected')
        <div class="alert alert-danger">
            <i class="ri-indeterminate-circle-fill text-danger fs-14" data-bs-toggle="tooltip" title="مرفوض"></i> 
            {{ trans('cruds.beneficiary.extra.rejected_text') }}
            <br>
            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.rejection_reason') }}
                : </span>
            {{ $beneficiary->rejection_reason }}
        </div>
    @endif

    <!-- Pending Loan Section -->
    @if($pendingLoan)
        <div class="row">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="ri-money-dollar-circle-line text-warning me-2"></i>
                            {{ trans('cruds.serviceLoan.fields.pending_loan') }}
                            ({{ $pendingLoan->group_name }})
                        </div>
                    </div>
                    <div class="card-body"> 
                        <form action="{{ route('beneficiary.loan.update') }}" method="POST" class="d-inline">
                            @csrf 
                            <input type="hidden" name="id" value="{{ $seriveloanMember->id }}">
                            <div class="row gy-4 mt-3"> 
                                @include('utilities.form.select', [
                                    'name' => 'project_type',
                                    'label' => 'cruds.serviceLoan.fields.project_type',
                                    'isRequired' => false,
                                    'options' => \App\Models\ServiceLoanMember::PROJECT_TYPE_SELECT,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.select', [
                                    'name' => 'project_location',
                                    'label' => 'cruds.serviceLoan.fields.project_location',
                                    'isRequired' => false,
                                    'options' => \App\Models\ServiceLoanMember::PROJECT_LOCATION_SELECT,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.select', [
                                    'name' => 'district_id',
                                    'label' => 'cruds.serviceLoan.fields.district',
                                    'isRequired' => false,
                                    'options' => $districts ?? ['' => trans('global.pleaseSelect')],
                                    'search' => true,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.text', [
                                    'name' => 'street',
                                    'label' => 'cruds.serviceLoan.fields.street',
                                    'isRequired' => false,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.date', [
                                    'name' => 'project_start_date',
                                    'id' => 'project_start_date',
                                    'label' => 'cruds.serviceLoan.fields.project_start_date',
                                    'isRequired' => false,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.text', [
                                    'name' => 'project_years_of_experience',
                                    'label' => 'cruds.serviceLoan.fields.project_years_of_experience',
                                    'isRequired' => false,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.select', [
                                    'name' => 'loan_id',
                                    'label' => 'cruds.serviceLoan.fields.loan',
                                    'isRequired' => false,
                                    'options' => $loans->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), ''),
                                    'grid' => 'col-md-6',
                                ])
                                @include('utilities.form.textarea', [
                                    'name' => 'project_short_description',
                                    'label' => 'cruds.serviceLoan.fields.project_short_description',
                                    'isRequired' => false,
                                    'grid' => 'col-md-12',
                                ])

                                @include('utilities.form.select', [
                                    'name' => 'project_financial_source',
                                    'label' => 'cruds.serviceLoan.fields.project_financial_source',
                                    'isRequired' => false,
                                    'options' => \App\Models\ServiceLoanMember::PROJECT_FINANCIAL_SOURCE_SELECT,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.text', [
                                    'name' => 'purpose_of_loan',
                                    'label' => 'cruds.serviceLoan.fields.purpose_of_loan',
                                    'isRequired' => false,
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.select', [
                                    'name' => 'has_previous_loan',
                                    'label' => 'cruds.serviceLoan.fields.has_previous_loan',
                                    'isRequired' => false,
                                    'options' => [
                                        '' => trans('global.pleaseSelect'),
                                        'yes' => trans('global.yes'),
                                        'no' => trans('global.no'),
                                    ],
                                    'grid' => 'col-md-3',
                                ])

                                @include('utilities.form.text', [
                                    'name' => 'previous_loan_number',
                                    'label' => 'cruds.serviceLoan.fields.previous_loan_number',
                                    'isRequired' => false,
                                    'grid' => 'col-md-3',
                                ])
                            </div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-center"> 
                                        <button type="submit" class="btn btn-success btn-lg px-4" name="status" value="approved">
                                            <i class="ri-check-line me-2"></i>
                                            {{ trans('cruds.serviceLoan.fields.accept_loan') }}
                                        </button>
                                    
                                        <button type="submit" class="btn btn-danger btn-lg px-4" name="status" value="rejected"
                                                onclick="return confirm('{{ trans('cruds.serviceLoan.fields.confirm_reject') }}')">
                                            <i class="ri-close-line me-2"></i>
                                            {{ trans('cruds.serviceLoan.fields.reject_loan') }}
                                        </button> 
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection 
