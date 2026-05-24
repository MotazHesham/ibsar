<div class="card mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0">استلام الطلب (المخزون)</h6>
    </div>
    <div class="card-body">
        @if (!empty($allocation))
            <div class="alert alert-light border mb-3 small">
                <strong>الصنف:</strong> {{ $allocation['item_name'] ?? '' }} —
                <strong>الكمية:</strong> {{ $allocation['quantity'] ?? '' }}
            </div>
        @endif
        @if (!empty($pickup['date']))
            <div class="alert alert-info mb-3">
                موعد الاستلام المؤكد: {{ $pickup['date'] }} {{ $pickup['time'] ?? '' }}
            </div>
        @else
            <div class="alert alert-warning mb-3">
                بانتظار تأكيد المستفيد لموعد الاستلام من صفحته.
            </div>
        @endif
        @if (!empty($pickup['confirmed_at']))
            @include('utilities.form.text', [
                'name' => 'otp_code',
                'label' => 'رمز OTP من المستفيد',
                'isRequired' => true,
                'grid' => 'col-md-6',
            ])
            <p class="text-muted small mb-0">أدخل الرمز الذي وصل للمستفيد. عند التأكيد تُخصم الكمية من المخزون.</p>
        @endif
    </div>
</div>
