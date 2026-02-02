{{-- Tab Navigation --}}
<ul class="nav nav-tabs" id="consultantFormTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="order-info-tab" data-bs-toggle="tab" data-bs-target="#order-info" type="button"
            role="tab" aria-controls="order-info" aria-selected="true">
            {{ trans('cruds.service.extra.order_info') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="appointment-info-tab" data-bs-toggle="tab" data-bs-target="#appointment-info"
            type="button" role="tab" aria-controls="appointment-info" aria-selected="false">
            {{ trans('cruds.service.extra.consultant_info') }}
        </button>
    </li>
</ul>

{{-- Tab Content --}}
<div class="tab-content" id="consultantFormTabsContent">
    <div class="tab-pane fade show active" id="order-info" role="tabpanel" aria-labelledby="order-info-tab">
        @include('partials.beneficiaryOrderForm.basic-data')
    </div>
    <div class="tab-pane fade" id="appointment-info" role="tabpanel" aria-labelledby="appointment-info-tab">

        <div class="row">
            <!-- Consultation Type -->
            <div class="col-md-4">
                @include('utilities.form.select', [
                    'name' => 'consultation_type_id',
                    'label' => 'cruds.consultant.fields.consultation_type',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'options' => App\Models\ConsultationType::pluck('name', 'id')->prepend(
                        trans('global.pleaseSelect'),
                        ''),
                    'search' => true,
                    'attributes' => 'onchange=loadConsultants()',
                ])
            </div>

            <!-- Attendance Type -->
            <div class="col-md-4">
                @include('utilities.form.select', [
                    'name' => 'attendance_type',
                    'label' => 'cruds.consultantSchedule.fields.attendance_type',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'options' =>
                        ['' => trans('global.pleaseSelect')] +
                        App\Models\ConsultantSchedule::ATTENDANCE_TYPE_SELECT,
                    'attributes' => 'onchange=loadAvailableDays()',
                ])
            </div>

            <!-- Date Selection -->
            <div class="col-md-4" id="date-selection-section">
                @include('utilities.form.date', [
                    'name' => 'appointment_date',
                    'id' => 'appointment_date',
                    'label' => 'cruds.beneficiaryOrder.appointment.date',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'attributes' => 'onchange=loadAvailableTimes()',
                ])
            </div>

            <!-- Time Slots -->
            <div class="col-md-12" id="time-slots-section" style="display: none;">
                <div class="form-group">
                    <label class="form-label">{{ trans('cruds.beneficiaryOrder.appointment.time') }}</label>
                    <div class="time-slots-scroll-container">
                        <div class="time-slots-wrapper" id="time-slots-container">
                            <!-- Time slots will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden consultant field -->
            <input type="hidden" name="consultant_id" id="selected_consultant_id">
        </div>
        
        <div class="d-grid gap-2 col-6 mx-auto">
            <button class="btn btn-primary rounded-pill btn-wave" type="submit" id="submitBtn">
                {{ trans('global.save') }}
            </button>
        </div>
    </div>
</div>

<style>
    /* Time Slots Horizontal Slider Styles */
    .time-slots-scroll-container {
        position: relative;
        width: 100%;
        overflow: hidden;
        border-radius: 8px;
        background: #f8f9fa;
        padding: 10px 0;
    }

    .time-slots-wrapper {
        display: flex;
        gap: 12px;
        padding: 0 15px;
        overflow-x: auto;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #dee2e6 #f8f9fa;
    }

    .time-slots-wrapper::-webkit-scrollbar {
        height: 6px;
    }

    .time-slots-wrapper::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 3px;
    }

    .time-slots-wrapper::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }

    .time-slots-wrapper::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }

    .time-slot-item {
        flex: 0 0 auto;
        min-width: 120px;
        position: relative;
    }

    .time-slot-item input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .time-slot-label {
        display: block;
        padding: 12px 16px;
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .time-slot-label:hover {
        border-color: #007bff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .time-slot-item input[type="radio"]:checked+.time-slot-label {
        border-color: #007bff;
        background: #f8f9ff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    }

    .time-text {
        font-size: 16px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 4px;
    }

    .consultant-name {
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .time-slot-item {
            min-width: 100px;
        }

        .time-slot-label {
            padding: 10px 12px;
        }

        .time-text {
            font-size: 14px;
        }

        .consultant-name {
            font-size: 11px;
        }
    }
</style>

<script>
    let availableDays = [];
    let selectedDate = null;

    function loadConsultants() {
        const consultationTypeId = document.querySelector('select[name="consultation_type_id"]').value;
        const attendanceType = document.querySelector('select[name="attendance_type"]').value;

        if (consultationTypeId && attendanceType) {
            loadAvailableDays();
        }
    }

    function loadAvailableDays() {
        const consultationTypeId = document.querySelector('select[name="consultation_type_id"]').value;
        const attendanceType = document.querySelector('select[name="attendance_type"]').value;

        if (!consultationTypeId || !attendanceType) return;

        // Fetch available days
        fetch(
                `{{ route('consultants.available-days') }}?consultation_type_id=${consultationTypeId}&attendance_type=${attendanceType}`
            )
            .then(response => response.json())
            .then(data => {
                availableDays = data.available_days || [];

                // Find and update the help-block text for appointment_date
                const helpBlock = document.querySelector('#appointment_date').closest('.form-group').querySelector(
                    '.help-block');
                if (helpBlock) {
                    helpBlock.innerHTML =
                        `الأيام المتاحة: <span class="text-primary">${availableDays.join(' - ')}</span>`;
                }
            });
    }

    function loadAvailableTimes() {
        const date = document.querySelector('input[name="appointment_date"]').value;
        const consultationTypeId = document.querySelector('select[name="consultation_type_id"]').value;
        const attendanceType = document.querySelector('select[name="attendance_type"]').value;

        if (!date || !consultationTypeId || !attendanceType) return;

        // Show loading
        document.getElementById('time-slots-section').style.display = 'block';
        document.getElementById('time-slots-container').innerHTML =
            '<div class="col-12"><div class="spinner-border text-primary" role="status"></div></div>';

        // Fetch available times
        fetch(
                `{{ route('consultants.available-times') }}?date=${date}&consultation_type_id=${consultationTypeId}&attendance_type=${attendanceType}`
            )
            .then(response => response.json())
            .then(data => {
                if (data.available_times.length > 0) {
                    renderTimeSlots(data.available_times || []);
                } else {
                    document.getElementById('time-slots-section').style.display = 'block';
                    document.getElementById('time-slots-container').innerHTML =
                        '<div class="col-12 text-warning">لا يوجد مواعيد متاحة في هذا اليوم. يرجى اختيار يوم آخر.</div>';
                }
            })
            .catch(error => {
                document.getElementById('time-slots-container').innerHTML =
                    '<div class="col-12 text-danger">Error loading available times</div>';
            });
    }

    function renderTimeSlots(availableTimes) {
        const container = document.getElementById('time-slots-container');

        if (availableTimes.length === 0) {
            container.innerHTML = '<div class="text-warning text-center w-100">لا توجد مواعيد متاحة في هذا اليوم</div>';
            return;
        }

        let html = '';
        availableTimes.forEach(timeSlot => {
            html += `
                <div class="time-slot-item">
                    <input class="form-check-input" type="radio" name="appointment_time" 
                        value="${timeSlot.time}" id="time_${timeSlot.time.replace(':', '_')}_${timeSlot.consultant_id}"
                        data-consultant-id="${timeSlot.consultant_id}"
                        data-consultant-name="${timeSlot.consultant_name}"
                        onchange="selectTimeSlot(this)">
                    <label class="time-slot-label" for="time_${timeSlot.time.replace(':', '_')}_${timeSlot.consultant_id}">
                        <div class="time-text">${timeSlot.time}</div>
                        <div class="consultant-name">${timeSlot.consultant_name}</div>
                    </label>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function selectTimeSlot(radio) {
        const consultantId = radio.getAttribute('data-consultant-id');
        const consultantName = radio.getAttribute('data-consultant-name');

        // Set the hidden consultant field
        document.getElementById('selected_consultant_id').value = consultantId;

        // You can also show a confirmation message
        console.log(`Selected consultant: ${consultantName} (ID: ${consultantId})`);
    }
</script>
