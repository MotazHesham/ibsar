<div class="alert alert-info mb-3">
    <strong>البدء في البرنامج</strong>
    <p class="mb-2 small">عند حضور المستفيد وقص الباركود يُسجَّل الحضور. بعد انتهاء الجلسات المقررة انتقل للاختبار.</p>
    @if (!empty($groupSchedule))
        <div class="small">
            <strong>جدول اللقاءات:</strong>
            {{ $groupSchedule['start_date'] ?? '' }} — {{ $groupSchedule['end_date'] ?? '' }}
            ({{ implode('، ', $groupSchedule['days'] ?? []) }})
        </div>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            @include('utilities.form.text', [
                'id' => 'group_attendance_barcode',
                'name' => 'attendance_barcode',
                'label' => 'باركود حضور المستفيد',
                'isRequired' => false,
                'grid' => 'col-md-8',
                'helperBlock' => 'امسح باركود المستفيد ليتم تسجيل حضوره في البرنامج وتحويل قيد محاسبي تلقائياً.',
            ])
            <div class="col-md-4 mb-3 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100" id="open-group-attendance-qr-scanner">
                    <i class="ri-qr-code-line me-1"></i>
                    ماسح QR للحضور
                </button>
            </div>
        </div>
        <button type="submit" class="d-none" id="submit-group-scanned-attendance" name="workflow_action"
            value="mark_program_attendance"></button>
    </div>
</div>

<div class="modal fade" id="groupAttendanceQrScannerModal" tabindex="-1" aria-labelledby="groupAttendanceQrScannerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="groupAttendanceQrScannerModalLabel">مسح QR لتسجيل حضور البرنامج</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="group-attendance-qr-reader" style="width: 100%; min-height: 260px;"></div>
                <p class="small text-muted mt-2 mb-0">عند نجاح المسح سيتم تسجيل الحضور وتحويل القيد المحاسبي مباشرة.</p>
            </div>
        </div>
    </div>
</div>
