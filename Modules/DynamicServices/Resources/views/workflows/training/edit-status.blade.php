@if (!empty($workflowContext))
    @php
        $service = $workflowContext['service'];
        $dynamicServiceOrder = $workflowContext['dynamicServiceOrder'];
        $availableActions = $workflowContext['availableActions'] ?? [];
        $currentStep = $workflowContext['currentStep'];
        $categoryLabel =
            \Modules\DynamicServices\Models\DynamicService::CATEGORIES[$service->category] ?? $service->category;
        $isGroup = $service->service_type === 'group';

        $trainers = $workflowContext['trainers'] ?? collect();
        $visualStatusOptions = $workflowContext['visualStatusOptions'] ?? [];
        $evaluationAppointmentTypes = $workflowContext['evaluationAppointmentTypes'] ?? [];
        $testCriteria = $workflowContext['testCriteria'] ?? [];
        $passThreshold = $workflowContext['passThreshold'] ?? 70;
        $beneficiaryDob = $workflowContext['beneficiaryDob'] ?? null;
        $evaluation = $workflowContext['evaluation'] ?? [];
        $evaluationAppointment = $workflowContext['evaluationAppointment'] ?? [];
        $financial = $workflowContext['financial'] ?? [];
        $sessions = $workflowContext['sessions'] ?? [];
        $sessionsCount = $workflowContext['sessionsCount'] ?? 0;
        $groupSchedule = $workflowContext['groupSchedule'] ?? [];
        $testResult = $workflowContext['testResult'] ?? [];
        $hasDonationAllocation = $workflowContext['hasDonationAllocation'] ?? false;
        $programMeetings = $workflowContext['programMeetings'] ?? [];
        $groupApplicants = $workflowContext['groupApplicants'] ?? collect();
        $groupApprovedCount = $workflowContext['groupApprovedCount'] ?? 0;
        $groupPendingCount = $workflowContext['groupPendingCount'] ?? 0;
        $canScheduleGroupMeetings = $workflowContext['canScheduleGroupMeetings'] ?? false;
    @endphp

    <div class="alert alert-info py-2 mb-3">
        <small>
            <strong>{{ $isGroup ? 'التدريب الجماعي' : 'التدريب الفردي' }}</strong>
            — {{ $categoryLabel }}
        </small>
    </div>

    @include('dynamicservices::workflows.partials.progress', ['workflowContext' => $workflowContext])

    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="text-muted">المرحلة الحالية</span>
        <span class="badge bg-primary-transparent">{{ $workflowContext['currentStepLabel'] }}</span>
    </div>

    @if ($isGroup)
        <div class="card mb-3">
            <div class="card-header py-2">
                <h6 class="mb-0">قائمة المتقدمين للبرنامج</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2 mb-3">
                    <small>المعتمدون: {{ $groupApprovedCount }} | بانتظار الاعتماد: {{ $groupPendingCount }}</small>
                </div>
                @if ($groupApplicants->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>المستفيد</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groupApplicants as $applicant)
                                    <tr>
                                        <td>{{ $applicant['name'] ?? ('مستفيد #' . ($applicant['beneficiary_order_id'] ?? '')) }}</td>
                                        <td>
                                            @if (!empty($applicant['is_approved']))
                                                <span class="badge bg-success">معتمد</span>
                                            @else
                                                <span class="badge bg-warning">قيد الاعتماد</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($workflowContext['isRejected'])
        <div class="alert alert-danger">
            تم رفض الطلب
            @if ($beneficiaryOrder->refused_reason)
                <div class="mt-2"><strong>السبب:</strong> {{ $beneficiaryOrder->refused_reason }}</div>
            @endif
        </div>
    @elseif ($workflowContext['isCompleted'] || $beneficiaryOrder->done)
        @include('dynamicservices::workflows.training.partials.completed')
    @elseif (count($availableActions) > 0)
        <form action="{{ route('admin.beneficiary-orders.update-status', $beneficiaryOrder) }}" method="POST"
            id="training-workflow-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="dynamic_workflow" value="1">

            @if (!$isGroup)
                @includeWhen(
                    $currentStep === 'evaluation_scheduling',
                    'dynamicservices::workflows.training.steps.individual.evaluation-scheduling')
                @includeWhen(
                    $currentStep === 'evaluation',
                    'dynamicservices::workflows.training.steps.individual.evaluation-form')
                @includeWhen(
                    $currentStep === 'financial_approval',
                    'dynamicservices::workflows.training.steps.individual.financial-approval')
                @includeWhen(
                    $currentStep === 'donation_allocation',
                    'dynamicservices::workflows.training.steps.individual.donation-allocation')
                @includeWhen(
                    $currentStep === 'session_scheduling',
                    'dynamicservices::workflows.training.steps.individual.session-scheduling')
                @includeWhen(
                    $currentStep === 'testing',
                    'dynamicservices::workflows.training.steps.shared.testing-form')
            @else
                @includeWhen(
                    $currentStep === 'send_meeting_schedule',
                    'dynamicservices::workflows.training.steps.group.meeting-schedule')
                @includeWhen(
                    $currentStep === 'start_program',
                    'dynamicservices::workflows.training.steps.group.start-program')
                @includeWhen(
                    $currentStep === 'testing',
                    'dynamicservices::workflows.training.steps.shared.testing-form')
            @endif

            @include('utilities.form.textarea', [
                'name' => 'note',
                'label' => 'cruds.beneficiaryOrder.fields.note',
                'isRequired' => false,
                'value' => $beneficiaryOrder->note,
            ])

            <div id="workflow-reject-reason-wrapper" class="mb-3" style="display: none;">
                @include('utilities.form.textarea', [
                    'name' => 'reason',
                    'label' => 'cruds.beneficiaryOrder.fields.refused_reason',
                    'isRequired' => false,
                    'value' => $beneficiaryOrder->refused_reason,
                ])
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach ($availableActions as $action)
                    @php
                        $btnClass = match ($action['type'] ?? 'primary') {
                            'success' => 'btn-success',
                            'danger' => 'btn-danger',
                            'warning' => 'btn-warning',
                            'secondary' => 'btn-secondary',
                            default => 'btn-primary',
                        };
                    @endphp
                    <button type="submit" name="workflow_action" value="{{ $action['key'] }}"
                        class="btn {{ $btnClass }}"
                        @if (!empty($action['requires_reason'])) data-requires-reason="1" @endif
                        @if (!empty($action['session_number'])) onclick="document.getElementById('session_number_input').value='{{ $action['session_number'] }}'" @endif>
                        {{ $action['label'] }}
                    </button>
                @endforeach
            </div>
        </form>
    @elseif ($isGroup && $currentStep === 'send_meeting_schedule' && !$canScheduleGroupMeetings)
        <div class="alert alert-warning">
            لا يمكن تحديد موعد اللقاءات قبل اكتمال اعتماد جميع المتقدمين.
        </div>
    @endif

    @include('dynamicservices::workflows.training.partials.history', [
        'dynamicServiceOrder' => $dynamicServiceOrder,
        'workflowContext' => $workflowContext,
    ])
@endif

@section('scripts')
    @parent
    <script src="https://unpkg.com/html5-qrcode" defer></script>
    <script>
        document.querySelectorAll('#training-workflow-form [data-requires-reason="1"]').forEach(function(button) {
            button.addEventListener('click', function() {
                var wrapper = document.getElementById('workflow-reject-reason-wrapper');
                if (wrapper) wrapper.style.display = 'block';
            });
        });

        var visualStatus = document.getElementById('visual_status');
        var otherWrapper = document.getElementById('visual_status_other_wrapper');
        if (visualStatus && otherWrapper) {
            function toggleVisualOther() {
                otherWrapper.style.display = visualStatus.value === 'other' ? 'block' : 'none';
            }
            visualStatus.addEventListener('change', toggleVisualOther);
            toggleVisualOther();
        }

        var scannerButton = document.getElementById('open-attendance-qr-scanner');
        var barcodeInput = document.getElementById('attendance_barcode');
        var sessionNumberInput = document.getElementById('session_number_input');
        var submitScannedAttendance = document.getElementById('submit-scanned-attendance');
        var scannerModalElement = document.getElementById('attendanceQrScannerModal');
        var scannerModal = scannerModalElement ? new bootstrap.Modal(scannerModalElement) : null;
        var qrScanner = null;
        var scannerStarted = false;

        function parseSessionNumber(payload) {
            var match = (payload || '').trim().match(/^training_attendance:\d+:(\d+)$/);
            return match && match[1] ? match[1] : null;
        }

        function stopScanner() {
            if (qrScanner && scannerStarted) {
                qrScanner.stop().catch(function() {}).finally(function() {
                    scannerStarted = false;
                });
            }
        }

        function applyScannedPayload(payload) {
            if (barcodeInput) {
                barcodeInput.value = payload;
            }

            var sessionNumber = parseSessionNumber(payload);
            if (sessionNumber && sessionNumberInput) {
                sessionNumberInput.value = sessionNumber;
            }

            if (submitScannedAttendance) {
                submitScannedAttendance.click();
            }
        }

        if (scannerButton && scannerModal && scannerModalElement) {
            scannerButton.addEventListener('click', function() {
                scannerModal.show();
            });

            scannerModalElement.addEventListener('shown.bs.modal', function() {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('تعذر تحميل ماسح QR. حاول تحديث الصفحة.');
                    return;
                }

                if (!qrScanner) {
                    qrScanner = new Html5Qrcode('attendance-qr-reader');
                }

                qrScanner.start(
                    {
                        facingMode: 'environment',
                    },
                    {
                        fps: 10,
                        qrbox: {
                            width: 220,
                            height: 220
                        },
                    },
                    function(decodedText) {
                        applyScannedPayload(decodedText);
                        stopScanner();
                        scannerModal.hide();
                    },
                    function() {}
                ).then(function() {
                    scannerStarted = true;
                }).catch(function() {
                    alert('تعذر تشغيل الكاميرا. تأكد من منح صلاحية الوصول.');
                });
            });

            scannerModalElement.addEventListener('hidden.bs.modal', function() {
                stopScanner();
            });
        }

        if (barcodeInput && sessionNumberInput) {
            barcodeInput.addEventListener('change', function() {
                var sessionNumber = parseSessionNumber(barcodeInput.value || '');
                if (sessionNumber) {
                    sessionNumberInput.value = sessionNumber;
                }
            });
        }

        var groupScannerButton = document.getElementById('open-group-attendance-qr-scanner');
        var groupBarcodeInput = document.getElementById('group_attendance_barcode');
        var groupSubmitAttendance = document.getElementById('submit-group-scanned-attendance');
        var groupScannerModalElement = document.getElementById('groupAttendanceQrScannerModal');
        var groupScannerModal = groupScannerModalElement ? new bootstrap.Modal(groupScannerModalElement) : null;
        var groupQrScanner = null;
        var groupScannerStarted = false;

        function stopGroupScanner() {
            if (groupQrScanner && groupScannerStarted) {
                groupQrScanner.stop().catch(function() {}).finally(function() {
                    groupScannerStarted = false;
                });
            }
        }

        function applyGroupScannedPayload(payload) {
            if (groupBarcodeInput) {
                groupBarcodeInput.value = payload;
            }
            if (groupSubmitAttendance) {
                groupSubmitAttendance.click();
            }
        }

        if (groupScannerButton && groupScannerModal && groupScannerModalElement) {
            groupScannerButton.addEventListener('click', function() {
                groupScannerModal.show();
            });

            groupScannerModalElement.addEventListener('shown.bs.modal', function() {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('تعذر تحميل ماسح QR. حاول تحديث الصفحة.');
                    return;
                }

                if (!groupQrScanner) {
                    groupQrScanner = new Html5Qrcode('group-attendance-qr-reader');
                }

                groupQrScanner.start(
                    {
                        facingMode: 'environment',
                    },
                    {
                        fps: 10,
                        qrbox: {
                            width: 220,
                            height: 220
                        },
                    },
                    function(decodedText) {
                        applyGroupScannedPayload(decodedText);
                        stopGroupScanner();
                        groupScannerModal.hide();
                    },
                    function() {}
                ).then(function() {
                    groupScannerStarted = true;
                }).catch(function() {
                    alert('تعذر تشغيل الكاميرا. تأكد من منح صلاحية الوصول.');
                });
            });

            groupScannerModalElement.addEventListener('hidden.bs.modal', function() {
                stopGroupScanner();
            });
        }

        var testScannerButton = document.getElementById('open-test-qr-scanner');
        var testBarcodeInput = document.getElementById('test_attendance_barcode');
        var testCriteriaWrapper = document.getElementById('test-criteria-wrapper');
        var testScannerModalElement = document.getElementById('testQrScannerModal');
        var testScannerModal = testScannerModalElement ? new bootstrap.Modal(testScannerModalElement) : null;
        var testQrScanner = null;
        var testScannerStarted = false;

        function isValidTestBarcode(payload) {
            return /^training_test:\d+$/.test((payload || '').trim());
        }

        function toggleTestCriteria() {
            if (!testBarcodeInput || !testCriteriaWrapper) return;
            testCriteriaWrapper.style.display = isValidTestBarcode(testBarcodeInput.value) ? '' : 'none';
        }

        function stopTestScanner() {
            if (testQrScanner && testScannerStarted) {
                testQrScanner.stop().catch(function() {}).finally(function() {
                    testScannerStarted = false;
                });
            }
        }

        if (testBarcodeInput) {
            testBarcodeInput.addEventListener('input', toggleTestCriteria);
            toggleTestCriteria();
        }

        if (testScannerButton && testScannerModal && testScannerModalElement) {
            testScannerButton.addEventListener('click', function() {
                testScannerModal.show();
            });

            testScannerModalElement.addEventListener('shown.bs.modal', function() {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('تعذر تحميل ماسح QR. حاول تحديث الصفحة.');
                    return;
                }

                if (!testQrScanner) {
                    testQrScanner = new Html5Qrcode('test-qr-reader');
                }

                testQrScanner.start({
                    facingMode: 'environment',
                }, {
                    fps: 10,
                    qrbox: { width: 220, height: 220 },
                }, function(decodedText) {
                    if (testBarcodeInput) {
                        testBarcodeInput.value = decodedText;
                    }
                    toggleTestCriteria();
                    stopTestScanner();
                    testScannerModal.hide();
                }, function() {}).then(function() {
                    testScannerStarted = true;
                }).catch(function() {
                    alert('تعذر تشغيل الكاميرا. تأكد من منح صلاحية الوصول.');
                });
            });

            testScannerModalElement.addEventListener('hidden.bs.modal', function() {
                stopTestScanner();
            });
        }
    </script>
@endsection
