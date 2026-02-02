<div class="card custom-card">
    <div class="card-header">
        <div class="card-title">
            <i class="ri-user-voice-line me-2 text-primary"></i>
            {{ trans('cruds.beneficiaryOrder.extra.consultant_info') }}
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @if ($beneficiaryOrder->beneficiaryOrderAppointment && $beneficiaryOrder->beneficiaryOrderAppointment->consultationType)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-md bg-primary-transparent rounded-circle">
                                <i class="ri-file-list-3-line text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.consultant.fields.consultation_type') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->consultationType->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->beneficiaryOrderAppointment && $beneficiaryOrder->beneficiaryOrderAppointment->consultant)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-md bg-success-transparent rounded-circle">
                                <i class="ri-user-star-line text-success"></i>
                            </div>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.consultant.title_singular') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->consultant->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->beneficiaryOrderAppointment)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-md bg-info-transparent rounded-circle">
                                <i class="ri-calendar-line text-info"></i>
                            </div>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.beneficiaryOrder.appointment.date') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->date ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->beneficiaryOrderAppointment)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-md bg-warning-transparent rounded-circle">
                                <i class="ri-time-line text-warning"></i>
                            </div>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.beneficiaryOrder.appointment.time') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->time ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->beneficiaryOrderAppointment)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-md bg-secondary-transparent rounded-circle">
                                <i class="ri-timer-line text-secondary"></i>
                            </div>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.consultantSchedule.fields.slot_duration') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->duration ?? 'N/A' }}
                                {{ trans('global.minutes') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->beneficiaryOrderAppointment)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-md bg-purple-transparent rounded-circle">
                                <i class="ri-user-location-line text-purple"></i>
                            </div>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.consultantSchedule.fields.attendance_type') }}</span>
                            <span
                                class="badge bg-{{ $beneficiaryOrder->beneficiaryOrderAppointment->attendance_type == 'online' ? 'info' : 'primary' }}-transparent">
                                {{ \App\Models\BeneficiaryOrderAppointment::ATTENDANCE_TYPE_SELECT[$beneficiaryOrder->beneficiaryOrderAppointment->attendance_type] ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->beneficiaryOrderAppointment)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div
                                class="avatar avatar-md bg-{{ $beneficiaryOrder->beneficiaryOrderAppointment->status == 'confirmed' ? 'success' : ($beneficiaryOrder->beneficiaryOrderAppointment->status == 'canceled' ? 'danger' : 'warning') }}-transparent rounded-circle">
                                <i
                                    class="ri-checkbox-circle-line text-{{ $beneficiaryOrder->beneficiaryOrderAppointment->status == 'confirmed' ? 'success' : ($beneficiaryOrder->beneficiaryOrderAppointment->status == 'canceled' ? 'danger' : 'warning') }}"></i>
                            </div>
                        </div>
                        <div>
                            <span class="d-block fs-14 fw-medium text-muted">{{ trans('global.status') }}</span>
                            <span
                                class="badge bg-{{ $beneficiaryOrder->beneficiaryOrderAppointment->status == 'confirmed' ? 'success' : ($beneficiaryOrder->beneficiaryOrderAppointment->status == 'canceled' ? 'danger' : 'warning') }}-transparent">
                                {{ \App\Models\BeneficiaryOrderAppointment::STATUS_SELECT[$beneficiaryOrder->beneficiaryOrderAppointment->status] ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if (auth()->user()->user_type != 'beneficiary')
            <!-- Consultant Specific Information -->
            @if ($beneficiaryOrder->beneficiaryOrderAppointment && $beneficiaryOrder->beneficiaryOrderAppointment->consultant)
                <div class="mt-4">
                    <h6 class="mb-3 border-bottom pb-2">
                        <i class="ri-user-settings-line me-2"></i>
                        {{ trans('cruds.beneficiaryOrder.extra.consultant_details') }}
                    </h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-md bg-primary-transparent rounded-circle">
                                        <i class="ri-id-card-line text-primary"></i>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.consultant.fields.national_id') }}</span>
                                    <span
                                        class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->consultant->national_id ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-md bg-success-transparent rounded-circle">
                                        <i class="ri-phone-line text-success"></i>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.consultant.fields.phone_number') }}</span>
                                    <span
                                        class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->consultant->phone_number ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="avatar avatar-md bg-info-transparent rounded-circle">
                                        <i class="ri-graduation-cap-line text-info"></i>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.consultant.fields.academic_degree') }}</span>
                                    <span
                                        class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->beneficiaryOrderAppointment->consultant->academic_degree ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (
                        $beneficiaryOrder->beneficiaryOrderAppointment->consultant->documents &&
                            count($beneficiaryOrder->beneficiaryOrderAppointment->consultant->documents) > 0)
                        <div class="mt-3">
                            <h6 class="mb-2">{{ trans('cruds.consultant.fields.documents') }}</h6>
                            <div class="row">
                                @foreach ($beneficiaryOrder->beneficiaryOrderAppointment->consultant->documents as $document)
                                    <div class="col-md-4 mb-2">
                                        <div class="card border">
                                            <div class="card-body p-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <i class="ri-file-text-line text-primary fs-4"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <a href="{{ $document->getUrl() }}" target="_blank"
                                                            class="text-decoration-none">
                                                            <span
                                                                class="d-block fs-12 fw-medium text-truncate">{{ $document->file_name }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endif

    </div>
</div>
