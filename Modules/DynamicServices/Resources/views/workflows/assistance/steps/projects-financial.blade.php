<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">اعتماد قسم المشاريع — مساعدة مالية</h6>
    </div>
    <div class="card-body">
        @if (!empty($researcherReview['allocated_amount']))
            <div class="alert alert-light border mb-3 small">
                المبلغ المرصود من الباحث: <strong>{{ $researcherReview['allocated_amount'] }}</strong>
            </div>
        @endif
        @include('utilities.form.text', [
            'name' => 'approved_amount',
            'label' => 'المبلغ المعتمد',
            'isRequired' => true,
            'grid' => 'col-md-6',
            'value' => $financialReview['amount'] ?? $researcherReview['allocated_amount'] ?? '',
            'attributes' => 'type="number" step="0.01" min="0"',
        ])
        @include('utilities.form.textarea', [
            'name' => 'projects_notes',
            'label' => 'ملاحظات المشاريع',
            'isRequired' => false,
            'value' => $projectsReview['notes'] ?? '',
        ])
        <p class="text-muted small mb-0">بعد الاعتماد يظهر طلب الصرف لدى المالية.</p>
    </div>
</div>
