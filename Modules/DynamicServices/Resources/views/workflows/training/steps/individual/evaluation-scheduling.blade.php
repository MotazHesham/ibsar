<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">جدولة موعد التقييم</h6>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">حدد موعد التقييم (الاستقبال، البحث الاجتماعي، التدريب) وسيُرسَل للمستفيد.</p>
        <div class="row">
            @include('utilities.form.date', [
                'name' => 'evaluation_date',
                'label' => 'تاريخ التقييم',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $evaluationAppointment['date'] ?? '',
            ])
            @include('utilities.form.text', [
                'name' => 'evaluation_time',
                'label' => 'وقت التقييم',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $evaluationAppointment['time'] ?? '',
                'attributes' => 'type="time"',
            ])
            <div class="col-md-12 mb-3">
                <label class="form-label">نوع التقييم <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach ($evaluationAppointmentTypes as $key => $label)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="evaluation_types[]"
                                value="{{ $key }}" id="eval_type_{{ $key }}"
                                @checked(in_array($key, $evaluationAppointment['types'] ?? []))>
                            <label class="form-check-label" for="eval_type_{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
