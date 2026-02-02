
<div class="offcanvas-body p-0">
    <div class="d-flex align-items-top p-3 border-bottom border-block-end-dashed main-profile-cover">
        <span class="avatar avatar-xxl avatar-rounded me-3 flex-shrink-0">
            <img src="{{ $beneficiaryFamily->photo ? $beneficiaryFamily->photo->getUrl('preview') : asset('assets/images/faces/11.jpg') }}" alt="">
        </span>
        <div class="flex-fill main-profile-info">
            <div class="d-flex align-items-start justify-content-between">
                <h6 class="fw-medium mb-1">
                    {{ $beneficiaryFamily->name }}
                    <span class="badge bg-success-transparent fs-10"><i class="ri-circle-fill fs-8 text-success me-1 "></i> {{ App\Models\BeneficiaryFamily::GENDER_SELECT[$beneficiaryFamily->gender] ?? '' }}</span>
                </h6>
                <button type="button" class="btn-close crm-contact-close-btn" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <p class="mb-2 text-muted fs-12">{{ $beneficiaryFamily->family_relationship->name ?? '' }}</p>
            <div class="d-flex gap-2 fs-15 mt-1">
                <a href="tel:{{ $beneficiaryFamily->phone }}" class="btn btn-icon btn-sm rounded-pill btn-success-light"><i class="ri-phone-line"></i></a>
                <a href="mailto:{{ $beneficiaryFamily->email }}" class="btn btn-icon btn-sm rounded-pill btn-info-light"><i class="ri-mail-line"></i></a>
                <a href="sms:{{ $beneficiaryFamily->phone }}" class="btn btn-icon btn-sm rounded-pill btn-secondary-light"><i class="ri-message-line"></i></a> 
            </div>
        </div>
    </div>  
    <div class="p-3 border-bottom border-block-end-dashed">
        <p class="fs-14 mb-2 fw-medium">Contact Information :</p>
        <div class="">
            <div class="d-flex align-items-center mb-2">
                <div class="me-2">
                    <span class="avatar avatar-sm avatar-rounded bg-success-transparent">
                        <i class="ri-mail-line align-middle fs-14"></i>
                    </span>
                </div>
                <div>
                    {{ $beneficiaryFamily->email }} 
                </div>
            </div>
            <div class="d-flex align-items-center mb-2">
                <div class="me-2">
                    <span class="avatar avatar-sm avatar-rounded bg-info-transparent">
                        <i class="ri-phone-line align-middle fs-14"></i>
                    </span>
                </div>
                <div>
                    {{ $beneficiaryFamily->phone }}
                </div>
            </div>                           
        </div>
    </div> 
    <table class="table table-bordered">
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.identity_num') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ $beneficiaryFamily->identity_num }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.dob') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ $beneficiaryFamily->dob }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.gender') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ App\Models\BeneficiaryFamily::GENDER_SELECT[$beneficiaryFamily->gender] ?? '' }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.family_relationship') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ $beneficiaryFamily->family_relationship->name ?? '' }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.marital_status') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ $beneficiaryFamily->marital_status->name ?? '' }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.educational_qualification') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ $beneficiaryFamily->educational_qualification->name ?? '' }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.health_condition') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ $beneficiaryFamily->health_condition->name ?? '' }} {{ $beneficiaryFamily->custom_health_condition ? '(' . $beneficiaryFamily->custom_health_condition . ')' : '' }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.disability_type') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ $beneficiaryFamily->disability_type->name ?? '' }} {{ $beneficiaryFamily->custom_disability_type ? '(' . $beneficiaryFamily->custom_disability_type . ')' : '' }}</span></td>
        </tr>
        <tr>
            <td>{{ trans('cruds.beneficiaryFamily.fields.can_work') }}</td>
            <td> <span class="badge bg-primary-transparent">{{ App\Models\BeneficiaryFamily::CAN_WORK_SELECT[$beneficiaryFamily->can_work] ?? '' }}</span></td>
        </tr>
    </table> 
</div>