<ul class="list-group list-group-flush border rounded-3">
    <li class="list-group-item p-3">
        <span class="fw-medium fs-15 d-block mb-3"><span class="me-1">&#10024;</span>معلومات عامة :</span>
        <p class="text-muted mb-2">
            <span class="text-primary fw-semibold">{{ $user->name }}</span> هي
            <span class="text-success fw-semibold">{{ $beneficiary->nationality->name ?? 'مقيمة' }}</span>
            <span class="text-info fw-semibold">{{ $beneficiary->marital_status->name ?? 'فرد' }}</span>
            تقيم في <span class="text-warning fw-semibold">{{ $beneficiary->district->name ?? 'المنطقة' }}</span>.
            والمؤهل التعليمي <span
                class="text-danger fw-semibold">{{ $beneficiary->educational_qualification->name ?? 'مؤهلها التعليمي' }}</span>،
            وهي <span
                class="text-secondary fw-semibold">{{ $beneficiary->can_work === 'yes' ? 'قادرة على العمل' : 'غير قادرة على العمل حالياً' }}</span>.
        </p>
        <p class="text-muted mb-0">
            @if ($beneficiary->job_type)
                تعمل حالياً كـ <span class="text-primary fw-semibold">{{ $beneficiary->job_type->name }}</span>،
            @endif
            وتقيم في <span class="text-success fw-semibold">{{ $beneficiary->street ?? 'عنوانها' }}</span>
            في مبنى رقم <span class="text-info fw-semibold">{{ $beneficiary->building_number ?? 'غير محدد' }}</span>.
            حالة ملفها <span
                class="text-warning fw-semibold">{{ $beneficiary->profile_status ? \App\Models\Beneficiary::PROFILE_STATUS_SELECT[$beneficiary->profile_status] : 'غير محدد' }}</span>
            ولديها <span
                class="text-danger fw-semibold">{{ $beneficiary->beneficiaryBeneficiaryOrders->count() }}</span>
            طلب.
        </p>
    </li>
    <li class="list-group-item p-3">
        <span class="fw-medium fs-15 d-block mb-3">{{ trans('cruds.beneficiary.profile.contact_info') }}
            :</span>
        <div class="text-muted">
            <p class="mb-3">
                <span class="avatar avatar-sm avatar-rounded text-primary p-1 bg-primary-transparent me-2">
                    <i class="ri-mail-line align-middle fs-15"></i>
                </span>
                <span class="fw-medium text-default">{{ trans('cruds.user.fields.email') }}
                    : </span>
                {{ $user->email }}
            </p>
            <p class="mb-3">
                <span class="avatar avatar-sm avatar-rounded text-info p-1 bg-info-transparent me-2">
                    <i class="ri-building-line align-middle fs-15"></i>
                </span>
                <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.address') }}
                    : </span>
                {{ $beneficiary->address ?? '' }}
            </p>
            <p class="mb-3">
                <span class="avatar avatar-sm avatar-rounded text-secondary p-1 bg-secondary-transparent me-2">
                    <i class="ri-phone-line align-middle fs-15"></i>
                </span>
                <span class="fw-medium text-default">{{ trans('cruds.user.fields.phone') }}
                    : </span> {{ $user->phone }}
            </p>
            @if ($beneficiary->beneficiary_category)
                <p class="mb-0">
                    <span class="avatar avatar-sm avatar-rounded text-warning p-1 bg-warning-transparent me-2">
                        <i class="ri-building-line align-middle fs-15"></i>
                    </span>
                    <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.beneficiary_category') }}
                        : </span> {{ $beneficiary->beneficiary_category->name ?? '-' }} 
                </p>
            @endif
        </div>
    </li>
    <li class="list-group-item p-3">
        <div class="row">
            <div class="col-md-6"> 
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="ribbon-2 ribbon-primary ribbon-left">
                            <span class="ribbon-text">{{ trans('cruds.beneficiary.form_steps.basic_information') }}</span>
                        </div>
                        <p class="mb-3 mt-5">
                            <span class="fw-medium text-default">{{ trans('cruds.user.fields.name') }}
                                : </span>
                            <span class="text-muted">{{ $user->name }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.user.fields.email') }}
                                : </span>
                            <span class="text-muted">{{ $user->email }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.user.fields.phone') }}
                                : <span class="text-muted">{{ $user->phone }}</span>
                            </span>
                        </p>
                        @if ($user->phone_2)
                            <p class="mb-3">
                                <span class="fw-medium text-default">{{ trans('cruds.user.fields.phone_2') }}
                                    : </span>
                                <span class="text-muted">{{ $user->phone_2 }}</span>
                            </p>
                        @endif
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.marital_status') }}
                                : </span>
                            <span class="badge bg-success-transparent">{{ $beneficiary->marital_status->name ?? '-' }}</span>
                        </p> 
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.martial_status_date') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->martial_status_date ?? '-' }}</span>
                        </p> 
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.dob') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->dob ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.region') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->region->name ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.city') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->city->name ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.district') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->district->name ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.street') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->street ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.address') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->address ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.building_number') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->building_number ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.building_additional_number') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->building_additional_number ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.postal_code') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->postal_code ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.map') }}
                                : </span>
                            @include('utilities.map-view', [
                                'latitude' => $beneficiary->latitude,
                                'longitude' => $beneficiary->longitude,
                            ])
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="ribbon-2 ribbon-danger ribbon-left">
                            <span class="ribbon-text">{{ trans('cruds.beneficiary.form_steps.work_information') }}</span>
                        </div>

                        <p class="mb-3 mt-5">
                            <span
                                class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.educational_qualification') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->educational_qualification->name ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.job_type') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->job_type->name ?? '-' }}</span>
                        </p>
                        @if($beneficiary->job_details)
                            @php
                                $job_details = json_decode($beneficiary->job_details, true);
                            @endphp
                            <p class="mb-3">
                                <span class="fw-medium text-default">{{ trans('cruds.beneficiary.job_details.company_name') }}
                                    : </span>
                                <span class="text-muted">{{ $job_details['company_name'] ?? '-' }}</span>
                            </p>
                            <p class="mb-3">
                                <span class="fw-medium text-default">{{ trans('cruds.beneficiary.job_details.job_title') }}
                                    : </span>
                                <span class="text-muted">{{ $job_details['job_title'] ?? '-' }}</span>
                            </p>
                            <p class="mb-3">
                                <span class="fw-medium text-default">{{ trans('cruds.beneficiary.job_details.job_phone') }}
                                    : </span>
                                <span class="text-muted">{{ $job_details['job_phone'] ?? '-' }}</span>
                            </p>
                            <p class="mb-3">
                                <span class="fw-medium text-default">{{ trans('cruds.beneficiary.job_details.job_address') }}
                                    : </span>
                                <span class="text-muted">{{ $job_details['job_address'] ?? '-' }}</span>
                            </p>
                        @endif
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.can_work') }}
                                : </span>
                            <span class="text-muted">{{ $beneficiary->can_work ?? '-' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.health_condition') }}
                                : </span>
                            <span class="badge bg-primary-transparent">{{ $beneficiary->health_condition->name ?? '' }}
                                {{ $beneficiary->custom_health_condition ? '(' . $beneficiary->custom_health_condition . ')' : '' }}</span>
                        </p>
                        <p class="mb-3">
                            <span class="fw-medium text-default">{{ trans('cruds.beneficiary.fields.disability_type') }}
                                : </span>
                            <span class="badge bg-primary-transparent">{{ $beneficiary->disability_type->name ?? '' }}
                                {{ $beneficiary->custom_disability_type ? '(' . $beneficiary->custom_disability_type . ')' : '' }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>
