<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">نموذج الاختبار</h6>
    </div>
    <div class="card-body">
        @if (!empty($testResult))
            <div class="alert {{ !empty($testResult['passed']) ? 'alert-success' : 'alert-danger' }} mb-3">
                <strong>متوسط الدرجة: {{ $testResult['average'] ?? 0 }}%</strong>
                — {{ !empty($testResult['passed']) ? 'اجتاز' : 'لم يجتز' }}
                (معيار الاجتياز: {{ $passThreshold }}%)
            </div>
            @if (!empty($testResult['passed']))
                @if (!empty($testResult['needs_device']))
                    <p class="small text-muted">البرنامج يحتاج تسليم جهاز — سيتم إشعار المخزون.</p>
                @else
                    <p class="small text-muted">سيتم إرسال الشهادة لحساب المستفيد بعد إكمال استبيان الرضا.</p>
                @endif
                @if (empty($testResult['satisfaction_completed']))
                    <p class="small text-warning">يجب إكمال استبيان تقييم الرضا قبل إنهاء الطلب.</p>
                @endif
            @endif
        @else
            <p class="text-muted small mb-3">معايير الاختبار الثابتة — أدخل الدرجة لكل معيار (0-100):</p>
            <div class="row">
                @foreach ($testCriteria as $key => $label)
                    @include('utilities.form.text', [
                        'name' => "test_scores[{$key}]",
                        'label' => $label,
                        'isRequired' => true,
                        'grid' => 'col-md-6',
                        'value' => $testResult['scores'][$key] ?? '',
                        'attributes' => 'type="number" min="0" max="100"',
                    ])
                @endforeach
                @include('utilities.form.radio', [
                    'name' => 'needs_device',
                    'label' => 'هل البرنامج يحتاج تسليم جهاز؟',
                    'isRequired' => true,
                    'grid' => 'col-md-12',
                    'options' => ['yes' => 'نعم', 'no' => 'لا'],
                    'value' => isset($testResult['needs_device']) ? ($testResult['needs_device'] ? 'yes' : 'no') : '',
                ])
            </div>
        @endif
    </div>
</div>
