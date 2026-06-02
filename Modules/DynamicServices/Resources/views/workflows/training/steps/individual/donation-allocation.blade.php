<div class="alert alert-info mb-3">
  <strong>تخصيص التبرعات</strong>
  <p class="mb-2 small">يقوم قسم المشاريع أو إدارة التدريب بتخصيص التبرع بحسب عدد الجلسات ({{ $sessionsCount }} جلسة).</p>
  @if ($hasDonationAllocation)
    <span class="badge bg-success">تم تخصيص تبرع لهذا الطلب</span>
  @else
    <span class="badge bg-warning">لم يتم تخصيص تبرع بعد — استخدم قسم تخصيص التبرعات في الصفحة</span>
  @endif
</div>
