<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">نموذج التقييم</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">تاريخ الميلاد</label>
                <input type="text" class="form-control" readonly
                    value="{{ $beneficiaryDob ?? 'غير متوفر' }}">
            </div>
            @include('utilities.form.select', [
                'name' => 'visual_status',
                'id' => 'visual_status',
                'label' => 'الحالة البصرية',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => $visualStatusOptions,
                'value' => $evaluation['visual_status'] ?? '',
            ])
            <div class="col-md-12 mb-3" id="visual_status_other_wrapper" style="display: none;">
                @include('utilities.form.text', [
                    'name' => 'visual_status_other',
                    'label' => 'توضيح الحالة البصرية',
                    'isRequired' => false,
                    'grid' => 'col-md-12',
                    'value' => $evaluation['visual_status_other'] ?? '',
                ])
            </div>
            @include('utilities.form.text', [
                'name' => 'sessions_count',
                'label' => 'عدد الجلسات التدريبية',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $evaluation['sessions_count'] ?? '',
                'attributes' => 'type="number" min="1" max="100"',
            ])
            @include('utilities.form.select', [
                'name' => 'evaluator_id',
                'label' => 'المُقيِّم (مدرب)',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => $trainers->prepend('اختر المدرب', '')->toArray(),
                'search' => true,
                'value' => $evaluation['evaluator_id'] ?? '',
            ])
            @include('utilities.form.radio', [
                'name' => 'qualified',
                'label' => 'هل المستفيد مؤهل للتدريب؟',
                'isRequired' => true,
                'grid' => 'col-md-12',
                'options' => ['yes' => 'يعتمد', 'no' => 'لا يعتمد'],
                'value' => isset($evaluation['qualified']) ? ($evaluation['qualified'] ? 'yes' : 'no') : '',
            ])
            @include('utilities.form.textarea', [
                'name' => 'evaluation_notes',
                'label' => 'ملاحظات التقييم',
                'isRequired' => false,
                'grid' => 'col-md-12',
                'value' => $evaluation['notes'] ?? '',
            ])
        </div>
    </div>
</div>
