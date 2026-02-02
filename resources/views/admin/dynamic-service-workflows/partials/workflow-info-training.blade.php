{{-- Training Workflow Info --}}
@php
    $categoryData = $workflow->training;
    $hasReport = false;
    if ($categoryData && $categoryData->specialist_report) {
        $hasReport = true;
    }
@endphp

@if($workflow->refused_reason)
    <div class="alert alert-danger">
        <strong>سبب الرفض:</strong><br>{{ $workflow->refused_reason }}
    </div>
@endif

@if($workflow->specialist)
    <p><strong>الأخصائي:</strong> {{ $workflow->specialist->name }}</p>
@endif

@if($categoryData)
    @if($categoryData->appointment_date)
        <p><strong>تاريخ الموعد:</strong><br>{{ $categoryData->appointment_date->format('Y-m-d H:i') }}</p>
    @endif

    @if($categoryData->appointment_attended !== null)
        <p><strong>حضور الموعد:</strong> 
            <span class="badge bg-{{ $categoryData->appointment_attended ? 'success' : 'danger' }}">
                {{ $categoryData->appointment_attended ? 'حضر' : 'لم يحضر' }}
            </span>
        </p>
    @endif
    
    @if($hasReport)
        <div class="mt-3">
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal">
                عرض تقرير التقييم
            </button>
        </div>
    @endif

    @if($categoryData->training_department_approved !== null)
        <p><strong>قرار قسم التدريب:</strong>
            <span class="badge bg-{{ $categoryData->training_department_approved ? 'success' : 'danger' }}">
                {{ $categoryData->training_department_approved ? 'موافق' : 'مرفوض' }}
            </span>
        </p>
    @endif

    @if($categoryData->program_start_date)
        <p><strong>تاريخ بدء البرنامج:</strong><br>{{ $categoryData->program_start_date->format('Y-m-d') }}</p>
    @endif

    @if($categoryData->test_passed !== null)
        <p><strong>نتيجة الاختبار:</strong>
            <span class="badge bg-{{ $categoryData->test_passed ? 'success' : 'danger' }}">
                {{ $categoryData->test_passed ? 'نجح' : 'فشل' }}
            </span>
        </p>
        @if($categoryData->test_result)
            <p><strong>تفاصيل النتيجة:</strong><br>{{ $categoryData->test_result }}</p>
        @endif
    @endif

    @if($categoryData->device_delivered)
        <p><strong>تم تسليم الجهاز:</strong> نعم</p>
        @if($categoryData->device_item_id)
            <p><strong>رقم العنصر:</strong> {{ $categoryData->device_item_id }}</p>
        @endif
    @endif

    @if($categoryData->service_type === 'group')
        @if($categoryData->payment_url)
            <p><strong>رابط الدفع:</strong><br><a href="{{ $categoryData->payment_url }}" target="_blank">{{ $categoryData->payment_url }}</a></p>
        @endif
        @if($categoryData->is_paid_program)
            <p><span class="badge bg-info">البرنامج مدفوع</span></p>
        @endif
        @if($categoryData->in_waiting_list)
            <p><strong>في قائمة الانتظار:</strong> نعم</p>
            @if($categoryData->group_position)
                <p><strong>الموقع:</strong> {{ $categoryData->group_position }}</p>
            @endif
            @if($categoryData->group_size)
                <p><strong>حجم المجموعة المطلوب:</strong> {{ $categoryData->group_size }}</p>
            @endif
        @endif
        @if($categoryData->meeting_schedule)
            <div class="mt-3">
                <strong>جدول اللقاءات:</strong>
                <ul class="list-unstyled mt-2">
                    @foreach($categoryData->meeting_schedule as $meeting)
                        <li class="mb-2">
                            <strong>{{ $meeting['title'] ?? 'اجتماع' }}</strong><br>
                            <small class="text-muted">{{ isset($meeting['date']) ? \Carbon\Carbon::parse($meeting['date'])->format('Y-m-d H:i') : '' }}</small>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if($categoryData->certificate_issued)
            <p><span class="badge bg-success">تم إصدار الشهادة</span></p>
        @endif
        @if($categoryData->certificate_test_passed !== null)
            <p><strong>نتيجة اختبار الشهادة:</strong>
                <span class="badge bg-{{ $categoryData->certificate_test_passed ? 'success' : 'danger' }}">
                    {{ $categoryData->certificate_test_passed ? 'نجح' : 'لم ينجح' }}
                </span>
            </p>
        @endif
        @if($categoryData->certificate_message_sent)
            <p><strong>رسالة الإرسال:</strong><br>{{ $categoryData->certificate_message_sent }}</p>
        @endif
    @endif
@endif

@if($workflow->notes)
    <p><strong>ملاحظات:</strong><br>{{ $workflow->notes }}</p>
@endif

{{-- Attendance Tracking Form --}}
@if($categoryData && $workflow->current_status === \App\Models\DynamicServiceWorkflowTraining::STATUS_ATTENDANCE_TRACKING)
    @php
        $meetings = $categoryData->meeting_schedule ?? [];
        $attendanceData = $categoryData->attendance_data ?? [];
    @endphp 
@endif

{{-- Report Modal --}}
@if($hasReport)
    @include('admin.dynamic-service-workflows.partials.workflow-info-training-report-modal', ['workflow' => $workflow])
@endif

