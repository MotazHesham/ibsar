<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">اعتماد الباحث الاجتماعي</h6>
    </div>
    <div class="card-body">
        @if ($isFinancial)
            <p class="text-muted small">تظهر المبالغ المرصودة لتخصيصها للمستفيد. بعد الاعتماد يُرسَل الطلب لقسم المشاريع.</p>
            @include('utilities.form.text', [
                'name' => 'allocated_amount',
                'label' => 'المبلغ المرصود',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $researcherReview['allocated_amount'] ?? '',
                'attributes' => 'type="number" step="0.01" min="0"',
            ])
        @else
            <p class="text-muted small mb-3">يرتبط الاستلام العيني بالمخزون. عند عدم التوفر يُعتذر للمستفيد تلقائياً.</p>
            @include('utilities.form.select', [
                'name' => 'donation_item_id',
                'label' => 'الصنف من المخزون',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => collect($availableStockItems)->prepend('اختر الصنف', ''),
                'value' => $allocation['donation_item_id'] ?? '',
            ])
            @include('utilities.form.text', [
                'name' => 'allocated_quantity',
                'label' => 'الكمية المطلوبة',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'value' => $allocation['quantity'] ?? '',
                'attributes' => 'type="number" step="0.01" min="0.01"',
            ])
            @include('utilities.form.radio', [
                'name' => 'stock_available',
                'label' => 'توفر المخزون',
                'isRequired' => true,
                'grid' => 'col-md-12',
                'options' => ['yes' => 'متوفر', 'no' => 'غير متوفر'],
                'value' => 'yes',
            ])
        @endif
        @include('utilities.form.textarea', [
            'name' => 'researcher_notes',
            'label' => 'ملاحظات الباحث',
            'isRequired' => false,
            'value' => $researcherReview['notes'] ?? '',
        ])
        @if ($isFinancial)
            @include('utilities.form.textarea', [
                'name' => 'incomplete_message',
                'label' => 'الأمور الناقصة (عند اختيار «طلب استكمال الوثائق»)',
                'isRequired' => false,
            ])
        @endif
    </div>
</div>
