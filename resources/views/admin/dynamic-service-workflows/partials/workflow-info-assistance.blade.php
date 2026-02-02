{{-- Assistance Workflow Info --}}
@php
    $categoryData = $workflow->assistance;
@endphp

@if($workflow->refused_reason)
    <div class="alert alert-danger">
        <strong>سبب الرفض:</strong><br>{{ $workflow->refused_reason }}
    </div>
@endif

@if($workflow->specialist)
    <p><strong>الأخصائي:</strong> {{ $workflow->specialist->name }}</p>
@endif

@if($categoryData)
    @if($categoryData->assistance_type)
        <p><strong>نوع المساعدة:</strong> 
            <span class="badge bg-info">{{ $categoryData->assistance_type === 'real_receipt' ? 'استلام فعلي' : 'مالي' }}</span>
        </p>
    @endif

    @if($categoryData->study_case_approved !== null)
        <p><strong>قرار دراسة الحالة:</strong>
            <span class="badge bg-{{ $categoryData->study_case_approved ? 'success' : 'danger' }}">
                {{ $categoryData->study_case_approved ? 'موافق' : 'مرفوض' }}
            </span>
        </p>
    @endif

    @if($categoryData->stock_available !== null)
        <p><strong>توفر المخزون:</strong>
            <span class="badge bg-{{ $categoryData->stock_available ? 'success' : 'danger' }}">
                {{ $categoryData->stock_available ? 'متوفر' : 'غير متوفر' }}
            </span>
        </p>
        @if($categoryData->stock_item_id)
            <p><strong>رقم العنصر من المخزون:</strong> {{ $categoryData->stock_item_id }}</p>
        @endif
    @endif

    @if($categoryData->need_training !== null)
        <p><strong>الحاجة للتدريب:</strong>
            <span class="badge bg-{{ $categoryData->need_training ? 'warning' : 'info' }}">
                {{ $categoryData->need_training ? 'يحتاج تدريب' : 'لا يحتاج تدريب' }}
            </span>
        </p>
    @endif

    @if($categoryData->training_program_start_date)
        <p><strong>تاريخ بدء برنامج التدريب:</strong><br>{{ $categoryData->training_program_start_date->format('Y-m-d') }}</p>
    @endif

    @if($categoryData->training_test_passed !== null)
        <p><strong>نتيجة اختبار التدريب:</strong>
            <span class="badge bg-{{ $categoryData->training_test_passed ? 'success' : 'danger' }}">
                {{ $categoryData->training_test_passed ? 'نجح' : 'فشل' }}
            </span>
        </p>
        @if($categoryData->training_test_notes)
            <p><strong>ملاحظات الاختبار:</strong><br>{{ $categoryData->training_test_notes }}</p>
        @endif
    @endif

    @if($categoryData->machine_item_id)
        <p><strong>رقم عنصر الجهاز:</strong> {{ $categoryData->machine_item_id }}</p>
    @endif

    @if($categoryData->review_request_sent)
        <p><span class="badge bg-info">تم إرسال طلب المراجعة</span></p>
    @endif

    @if($categoryData->training_schedule)
        <div class="mt-3">
            <strong>جدول التدريب:</strong>
            <ul class="list-unstyled mt-2">
                @foreach($categoryData->training_schedule as $session)
                    <li class="mb-2">
                        <strong>{{ $session['title'] ?? 'جلسة' }}</strong><br>
                        <small class="text-muted">{{ isset($session['date']) ? \Carbon\Carbon::parse($session['date'])->format('Y-m-d H:i') : '' }}</small>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Stock not available flow fields --}}
    @if($categoryData->waiting_list_position)
        <p><strong>الموقع في قائمة الانتظار:</strong> {{ $categoryData->waiting_list_position }}</p>
    @endif

    @if($categoryData->vendor_offers && is_array($categoryData->vendor_offers) && count($categoryData->vendor_offers) > 0)
        <div class="mt-3">
            <strong>عروض الموردين:</strong>
            <ul class="list-unstyled mt-2">
                @foreach($categoryData->vendor_offers as $index => $offer)
                    <li class="mb-2 border p-2">
                        <strong>{{ $offer['vendor_name'] ?? 'مورد ' . ($index + 1) }}</strong>
                        @if(isset($offer['price']))
                            <span class="badge bg-info">السعر: {{ $offer['price'] }}</span>
                        @endif
                        @if($categoryData->selected_vendor_id == $index)
                            <span class="badge bg-success">مختار</span>
                        @endif
                        @if(isset($offer['notes']))
                            <br><small class="text-muted">{{ $offer['notes'] }}</small>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($categoryData->management_decision_notes)
        <p><strong>ملاحظات قرار الإدارة:</strong><br>{{ $categoryData->management_decision_notes }}</p>
    @endif

    @if($categoryData->payment_receipt_url)
        <p><strong>رابط إيصال الدفع:</strong><br><a href="{{ $categoryData->payment_receipt_url }}" target="_blank">{{ $categoryData->payment_receipt_url }}</a></p>
    @endif

    @if($categoryData->feedback_data)
        <div class="mt-3">
            <strong>الملاحظات:</strong>
            @if(is_array($categoryData->feedback_data))
                <p>{{ $categoryData->feedback_data['notes'] ?? json_encode($categoryData->feedback_data, JSON_UNESCAPED_UNICODE) }}</p>
            @else
                <p>{{ $categoryData->feedback_data }}</p>
            @endif
        </div>
    @endif

    {{-- Financial assistance flow fields --}}
    @if($categoryData->study_case_rejection_reason)
        <div class="alert alert-warning mt-3">
            <strong>سبب رفض دراسة الحالة:</strong><br>{{ $categoryData->study_case_rejection_reason }}
        </div>
    @endif

    @if($categoryData->missing_data_info)
        <div class="mt-3">
            <strong>معلومات البيانات الناقصة:</strong>
            @if(is_array($categoryData->missing_data_info))
                <p>{{ $categoryData->missing_data_info['info'] ?? json_encode($categoryData->missing_data_info, JSON_UNESCAPED_UNICODE) }}</p>
            @else
                <p>{{ $categoryData->missing_data_info }}</p>
            @endif
        </div>
    @endif

    @if($categoryData->getMedia('financial_receipt')->first())
        <div class="mt-3">
            <strong>إيصال الدفع المالي:</strong><br>
            <a href="{{ $categoryData->getMedia('financial_receipt')->first()->getUrl() }}" target="_blank" class="btn btn-sm btn-primary">
                عرض الإيصال
            </a>
        </div>
    @endif

    @if($categoryData->financial_feedback_data)
        <div class="mt-3">
            <strong>الملاحظات المالية:</strong>
            @if(is_array($categoryData->financial_feedback_data))
                <p>{{ $categoryData->financial_feedback_data['notes'] ?? json_encode($categoryData->financial_feedback_data, JSON_UNESCAPED_UNICODE) }}</p>
            @else
                <p>{{ $categoryData->financial_feedback_data }}</p>
            @endif
        </div>
    @endif
@endif

@if($workflow->notes)
    <p><strong>ملاحظات:</strong><br>{{ $workflow->notes }}</p>
@endif

