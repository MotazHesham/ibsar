<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">اعتماد قسم المشاريع — استلام عيني</h6>
    </div>
    <div class="card-body">
        @if (!empty($allocation))
            <div class="alert alert-light border mb-3 small">
                <strong>التخصيص:</strong> {{ $allocation['item_name'] ?? '' }} — الكمية: {{ $allocation['quantity'] ?? '' }}
            </div>
        @endif
        @include('utilities.form.radio', [
            'name' => 'requires_training',
            'id' => 'requires_training',
            'label' => 'هل يحتاج تدريباً؟',
            'isRequired' => true,
            'grid' => 'col-md-12',
            'options' => ['yes' => 'نعم', 'no' => 'لا'],
            'value' => isset($projectsReview['requires_training']) ? ($projectsReview['requires_training'] ? 'yes' : 'no') : '',
        ])
        <div class="col-md-12 mb-3" id="training_type_wrapper" style="display: none;">
            @include('utilities.form.radio', [
                'name' => 'training_type',
                'label' => 'نوع التدريب',
                'isRequired' => false,
                'grid' => 'col-md-12',
                'options' => ['individual' => 'فردي', 'group' => 'جماعي'],
                'value' => $projectsReview['training_type'] ?? 'individual',
            ])
        </div>
        @include('utilities.form.text', [
            'name' => 'pickup_deadline_days',
            'label' => 'مهلة الحضور للاستلام (أيام)',
            'isRequired' => false,
            'grid' => 'col-md-6',
            'value' => $projectsReview['pickup_deadline_days'] ?? 14,
            'attributes' => 'type="number" min="1" max="30"',
        ])
        @include('utilities.form.textarea', [
            'name' => 'projects_notes',
            'label' => 'ملاحظات المشاريع',
            'isRequired' => false,
            'value' => $projectsReview['notes'] ?? '',
        ])
        <p class="text-muted small mb-0">بعد الاعتماد: يُرسَل للمستفيد طلب تأكيد الاستلام. عند عدم الحضور خلال المهلة تُعاد الكمية لمستفيد آخر.</p>
    </div>
</div>
