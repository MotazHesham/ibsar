<div class="alert alert-info mb-3">
    <strong>البدء في البرنامج</strong>
    <p class="mb-2 small">عند حضور المستفيد وقص الباركود يُسجَّل الحضور. بعد انتهاء الجلسات المقررة انتقل للاختبار.</p>
    @if (!empty($groupSchedule))
        <div class="small">
            <strong>جدول اللقاءات:</strong>
            {{ $groupSchedule['start_date'] ?? '' }} — {{ $groupSchedule['end_date'] ?? '' }}
            ({{ implode('، ', $groupSchedule['days'] ?? []) }})
        </div>
    @endif
</div>
