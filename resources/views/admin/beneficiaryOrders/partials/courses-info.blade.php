<div class="card custom-card">
    <div class="card-header">
        <div class="card-title">
            <i class="ri-book-open-line me-2 text-primary"></i>
            {{ trans('cruds.beneficiaryOrder.extra.course_info') }}
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @if ($beneficiaryOrder->courseStudent && $beneficiaryOrder->courseStudent->course)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md avatar-rounded bg-primary-gradient me-3">
                            <i class="ri-book-2-line fs-18"></i>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.course.fields.title') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->courseStudent->course->title ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->courseStudent && $beneficiaryOrder->courseStudent->course)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md avatar-rounded bg-success-gradient me-3">
                            <i class="ri-user-star-line fs-18"></i>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.course.fields.trainer') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->courseStudent->course->trainer ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->courseStudent && $beneficiaryOrder->courseStudent->course)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md avatar-rounded bg-info-gradient me-3">
                            <i class="ri-calendar-event-line fs-18"></i>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.course.fields.start_at') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->courseStudent->course->start_at ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->courseStudent && $beneficiaryOrder->courseStudent->course)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md avatar-rounded bg-warning-gradient me-3">
                            <i class="ri-calendar-event-line fs-18"></i>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.course.fields.end_at') }}</span>
                            <span
                                class="d-block fs-16 fw-semibold">{{ $beneficiaryOrder->courseStudent->course->end_at ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->courseStudent && $beneficiaryOrder->courseStudent->course)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md avatar-rounded bg-danger-gradient me-3">
                            <i class="ri-award-line fs-18"></i>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.course.fields.certificate') }}</span>
                            <span
                                class="badge bg-{{ $beneficiaryOrder->courseStudent->course->certificate == 'free' ? 'success' : ($beneficiaryOrder->courseStudent->course->certificate == 'money' ? 'warning' : 'secondary') }}-transparent">
                                {{ \App\Models\Course::CERTIFICATE_SELECT[$beneficiaryOrder->courseStudent->course->certificate] ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($beneficiaryOrder->courseStudent && $beneficiaryOrder->courseStudent->course)
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md avatar-rounded bg-purple-gradient me-3">
                            <i class="ri-computer-line fs-18"></i>
                        </div>
                        <div>
                            <span
                                class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.course.fields.attend_type') }}</span>
                            <span
                                class="badge bg-{{ $beneficiaryOrder->courseStudent->course->attend_type == 'online' ? 'info' : 'primary' }}-transparent">
                                {{ \App\Models\Course::ATTEND_TYPE_SELECT[$beneficiaryOrder->courseStudent->course->attend_type] ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Course Student Specific Information -->
        @if ($beneficiaryOrder->courseStudent)
            <div class="mt-4">
                <h6 class="mb-3 border-bottom pb-2">
                    <i class="ri-user-settings-line me-2"></i>
                    {{ trans('cruds.beneficiaryOrder.extra.course_user_inputed') }}
                </h6>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm avatar-rounded bg-success me-2">
                                <i class="ri-check-line fs-14"></i>
                            </div>
                            <div>
                                <span
                                    class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.courseStudent.fields.certificate') }}</span>
                                <span
                                    class="badge bg-{{ $beneficiaryOrder->courseStudent->certificate ? 'success' : 'secondary' }}-transparent">
                                    {{ $beneficiaryOrder->courseStudent->certificate ? trans('global.yes') : trans('global.no') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm avatar-rounded bg-info me-2">
                                <i class="ri-car-line fs-14"></i>
                            </div>
                            <div>
                                <span
                                    class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.courseStudent.fields.transportation') }}</span>
                                <span
                                    class="badge bg-{{ $beneficiaryOrder->courseStudent->transportation ? 'success' : 'secondary' }}-transparent">
                                    {{ $beneficiaryOrder->courseStudent->transportation ? trans('global.yes') : trans('global.no') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm avatar-rounded bg-warning me-2">
                                <i class="ri-briefcase-line fs-14"></i>
                            </div>
                            <div>
                                <span
                                    class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.courseStudent.fields.prev_experience') }}</span>
                                <span
                                    class="badge bg-{{ $beneficiaryOrder->courseStudent->prev_experience ? 'success' : 'secondary' }}-transparent">
                                    {{ $beneficiaryOrder->courseStudent->prev_experience ? trans('global.yes') : trans('global.no') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm avatar-rounded bg-danger me-2">
                                <i class="ri-repeat-line fs-14"></i>
                            </div>
                            <div>
                                <span
                                    class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.courseStudent.fields.attend_same_course_before') }}</span>
                                <span
                                    class="badge bg-{{ $beneficiaryOrder->courseStudent->attend_same_course_before ? 'success' : 'secondary' }}-transparent">
                                    {{ $beneficiaryOrder->courseStudent->attend_same_course_before ? trans('global.yes') : trans('global.no') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($beneficiaryOrder->courseStudent->prev_courses)
                    <div class="mt-3">
                        <h6 class="mb-2">{{ trans('cruds.courseStudent.fields.prev_courses') }}</h6>
                        <div class="alert alert-light">
                            <div class="d-flex align-items-center justify-content-between">
                                @foreach (json_decode($beneficiaryOrder->courseStudent->prev_courses) as $key => $prevCourse)
                                    <div>
                                        <span class="d-block fs-14 fw-medium text-muted">{{ trans('cruds.courseStudent.extra.prev_course.' . $key) }}</span>
                                        <span class="d-block fs-14 fw-medium text-muted">{{ $prevCourse }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if ($beneficiaryOrder->courseStudent->note)
                    <div class="mt-3">
                        <h6 class="mb-2">{{ trans('cruds.courseStudent.fields.note') }}</h6>
                        <div class="alert alert-info">
                            {{ $beneficiaryOrder->courseStudent->note }}
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
