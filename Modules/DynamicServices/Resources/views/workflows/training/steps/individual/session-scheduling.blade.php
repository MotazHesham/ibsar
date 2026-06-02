<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">جدولة مواعيد الجلسات ({{ $sessionsCount }} جلسة)</h6>
    </div>
    <div class="card-body">
        @if (!empty($sessions))
            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>الجلسة</th>
                            <th>التاريخ</th>
                            <th>الوقت</th>
                            <th>الحضور</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            <tr>
                                <td>{{ $session['number'] }}</td>
                                <td>{{ $session['date'] ?? '—' }}</td>
                                <td>{{ $session['time'] ?? '—' }}</td>
                                <td>
                                    @if (!empty($session['attended']))
                                        <span class="badge bg-success">حضر</span>
                                    @elseif (!empty($session['date']))
                                        <span class="badge bg-warning">مجدول</span>
                                    @else
                                        <span class="badge bg-light text-muted">لم يُجدول</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="row">
            @php
                $sessionOptions = !empty($sessions)
                    ? collect($sessions)
                        ->mapWithKeys(
                            fn($session) => [
                                $session['number'] => 'الجلسة ' . $session['number'],
                            ],
                        )
                        ->all()
                    : collect(range(1, max(1, (int) $sessionsCount)))
                        ->mapWithKeys(
                            fn($number) => [
                                $number => 'الجلسة ' . $number,
                            ],
                        )
                        ->all();
            @endphp
            @include('utilities.form.select', [
                'id' => 'session_number_input',
                'name' => 'session_number',
                'label' => 'رقم الجلسة',
                'isRequired' => true,
                'grid' => 'col-md-4',
                'options' => $sessionOptions,
                'value' => array_key_first($sessionOptions),
                'helperBlock' => '',
            ])
            @include('utilities.form.date', [
                'id' => 'session_date',
                'name' => 'session_date',
                'label' => 'تاريخ الجلسة',
                'isRequired' => false,
                'grid' => 'col-md-4',
                'helperBlock' => '',
            ])
            @include('utilities.form.time', [
                'id' => 'session_time',
                'name' => 'session_time',
                'label' => 'وقت الجلسة',
                'isRequired' => false,
                'grid' => 'col-md-4',
                'helperBlock' => '',
            ])
        </div>
        <div class="row mt-2">
            @include('utilities.form.text', [
                'id' => 'attendance_barcode',
                'name' => 'attendance_barcode',
                'label' => 'باركود حضور الجلسة',
                'isRequired' => false,
                'grid' => 'col-md-8',
                'helperBlock' => 'يمكن إدخال الباركود يدويًا أو استخدام زر ماسح QR.',
            ])
            <div class="col-md-4 mb-3 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100" id="open-attendance-qr-scanner">
                    <i class="ri-qr-code-line me-1"></i>
                    ماسح QR للحضور
                </button>
            </div>
        </div>
        <button type="submit" class="d-none" id="submit-scanned-attendance" name="workflow_action"
            value="mark_session_attended"></button>
        <p class="text-muted small mb-0 mt-2">يُرسَل الموعد للمستفيد ويظهر الباركود في صفحته. عند قص الباركود سجّل
            الحضور.</p>
    </div>
</div>

<div class="modal fade" id="attendanceQrScannerModal" tabindex="-1" aria-labelledby="attendanceQrScannerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="attendanceQrScannerModalLabel">مسح QR لتسجيل الحضور</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="attendance-qr-reader" style="width: 100%; min-height: 260px;"></div>
                <p class="small text-muted mt-2 mb-0">وجّه الكاميرا إلى QR الخاص بالمستفيد. سيتم تسجيل الحضور تلقائيًا بعد المسح.</p>
            </div>
        </div>
    </div>
</div>
