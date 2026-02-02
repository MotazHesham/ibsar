@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.dynamicServicesManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.dynamicService.title'),
                'url' => route('admin.dynamic-services.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.dynamicService.title_singular'), 'url' => '#'],
        ];
        $pageTitle =
            trans('global.show') . ' ' . trans('cruds.dynamicService.title_singular') . ' #' . $dynamicService->id;
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        <div class="col-xxl-8">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">{{ trans('cruds.dynamicService.fields.basic_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.title') }}</label>
                                <p class="form-control-static">{{ $dynamicService->title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.slug') }}</label>
                                <p class="form-control-static">{{ $dynamicService->slug }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.status') }}</label>
                                <p class="form-control-static">
                                    <span
                                        class="badge bg-{{ $dynamicService->status == 'active' ? 'success' : 'danger' }}-transparent">
                                        {{ trans('global.' . $dynamicService->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.icon') }}</label>
                                <p class="form-control-static">
                                    @if ($dynamicService->icon)
                                        <i class="{{ $dynamicService->icon }}"></i> {{ $dynamicService->icon }} 
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold">{{ trans('cruds.dynamicService.fields.description') }}</label>
                                <p class="form-control-static">
                                    {{ $dynamicService->description ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Beneficiaries Requests Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">
                        {{ trans('cruds.dynamicService.fields.beneficiaries_requests') ?? 'طلبات المستفيدين' }}</h6>
                </div>
                <div class="card-body">
                    @if ($dynamicServiceOrders && $dynamicServiceOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.id') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.beneficiary') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.title') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.status') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.accept_status') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.specialist') }}</th>
                                        <th>{{ trans('cruds.beneficiaryOrder.fields.created_at') }}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dynamicServiceOrders as $index => $dynamicServiceOrder)
                                        @php
                                            $beneficiaryOrder = $dynamicServiceOrder->beneficiaryOrder;
                                        @endphp
                                        @if ($beneficiaryOrder)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $beneficiaryOrder->id }}</td>
                                                <td>
                                                    @if ($beneficiaryOrder->beneficiary && $beneficiaryOrder->beneficiary->user)
                                                        {{ $beneficiaryOrder->beneficiary->user->name }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $beneficiaryOrder->title ?? '-' }}</td>
                                                <td>
                                                    @if ($beneficiaryOrder->status)
                                                        <span
                                                            class="badge bg-{{ $beneficiaryOrder->status->badge_class ?? 'primary' }}-transparent">
                                                            {{ $beneficiaryOrder->status->name }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($beneficiaryOrder->accept_status)
                                                        <span
                                                            class="badge bg-{{ $beneficiaryOrder->accept_status == 'yes' ? 'success' : 'danger' }}-transparent">
                                                            {{ \App\Models\BeneficiaryOrder::ACCEPT_STATUS_RADIO[$beneficiaryOrder->accept_status] ?? $beneficiaryOrder->accept_status }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($beneficiaryOrder->specialist)
                                                        {{ $beneficiaryOrder->specialist->name }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $beneficiaryOrder->created_at ? $beneficiaryOrder->created_at->format('Y-m-d H:i') : '-' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.beneficiary-orders.show', $beneficiaryOrder->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ri-eye-line"></i> {{ trans('global.view') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ trans('cruds.dynamicService.fields.no_beneficiaries_requests') ?? 'لا توجد طلبات من المستفيدين لهذه الخدمة' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Program Meetings Section (for training category) -->
    @if ($dynamicService->category === 'training')
        <div class="row mt-4">
            <div class="col-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">جدول اللقاءات</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="toggleProgramMeetingsForm()">
                            <i class="ri-edit-line"></i> {{ $dynamicService->program_meetings ? 'تعديل' : 'إضافة' }}
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($dynamicService->program_meetings && count($dynamicService->program_meetings) > 0)
                            <div id="program_meetings_display">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>تاريخ ووقت الاجتماع</th>
                                                <th>عنوان الاجتماع</th>
                                                <th>الإجراءات</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dynamicService->program_meetings as $index => $meeting)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if (!empty($meeting['date']))
                                                            {{ \Carbon\Carbon::parse($meeting['date'])->format('Y-m-d H:i') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $meeting['title'] ?? '-' }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info" onclick="openAttendanceModal({{ $index }}, {{ $dynamicService->id }})">
                                                            <i class="ri-user-line"></i> عرض الحضور
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div id="program_meetings_display">
                                <div class="alert alert-info">لا توجد اجتماعات محددة</div>
                            </div>
                        @endif

                        <div id="program_meetings_form" style="display: none;">
                            <form id="programMeetingsForm" method="POST" action="{{ route('admin.dynamic-services.update-program-meetings', $dynamicService->id) }}">
                                @csrf
                                @method('PUT')
                                <div id="program_meetings_container">
                                    @if ($dynamicService->program_meetings && count($dynamicService->program_meetings) > 0)
                                        @foreach ($dynamicService->program_meetings as $index => $meeting)
                                            <div class="meeting-item mb-2 border rounded p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="mb-0">اجتماع {{ $index + 1 }}</h6>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeProgramMeeting(this)">
                                                        <i class="ri-delete-bin-line"></i> حذف
                                                    </button>
                                                </div>
                                                <input type="datetime-local" name="program_meetings[{{ $index }}][date]" 
                                                    class="form-control mb-2 program-meeting-date"
                                                    placeholder="تاريخ ووقت الاجتماع"
                                                    value="{{ !empty($meeting['date']) ? \Carbon\Carbon::parse($meeting['date'])->format('Y-m-d\TH:i') : '' }}">
                                                <input type="text" name="program_meetings[{{ $index }}][title]" 
                                                    class="form-control"
                                                    placeholder="عنوان الاجتماع"
                                                    value="{{ $meeting['title'] ?? '' }}">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="meeting-item mb-2 border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">اجتماع 1</h6>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeProgramMeeting(this)">
                                                    <i class="ri-delete-bin-line"></i> حذف
                                                </button>
                                            </div>
                                            <input type="datetime-local" name="program_meetings[0][date]" 
                                                class="form-control mb-2 program-meeting-date"
                                                placeholder="تاريخ ووقت الاجتماع">
                                            <input type="text" name="program_meetings[0][title]" 
                                                class="form-control"
                                                placeholder="عنوان الاجتماع">
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="addProgramMeeting()">
                                        <i class="ri-add-line"></i> إضافة اجتماع
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="ri-save-line"></i> حفظ
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="cancelProgramMeetingsForm()">
                                        <i class="ri-close-line"></i> إلغاء
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Form Fields Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-card">
                <div class="card-header">
                    <h6 class="card-title">{{ trans('cruds.dynamicService.fields.form_fields') }}</h6>
                </div>
                <div class="card-body">
                    @if ($dynamicService->form_fields)
                        @php
                            $formFields = json_decode($dynamicService->form_fields, true);
                        @endphp
                        <div class="row">
                            @foreach ($formFields as $field)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">{{ $field['label'] }}</h6>
                                            <span class="badge bg-primary-transparent">{{ $field['type'] }}</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <small
                                                    class="text-muted">{{ trans('cruds.dynamicService.fields.field_grid') }}:</small>
                                                <p class="mb-1">{{ $field['grid'] }}</p>
                                            </div>
                                            <div class="col-6">
                                                <small
                                                    class="text-muted">{{ trans('cruds.dynamicService.fields.field_required') }}:</small>
                                                <p class="mb-1">
                                                    <span
                                                        class="badge bg-{{ $field['required'] ? 'success' : 'secondary' }}-transparent">
                                                        {{ $field['required'] ? trans('global.yes') : trans('global.no') }}
                                                    </span>
                                                </p>
                                            </div>
                                            @if (!empty($field['options']))
                                                <div class="col-12">
                                                    <small
                                                        class="text-muted">{{ trans('cruds.dynamicService.fields.field_options') }}:</small>
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach ($field['options'] as $option)
                                                            <li><i
                                                                    class="fas fa-check text-success me-2"></i>{{ $option }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{ trans('cruds.dynamicService.fields.no_form_fields') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Modal -->
    @if ($dynamicService->category === 'training')
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendanceModalLabel">إدارة الحضور - اجتماع <span id="meetingTitle"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- QR Scanner Section -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">ماسح QR Code</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <!-- HTTPS Warning -->
                                        <div id="httpsWarning" class="alert alert-warning" style="display:none;">
                                            <strong>يتطلب الوصول إلى الكاميرا HTTPS</strong><br>
                                            للاستخدام، يرجى الوصول إلى هذه الصفحة عبر HTTPS.<br>
                                            أو يمكنك إدخال رمز QR يدوياً أدناه.
                                        </div>

                                        <!-- Manual Input Fallback -->
                                        <div class="mb-3" id="manualInputSection" style="display:none;">
                                            <label for="manualQrCode">أو أدخل رمز QR يدوياً:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="manualQrCode" placeholder="أدخل رقم المستفيد">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" id="manualScanButton" type="button">مسح</button>
                                                </div>
                                            </div>
                                        </div>

                                        <section class="container" id="cam-content">
                                            <div class="mb-3">
                                                <button class="btn btn-pill btn-lg btn-success" id="startButton">بدء</button>
                                                <button class="btn btn-pill btn-lg btn-info" id="resetButton">إيقاف</button>
                                            </div>

                                            <div>
                                                <div id="video" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
                                            </div>

                                            <div id="sourceSelectPanel" style="display:none">
                                                <label for="sourceSelect">تغيير مصدر الفيديو:</label>
                                                <select id="sourceSelect" style="max-width:400px" class="form-control">
                                                </select>
                                            </div>

                                            <div style="display: none" class="text-center">
                                                <label for="decoding-style">نمط فك التشفير:</label>
                                                <select id="decoding-style" size="1" class="form-control">
                                                    <option value="once">فك التشفير مرة واحدة</option>
                                                    <option value="continuously">فك التشفير المستمر</option>
                                                </select>
                                            </div>

                                            <span>النتيجة:</span>
                                            <pre><code id="result"></code></pre>
                                        </section>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance List Section -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">قائمة الحضور</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="attendanceList">
                                            <div class="text-center text-muted">
                                                <p>جارٍ تحميل قائمة الحضور...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@if ($dynamicService->category === 'training')
    @section('scripts')
        @parent
        <script>
            function toggleProgramMeetingsForm() {
                const display = document.getElementById('program_meetings_display');
                const form = document.getElementById('program_meetings_form');
                
                if (form.style.display === 'none') {
                    display.style.display = 'none';
                    form.style.display = 'block';
                } else {
                    display.style.display = 'block';
                    form.style.display = 'none';
                }
            }

            function cancelProgramMeetingsForm() {
                const display = document.getElementById('program_meetings_display');
                const form = document.getElementById('program_meetings_form');
                
                display.style.display = 'block';
                form.style.display = 'none';
            }

            function addProgramMeeting() {
                const container = document.getElementById('program_meetings_container');
                const meetingCount = container.querySelectorAll('.meeting-item').length;
                const newMeeting = document.createElement('div');
                newMeeting.className = 'meeting-item mb-2 border rounded p-3';
                newMeeting.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">اجتماع ${meetingCount + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeProgramMeeting(this)">
                            <i class="ri-delete-bin-line"></i> حذف
                        </button>
                    </div>
                    <input type="datetime-local" name="program_meetings[${meetingCount}][date]" 
                        class="form-control mb-2 program-meeting-date"
                        placeholder="تاريخ ووقت الاجتماع">
                    <input type="text" name="program_meetings[${meetingCount}][title]" 
                        class="form-control"
                        placeholder="عنوان الاجتماع">
                `;
                container.appendChild(newMeeting);
            }

            function removeProgramMeeting(button) {
                const container = document.getElementById('program_meetings_container');
                const items = container.querySelectorAll('.meeting-item');
                
                // Don't allow removing if it's the last item
                if (items.length > 1) {
                    button.closest('.meeting-item').remove();
                    // Renumber the remaining items
                    container.querySelectorAll('.meeting-item').forEach((item, index) => {
                        item.querySelector('h6').textContent = `اجتماع ${index + 1}`;
                        const dateInput = item.querySelector('input[type="datetime-local"]');
                        const titleInput = item.querySelector('input[type="text"]');
                        dateInput.name = `program_meetings[${index}][date]`;
                        titleInput.name = `program_meetings[${index}][title]`;
                    });
                } else {
                    alert('يجب أن يكون هناك اجتماع واحد على الأقل');
                }
            }

            // Handle form submission
            document.getElementById('programMeetingsForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء الحفظ');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء الحفظ');
                });
            });
        </script>

        <!-- QR Scanner Script -->
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script type="text/javascript">
            let html5QrCode = null;
            let isScanning = false;
            let currentMeetingIndex = null;
            let currentDynamicServiceId = null;

            function openAttendanceModal(meetingIndex, dynamicServiceId) {
                currentMeetingIndex = meetingIndex;
                currentDynamicServiceId = dynamicServiceId;
                
                // Get meeting details
                const meetings = @json($dynamicService->program_meetings ?? []);
                const meeting = meetings[meetingIndex];
                
                if (meeting) {
                    document.getElementById('meetingTitle').textContent = meeting.title || `اجتماع ${meetingIndex + 1}`;
                }
                
                // Load attendance list
                loadAttendanceList(meetingIndex, dynamicServiceId);
                
                // Initialize scanner if available
                if (checkCameraAvailability()) {
                    load_cam();
                }
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
                modal.show();
            }

            // Check if camera access is available (requires HTTPS or localhost)
            function checkCameraAvailability() {
                const isSecureContext = window.isSecureContext || location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
                
                if (!isSecureContext) {
                    document.getElementById('httpsWarning').style.display = 'block';
                    document.getElementById('manualInputSection').style.display = 'block';
                    document.getElementById('cam-content').style.display = 'none';
                    return false;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    document.getElementById('httpsWarning').style.display = 'block';
                    document.getElementById('manualInputSection').style.display = 'block';
                    document.getElementById('cam-content').style.display = 'none';
                    return false;
                }

                return true;
            }

            // Process QR code scan
            function processQrCode(code) {
                if (!code || code.trim() === '') {
                    showNotification('danger', 'يرجى إدخال رمز QR صالح');
                    return;
                }

                const beneficiaryId = code.trim();

                // Send attendance request
                fetch('{{ route("admin.dynamic-services.meeting-attendance") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        beneficiary_id: beneficiaryId,
                        dynamic_service_id: currentDynamicServiceId,
                        meeting_index: currentMeetingIndex,
                        attended: true
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('success', data.message || 'تم تسجيل الحضور بنجاح');
                        // Reload attendance list
                        loadAttendanceList(currentMeetingIndex, currentDynamicServiceId);
                        // Clear manual input
                        document.getElementById('manualQrCode').value = '';
                        // Stop scanning temporarily, restart after delay
                        if (isScanning) {
                            stopScanning().then(() => {
                                setTimeout(() => {
                                    if (!isScanning) {
                                        startScanning();
                                    }
                                }, 2000);
                            });
                        }
                    } else {
                        showNotification('danger', data.message || 'فشل تسجيل الحضور');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('danger', 'حدث خطأ أثناء تسجيل الحضور');
                });
            }

            // Load attendance list
            function loadAttendanceList(meetingIndex, dynamicServiceId) {
                fetch(`{{ route('admin.dynamic-services.meeting-attendance') }}?dynamic_service_id=${dynamicServiceId}&meeting_index=${meetingIndex}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const attendanceList = document.getElementById('attendanceList');
                    if (data.success && data.attendance && data.attendance.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-bordered table-striped">';
                        html += '<thead><tr><th>#</th><th>اسم المستفيد</th><th>رقم الهوية</th><th>حالة الحضور</th><th>ملاحظات</th></tr></thead><tbody>';
                        
                        data.attendance.forEach((item, index) => {
                            html += `<tr>
                                <td>${item.beneficiary_id}</td>
                                <td>${item.beneficiary_name || '-'}</td>
                                <td>${item.identity_number || '-'}</td>
                                <td><span class="badge bg-${item.attended ? 'success' : 'danger'}-transparent">${item.attended ? 'حاضر' : 'غائب'}</span></td>
                                <td>${item.notes || '-'}</td>
                            </tr>`;
                        });
                        
                        html += '</tbody></table></div>';
                        attendanceList.innerHTML = html;
                    } else {
                        attendanceList.innerHTML = '<div class="alert alert-info text-center">لا يوجد حضور مسجل لهذا الاجتماع</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('attendanceList').innerHTML = '<div class="alert alert-danger text-center">حدث خطأ أثناء تحميل قائمة الحضور</div>';
                });
            }

            // Manual input handler
            document.addEventListener('DOMContentLoaded', function() {
                const manualScanButton = document.getElementById('manualScanButton');
                const manualQrCode = document.getElementById('manualQrCode');
                
                if (manualScanButton) {
                    manualScanButton.addEventListener('click', function() {
                        const code = manualQrCode.value;
                        processQrCode(code);
                    });
                }

                if (manualQrCode) {
                    manualQrCode.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            const code = this.value;
                            processQrCode(code);
                        }
                    });
                }
            });

            // Initialize camera if available
            function load_cam() {
                console.log('HTML5 QR Code scanner initialized');

                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length) {
                        const sourceSelect = document.getElementById('sourceSelect');
                        sourceSelect.innerHTML = '';
                        
                        devices.forEach((device) => {
                            const sourceOption = document.createElement('option');
                            sourceOption.text = device.label || device.id;
                            sourceOption.value = device.id;
                            sourceSelect.appendChild(sourceOption);
                        });

                        const sourceSelectPanel = document.getElementById('sourceSelectPanel');
                        sourceSelectPanel.style.display = 'block';

                        // Start button handler
                        const startButton = document.getElementById('startButton');
                        if (startButton) {
                            startButton.addEventListener('click', () => {
                                startScanning();
                            });
                        }

                        // Stop button handler
                        const resetButton = document.getElementById('resetButton');
                        if (resetButton) {
                            resetButton.addEventListener('click', () => {
                                stopScanning();
                            });
                        }

                        // Source select change handler
                        sourceSelect.onchange = () => {
                            if (isScanning) {
                                stopScanning().then(() => {
                                    startScanning();
                                });
                            }
                        };
                    } else {
                        throw new Error('No cameras found');
                    }
                }).catch(err => {
                    console.error('Error getting cameras:', err);
                    document.getElementById('httpsWarning').style.display = 'block';
                    document.getElementById('manualInputSection').style.display = 'block';
                    document.getElementById('cam-content').style.display = 'none';
                });
            }

            function startScanning() {
                if (isScanning) {
                    return;
                }

                const sourceSelect = document.getElementById('sourceSelect');
                const cameraId = sourceSelect.value;

                if (!cameraId) {
                    showNotification('danger', 'يرجى اختيار كاميرا');
                    return;
                }

                const videoElement = document.getElementById('video');
                html5QrCode = new Html5Qrcode("video");
                isScanning = true;

                html5QrCode.start(
                    cameraId,
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 }
                    },
                    (decodedText, decodedResult) => {
                        console.log('QR Code detected:', decodedText);
                        processQrCode(decodedText);
                    },
                    (errorMessage) => {
                        // Ignore errors, just keep scanning
                    }
                ).catch((err) => {
                    console.error('Error starting scanner:', err);
                    isScanning = false;
                    showNotification('danger', 'فشل بدء الكاميرا');
                });
            }

            function stopScanning() {
                if (html5QrCode && isScanning) {
                    return html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        isScanning = false;
                        const resultElement = document.getElementById('result');
                        if (resultElement) {
                            resultElement.textContent = '';
                        }
                        console.log('Scanner stopped.');
                    }).catch((err) => {
                        console.error('Error stopping scanner:', err);
                        isScanning = false;
                    });
                }
                return Promise.resolve();
            }

            // Stop scanning when modal is closed
            document.getElementById('attendanceModal').addEventListener('hidden.bs.modal', function () {
                stopScanning();
            });

            // Notification function
            function showNotification(type, message) {
                // You can use your existing notification system here
                // For now, using alert as fallback
                if (typeof AIZ !== 'undefined' && AIZ.plugins && AIZ.plugins.notify) {
                    AIZ.plugins.notify(type, message);
                } else {
                    alert(message);
                }
            }
        </script>
    @endsection
@endif
