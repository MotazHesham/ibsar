{{-- Training Workflow Form --}}
<div class="mb-3">
    <label class="form-label">الانتقال إلى حالة:</label>
    <select name="to_status" id="to_status" class="form-select" required onchange="toggleTrainingStatusFields()">
        <option value="">-- اختر الحالة --</option>
        @php
            $validTransitions = [];
            $serviceType = $workflow->training->service_type ?? 'individual';
            if ($serviceType === 'group') {
                // Group workflow transitions
                switch ($workflow->current_status) {
                    case \App\Models\DynamicServiceWorkflow::STATUS_PENDING_REVIEW:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflow::STATUS_REFUSED => 'رفض الطلب',
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_APPROVED_WITH_PAYMENT =>
                                'موافق عليه مع رابط الدفع',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_APPROVED_WITH_PAYMENT:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_IN_WAITING_LIST =>
                                'إضافة إلى قائمة الانتظار',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_IN_WAITING_LIST:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_MEETING_SCHEDULE_SENT =>
                                'إرسال جدول اللقاءات',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_MEETING_SCHEDULE_SENT:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED => 'بدء البرنامج',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_ATTENDANCE_TRACKING => 'تتبع الحضور',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_ATTENDANCE_TRACKING:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_ACCOUNTING_ENTRIES => 'القيد المحاسبي',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_ACCOUNTING_ENTRIES:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_COMPLETED => 'اكتمال الشهادة',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_COMPLETED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_ISSUED => 'إصدار الشهادة', 
                            \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية (بدون شهادة)',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_DEVICE_DELIVERY:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_ISSUED => 'إصدار الشهادة',
                            \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_ISSUED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية',
                        ];
                        break;
                }
            } else {
                // Individual workflow transitions
                switch ($workflow->current_status) {
                    case \App\Models\DynamicServiceWorkflow::STATUS_PENDING_REVIEW:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflow::STATUS_REFUSED => 'رفض الطلب',
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_SET => 'تحديد موعد للتقييم',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_SET:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_RESCHEDULED =>
                                'إعادة جدولة الموعد',
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_RATED => 'تم التقييم',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_RESCHEDULED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_SET => 'تحديد موعد جديد',
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_RATED => 'تم التقييم',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_RATED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_SENT_TO_TRAINING => 'إرسال لقسم التدريب',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_SENT_TO_TRAINING:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_TRAINING_APPROVED => 'يعتمد',
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_TRAINING_REJECTED => 'لايعتمد',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_TRAINING_APPROVED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENTS_SCHEDULED => 'جدولة المواعيد',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENTS_SCHEDULED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED => 'بدء البرنامج',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_ATTENDANCE_TRACKING => 'تتبع الحضور',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_ATTENDANCE_TRACKING:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_ACCOUNTING_ENTRIES => 'القيد المحاسبي',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_ACCOUNTING_ENTRIES:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_SENT => 'إرسال الاختبار',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_SENT:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_FAILED => 'فشل الاختبار',
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_PASSED => 'نجح الاختبار',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_FAILED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_PASSED:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية',
                            \App\Models\DynamicServiceWorkflowTraining::STATUS_DEVICE_DELIVERY => 'تسليم الجهاز',
                        ];
                        break;
                    case \App\Models\DynamicServiceWorkflowTraining::STATUS_DEVICE_DELIVERY:
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية',
                        ];
                        break;
                }
            }
        @endphp
        @foreach ($validTransitions as $status => $label)
            <option value="{{ $status }}">{{ $label }}</option>
        @endforeach
    </select>
</div>

{{-- Refused Reason --}}
<div class="mb-3" id="refused_reason_field" style="display: none;">
    <label class="form-label">سبب الرفض:</label>
    <textarea name="refused_reason" class="form-control" rows="3"></textarea>
</div>

{{-- Group-specific: Payment URL --}}
<div class="mb-3" id="payment_url_field" style="display: none;">
    <div class="form-check mb-2">
        <input type="checkbox" name="is_paid_program" value="1" class="form-check-input" id="is_paid_program" onchange="togglePaymentUrl()">
        <label class="form-check-label" for="is_paid_program">البرنامج مدفوع</label>
    </div>
    <div id="payment_url_input" style="display: none;">
        <label class="form-label">رابط الدفع:</label>
        <input type="url" name="payment_url" class="form-control" placeholder="https://...">
    </div>
</div>  

{{-- Group-specific: Meeting Schedule --}}
<div class="mb-3" id="meeting_schedule_field" style="display: none;">
    <label class="form-label">جدول اللقاءات:</label>
    <div id="meeting_schedule_container">
        <div class="meeting-item mb-2">
            <input type="datetime-local" name="meeting_schedule[0][date]" class="form-control mb-2"
                placeholder="تاريخ ووقت الاجتماع">
            <input type="text" name="meeting_schedule[0][title]" class="form-control"
                placeholder="عنوان الاجتماع">
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-secondary" onclick="addMeetingSchedule()">إضافة اجتماع</button>
</div>

{{-- Group-specific: Certificate Test Result --}}
<div class="mb-3" id="certificate_test_field" style="display: none;">
    <label class="form-label">نوع الشهادة:</label>
    <select name="certificate_type" id="certificate_type" class="form-select" onchange="toggleCertificateType()">
        <option value="">-- اختر نوع الشهادة --</option>
        <option value="completion">شهادة إتمام</option>
        <option value="passed">شهادة اجتياز</option>
    </select>
    
    {{-- Completion Certificate Fields --}}
    <div id="completion_certificate_fields" style="display: none;">
        <label class="form-label mt-2">الرسالة:</label>
        <textarea name="certificate_message_sent" class="form-control" rows="3" placeholder="الرسالة المرسلة للمستفيد"></textarea>
    </div>
    
    {{-- Passed Certificate Fields --}}
    <div id="passed_certificate_fields" style="display: none;">
        <label class="form-label mt-2">نتيجة اختبار الشهادة:</label>
        <select name="certificate_test_passed" class="form-select">
            <option value="">-- اختر --</option>
            <option value="1">نجح في الاختبار</option>
            <option value="0">لم ينجح في الاختبار</option>
        </select>
        <label class="form-label mt-2">الرسالة:</label>
        <textarea name="certificate_message_sent" class="form-control" rows="3" placeholder="الرسالة المرسلة للمستفيد"></textarea>
    </div>
</div>

{{-- Appointment Date --}}
<div class="mb-3" id="appointment_date_field" style="display: none;">
    <label class="form-label">تاريخ الموعد:</label>
    <input type="datetime-local" name="appointment_date" class="form-control">
</div>

{{-- Specialist Report --}}
<div class="mb-3" id="specialist_report_field" style="display: none;">
    @include('admin.dynamic-service-workflows.partials.beneficiary-assessment-form')
</div>

{{-- Training Department Approval --}}
<div class="mb-3" id="training_approval_field" style="display: none;">
    <label class="form-label">قرار قسم التدريب:</label>
    <select name="training_department_approved" class="form-select">
        <option value="1">يعتمد</option>
        <option value="0">لايعتمد</option>
    </select>
</div>

{{-- Program Start Date --}}
<div class="mb-3" id="program_start_date_field" style="display: none;">
    <label class="form-label">تاريخ بدء البرنامج:</label>
    <input type="date" name="program_start_date" class="form-control"
        value="{{ $workflow->training->program_start_date ? $workflow->training->program_start_date->format('Y-m-d') : '' }}">
</div> 
{{-- Meetings List (shown when transitioning to ATTENDANCE_TRACKING) --}}
@php
    $categoryData = $workflow->training ?? null;
    $meetingsList = $categoryData && $categoryData->meeting_schedule ? $categoryData->meeting_schedule : [];
@endphp
@if(!empty($meetingsList))
<div class="mb-3" id="meetings_list_field" style="display: none;">
    <label class="form-label">جدول اللقاءات المحدد:</label>
    <div class="list-group">
        @foreach($meetingsList as $index => $meeting)
            <div class="list-group-item">
                <strong>{{ $meeting['title'] ?? 'اجتماع ' . ($index + 1) }}</strong><br>
                <small class="text-muted">
                    {{ isset($meeting['date']) ? \Carbon\Carbon::parse($meeting['date'])->format('Y-m-d H:i') : '' }}
                </small>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Test Result --}}
<div class="mb-3" id="test_result_field" style="display: none;">
    <label class="form-label">نتيجة الاختبار:</label>
    <textarea name="test_result" class="form-control" rows="3"></textarea>
    <div class="mt-2" id="alternatives_field" style="display: none;">
        <label class="form-label">البدائل المقدمة:</label>
        <textarea name="alternatives_offered" class="form-control" rows="3"></textarea>
    </div>
</div>

{{-- Device Delivery (for individual after test passed) --}}
<div class="mb-3" id="device_delivery_field" style="display: none;">
    <div class="form-check mb-3">
        <input type="checkbox" name="device_delivered" value="1" class="form-check-input"
            id="device_delivered" onchange="toggleDeviceItemId()">
        <label class="form-check-label" for="device_delivered">هل يتضمن البرنامج تسليم جهاز؟</label>
    </div>
    <div id="device_item_id_field" style="display: none;">
        <label class="form-label">رقم العنصر من المخزون:</label>
        <input type="number" name="device_item_id" id="device_item_id" class="form-control" placeholder="أدخل رقم العنصر من المخزون">
    </div>
</div>

<script>
    function toggleTrainingStatusFields() {
        const status = document.getElementById('to_status').value;
        const serviceType = '{{ $workflow->training->service_type ?? "individual" }}';

        // Helper function to safely set display style
        function setDisplay(elementId, display) {
            const element = document.getElementById(elementId);
            if (element) {
                element.style.display = display;
            }
        }

        // Hide all fields
        setDisplay('refused_reason_field', 'none');
        setDisplay('appointment_date_field', 'none');
        setDisplay('specialist_report_field', 'none');
        setDisplay('training_approval_field', 'none');
        setDisplay('program_start_date_field', 'none');
        setDisplay('test_result_field', 'none');
        setDisplay('device_delivery_field', 'none');
        setDisplay('alternatives_field', 'none');
        setDisplay('payment_url_field', 'none');  
        setDisplay('certificate_test_field', 'none');
        setDisplay('completion_certificate_fields', 'none');
        setDisplay('passed_certificate_fields', 'none'); 
        setDisplay('meeting_schedule_field', 'none'); 
        setDisplay('meetings_list_field', 'none');

        // Show relevant fields based on status
        if (status === '{{ \App\Models\DynamicServiceWorkflow::STATUS_REFUSED }}') {
            setDisplay('refused_reason_field', 'block');
            const refusedTextarea = document.querySelector('#refused_reason_field textarea');
            if (refusedTextarea) {
                refusedTextarea.required = true;
            }
        } else {
            const refusedTextarea = document.querySelector('#refused_reason_field textarea');
            if (refusedTextarea) {
                refusedTextarea.required = false;
            }
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_SET }}' ||
            status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENT_RESCHEDULED }}') {
            setDisplay('appointment_date_field', 'block');
            const appointmentInput = document.querySelector('#appointment_date_field input');
            if (appointmentInput) {
                appointmentInput.required = true;
            }
        } else {
            const appointmentInput = document.querySelector('#appointment_date_field input');
            if (appointmentInput) {
                appointmentInput.required = false;
            }
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_RATED }}') {
            setDisplay('specialist_report_field', 'block');
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_TRAINING_APPROVED }}' ||
            status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_TRAINING_REJECTED }}') {
            setDisplay('training_approval_field', 'block');
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED }}') {
            setDisplay('program_start_date_field', 'block');
            // Show meetings field when transitioning to PROGRAM_STARTED
            // For group: from MEETING_SCHEDULE_SENT
            // For individual: from APPOINTMENTS_SCHEDULED
            const currentStatus = '{{ $workflow->current_status }}';  
            const shouldShowMeetings = 
                (serviceType === 'group' && currentStatus === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_MEETING_SCHEDULE_SENT }}') ||
                (serviceType === 'individual' && currentStatus === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_APPOINTMENTS_SCHEDULED }}');
            
            if (shouldShowMeetings) {
                setDisplay('program_meetings_field', 'block');
                // Set required attribute on meeting date inputs when field is visible
                document.querySelectorAll('.program-meeting-date').forEach(input => {
                    input.required = true;
                });
            } else {
                // Remove required attribute when field is hidden
                document.querySelectorAll('.program-meeting-date').forEach(input => {
                    input.required = false;
                });
            }

        } else {
            // Remove required attribute when field is hidden
            document.querySelectorAll('.program-meeting-date').forEach(input => {
                input.required = false;
            });
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_ATTENDANCE_TRACKING }}') {
            // Show meetings list when transitioning from PROGRAM_STARTED
            if ('{{ $workflow->current_status }}' === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_PROGRAM_STARTED }}') {
                setDisplay('meetings_list_field', 'block');
            }
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_FAILED }}' ||
            status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_PASSED }}') {
            setDisplay('test_result_field', 'block');
            if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_TEST_FAILED }}') {
                setDisplay('alternatives_field', 'block');
            }
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_ISSUED }}' 
        ) {
            // Show device delivery field for both individual and group workflows
            setDisplay('device_delivery_field', 'block');
        }

        // Group-specific fields
        if (serviceType === 'group') {
            if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_APPROVED_WITH_PAYMENT }}') {
                setDisplay('payment_url_field', 'block');
            }  
            if (status === '{{ \App\Models\DynamicServiceWorkflowTraining::STATUS_CERTIFICATE_COMPLETED }}') {
                setDisplay('certificate_test_field', 'block');
                // Reset certificate type when showing the field
                const certificateTypeSelect = document.getElementById('certificate_type');
                if (certificateTypeSelect) {
                    certificateTypeSelect.value = '';
                    toggleCertificateType();
                }
            }
        }
    }

    function toggleCertificateType() {
        const certificateType = document.getElementById('certificate_type').value;
        const completionFields = document.getElementById('completion_certificate_fields');
        const passedFields = document.getElementById('passed_certificate_fields');
        
        // Get all form fields
        const completionMessage = completionFields ? completionFields.querySelector('textarea[name="certificate_message_sent"]') : null;
        const passedTestSelect = passedFields ? passedFields.querySelector('select[name="certificate_test_passed"]') : null;
        const passedMessage = passedFields ? passedFields.querySelector('textarea[name="certificate_message_sent"]') : null;
        
        // Hide both fields first and disable them
        if (completionFields) {
            completionFields.style.display = 'none';
        }
        if (passedFields) {
            passedFields.style.display = 'none';
        }
        
        // Disable all fields first
        if (completionMessage) {
            completionMessage.disabled = true;
            completionMessage.required = false;
        }
        if (passedTestSelect) {
            passedTestSelect.disabled = true;
            passedTestSelect.required = false;
        }
        if (passedMessage) {
            passedMessage.disabled = true;
            passedMessage.required = false;
        }
        
        // Show relevant fields based on selection
        if (certificateType === 'completion') {
            if (completionFields) {
                completionFields.style.display = 'block';
                // Enable and make message required for completion
                if (completionMessage) {
                    completionMessage.disabled = false;
                    completionMessage.required = true;
                }
            }
            // Clear and disable passed certificate fields
            if (passedTestSelect) {
                passedTestSelect.value = '';
            }
            if (passedMessage) {
                passedMessage.value = '';
            }
        } else if (certificateType === 'passed') {
            if (passedFields) {
                passedFields.style.display = 'block';
                // Enable and make test result required
                if (passedTestSelect) {
                    passedTestSelect.disabled = false;
                    passedTestSelect.required = true;
                }
                if (passedMessage) {
                    passedMessage.disabled = false;
                    passedMessage.required = true;
                }
            }
            // Clear and disable completion certificate fields
            if (completionMessage) {
                completionMessage.value = '';
            }
        } else {
            // Clear all fields when no type is selected
            if (completionMessage) {
                completionMessage.value = '';
            }
            if (passedTestSelect) {
                passedTestSelect.value = '';
            }
            if (passedMessage) {
                passedMessage.value = '';
            }
        }
    }

    function togglePaymentUrl() {
        const isPaidCheckbox = document.getElementById('is_paid_program');
        if (!isPaidCheckbox) return;
        
        const isPaid = isPaidCheckbox.checked;
        const paymentUrlInput = document.getElementById('payment_url_input');
        const paymentUrlField = document.querySelector('#payment_url_input input[name="payment_url"]');
        
        if (paymentUrlInput) {
            paymentUrlInput.style.display = isPaid ? 'block' : 'none';
        }
        
        if (paymentUrlField) {
            paymentUrlField.required = isPaid;
            if (!isPaid) {
                paymentUrlField.value = '';
            }
        }
    }

    let meetingCounter = 1;
    let programMeetingCounter = 1;

    function addMeetingSchedule() {
        const container = document.getElementById('meeting_schedule_container');
        const newMeeting = document.createElement('div');
        newMeeting.className = 'meeting-item mb-2';
        newMeeting.innerHTML = `
        <input type="datetime-local" name="meeting_schedule[${meetingCounter}][date]" class="form-control mb-2" placeholder="تاريخ ووقت الاجتماع">
        <input type="text" name="meeting_schedule[${meetingCounter}][title]" class="form-control" placeholder="عنوان الاجتماع">
        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="this.parentElement.remove()">حذف</button>
    `;
        container.appendChild(newMeeting);
        meetingCounter++;
    }

    function addProgramMeeting() {
        const container = document.getElementById('program_meetings_container');
        const newMeeting = document.createElement('div');
        newMeeting.className = 'meeting-item mb-2';
        const isFieldVisible = document.getElementById('program_meetings_field') && 
                               document.getElementById('program_meetings_field').style.display !== 'none';
        newMeeting.innerHTML = `
        <input type="datetime-local" name="program_meetings[${programMeetingCounter}][date]" class="form-control mb-2 program-meeting-date" placeholder="تاريخ ووقت الاجتماع" ${isFieldVisible ? 'required' : ''}>
        <input type="text" name="program_meetings[${programMeetingCounter}][title]" class="form-control" placeholder="عنوان الاجتماع">
        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="this.parentElement.remove()">حذف</button>
    `;
        container.appendChild(newMeeting);
        programMeetingCounter++;
    }

    function toggleDeviceItemId() {
        const deviceDelivered = document.getElementById('device_delivered');
        const deviceItemIdField = document.getElementById('device_item_id_field');
        const deviceItemId = document.getElementById('device_item_id');
        
        if (deviceDelivered && deviceItemIdField && deviceItemId) {
            if (deviceDelivered.checked) {
                deviceItemIdField.style.display = 'block';
                deviceItemId.required = true;
            } else {
                deviceItemIdField.style.display = 'none';
                deviceItemId.value = '';
                deviceItemId.required = false;
            }
        }
    }
    
    // Initialize device delivery field state when the field is shown
    document.addEventListener('DOMContentLoaded', function() {
        const deviceDelivered = document.getElementById('device_delivered');
        if (deviceDelivered) {
            // Check initial state
            toggleDeviceItemId();
        }
    });
</script>

