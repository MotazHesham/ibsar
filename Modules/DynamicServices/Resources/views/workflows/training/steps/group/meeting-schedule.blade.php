<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">جدول اللقاءات</h6>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">بعد اكتمال اعتماد جميع المتقدمين، حدد موعد البدء والنهاية والأيام والساعات — وسيتم الإرسال لجميع المعتمدين دفعة واحدة مع ظهور الباركود في سجلاتهم.</p>
        <div class="row">
            @include('utilities.form.date', [
                'id' => 'schedule_start_date',
                'name' => 'schedule_start_date',
                'label' => 'تاريخ البدء',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $groupSchedule['start_date'] ?? '',
            ])
            @include('utilities.form.date', [
                'id' => 'schedule_end_date',
                'name' => 'schedule_end_date',
                'label' => 'تاريخ النهاية',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $groupSchedule['end_date'] ?? '',
            ])
            @include('utilities.form.text', [
                'id' => 'schedule_start_time',
                'name' => 'schedule_start_time',
                'label' => 'وقت البدء',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $groupSchedule['start_time'] ?? '',
                'attributes' => 'type="time"',
            ])
            @include('utilities.form.text', [
                'id' => 'schedule_end_time',
                'name' => 'schedule_end_time',
                'label' => 'وقت النهاية',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $groupSchedule['end_time'] ?? '',
                'attributes' => 'type="time"',
            ])
            <div class="col-md-12 mb-3">
                <label class="form-label">أيام اللقاءات <span class="text-danger">*</span></label>
                @php
                    $days = [
                        'sunday' => 'الأحد',
                        'monday' => 'الإثنين',
                        'tuesday' => 'الثلاثاء',
                        'wednesday' => 'الأربعاء',
                        'thursday' => 'الخميس',
                        'friday' => 'الجمعة',
                        'saturday' => 'السبت',
                    ];
                    $selectedDays = $groupSchedule['days'] ?? [];
                @endphp
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($days as $key => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="schedule_days[]"
                                value="{{ $key }}" id="day_{{ $key }}" @checked(in_array($key, $selectedDays))>
                            <label class="form-check-label" for="day_{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
