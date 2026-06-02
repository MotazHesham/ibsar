<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">صرف المالية</h6>
    </div>
    <div class="card-body">
        @if (!empty($financialReview['amount']))
            <div class="alert alert-primary mb-3">
                المبلغ المعتمد: <strong>{{ $financialReview['amount'] }}</strong>
            </div>
        @endif
        @include('utilities.form.text', [
            'name' => 'disbursement_reference',
            'label' => 'مرجع القيد / الصرف',
            'isRequired' => false,
            'grid' => 'col-md-6',
            'value' => $financialReview['disbursement_reference'] ?? '',
        ])
        @include('utilities.form.textarea', [
            'name' => 'finance_notes',
            'label' => 'ملاحظات المالية',
            'isRequired' => false,
            'value' => $financialReview['notes'] ?? '',
        ])
        <p class="text-muted small mb-0">بعد تأكيد الصرف تُرسَل رسالة للمستفيد ويُطلب استبيان الرضا.</p>
    </div>
</div>
