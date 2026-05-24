<div class="alert alert-warning mb-3">
  <strong>اعتماد المالية</strong>
  <p class="mb-0 small mt-2">يراجع قسم المالية مساهمة المستفيد. في حال عدم السداد خلال 30 يوماً يُرسل تذكير، وبعد 20 يوماً يتواصل الاستقبال مع المستفيد.</p>
  @if (!empty($financial))
    <div class="mt-2 small">
      @if (isset($financial['approved']))
        الحالة: {{ $financial['approved'] ? 'معتمد' : 'غير معتمد' }}
      @endif
    </div>
  @endif
</div>
