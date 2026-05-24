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
            <input type="hidden" name="session_number" id="session_number_input" value="1">
            @include('utilities.form.text', [
                'name' => 'session_number_picker',
                'label' => 'رقم الجلسة',
                'isRequired' => false,
                'grid' => 'col-md-4',
                'value' => 1,
                'attributes' => 'type="number" min="1" onchange="document.getElementById(\'session_number_input\').value=this.value"',
            ])
            @include('utilities.form.date', [
                'name' => 'session_date',
                'label' => 'تاريخ الجلسة',
                'isRequired' => false,
                'grid' => 'col-md-4',
            ])
            @include('utilities.form.text', [
                'name' => 'session_time',
                'label' => 'وقت الجلسة',
                'isRequired' => false,
                'grid' => 'col-md-4',
                'attributes' => 'type="time"',
            ])
        </div>
        <p class="text-muted small mb-0 mt-2">يُرسَل الموعد للمستفيد ويظهر الباركود في صفحته. عند قص الباركود سجّل الحضور.</p>
    </div>
</div>
