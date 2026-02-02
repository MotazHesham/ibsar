{{-- Assistance Workflow Form --}}
<div class="mb-3">
    <label class="form-label">الانتقال إلى حالة:</label>
    <select name="to_status" id="to_status" class="form-select" required onchange="toggleAssistanceStatusFields()">
        <option value="">-- اختر الحالة --</option>
        @php
            $validTransitions = [];
            $assistanceType = $workflow->assistance->assistance_type ?? null;
            // Assistance workflow transitions
            switch ($workflow->current_status) {
                case \App\Models\DynamicServiceWorkflow::STATUS_PENDING_REVIEW:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflow::STATUS_REFUSED => 'رفض الطلب',
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_ASSISTANCE_TYPE_SELECTED =>
                            'اختيار نوع المساعدة',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_ASSISTANCE_TYPE_SELECTED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER =>
                            'دراسة الحالة من الباحث الاجتماعي',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_APPROVED =>
                            'موافقة على دراسة الحالة',
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_REJECTED => 'رفض دراسة الحالة',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_APPROVED:
                    if ($assistanceType === 'financial') {
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT =>
                                'مراجعة وتدقيق ادارة المشاريع',
                        ];
                    } else {
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT =>
                                'مراجعة وتدقيق ادارة المشاريع',
                        ];
                    }
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_REJECTED:
                    $validTransitions = [];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_REVIEW_AND_AUDIT:
                    if ($assistanceType === 'financial') {
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_APPROVED =>
                                'موافقة إدارة المشاريع',
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED =>
                                'عدم موافقة إدارة المشاريع',
                        ];
                    } else {
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_APPROVED =>
                                'موافقة إدارة المشاريع',
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED =>
                                'عدم موافقة إدارة المشاريع',
                        ];
                    }
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_APPROVED:
                    if ($assistanceType === 'financial') {
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_FINANCIAL_DEPARTMENT_FOR_PAYMENT =>
                                'إرسال للقسم المالي للدفع',
                        ];
                    } else {
                        $validTransitions = [
                            \App\Models\DynamicServiceWorkflowAssistance::STATUS_STOCK_AVAILABILITY_CHECK =>
                                'التحقق من توفر المخزون',
                        ];
                    }
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_PROJECT_MANAGEMENT_NOT_APPROVED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_FROM_SOCIAL_RESEARCHER =>
                            'دراسة الحالة من الباحث الاجتماعي',
                    ];
                    break;
                // Financial assistance flow
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_FINANCIAL_DEPARTMENT_FOR_PAYMENT:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_RECEIPT_UPLOADED =>
                            'رفع إيصال الدفع المالي',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_RECEIPT_UPLOADED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_FEEDBACK_RECEIVED =>
                            'استلام الملاحظات المالية',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_FEEDBACK_RECEIVED:
                    $validTransitions = [];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_STOCK_AVAILABILITY_CHECK:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_STOCK_AVAILABLE => 'المخزون متوفر',
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_STOCK_NOT_AVAILABLE => 'المخزون غير متوفر',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_STOCK_AVAILABLE:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_NEED_TRAINING_CHECK =>
                            'التحقق من الحاجة للتدريب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_NEED_TRAINING_CHECK:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_NEED_TRAINING => 'يحتاج تدريب',
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_NO_TRAINING_NEEDED => 'لا يحتاج تدريب',
                    ];
                    break;
                // No training needed flow
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_NO_TRAINING_NEEDED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_BENEFICIARY_NOTIFIED_AVAILABLE =>
                            'إشعار المستفيد بالتوفر',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_BENEFICIARY_NOTIFIED_AVAILABLE:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_RECEIVING_FORM_FILLED =>
                            'ملء نموذج الاستلام',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_RECEIVING_FORM_FILLED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_ORDER_RECEIVED => 'استلام الطلب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_ORDER_RECEIVED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL =>
                            'إرسال للمالية لإزالة من المخزون',
                    ];
                    break;
                // Training needed flow
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_NEED_TRAINING:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET =>
                            'تحديد جدول التدريب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_PROGRAM_STARTED =>
                            'بدء برنامج التدريب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_PROGRAM_STARTED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_ATTENDANCE_REVIEWED =>
                            'مراجعة تتبع الحضور',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_ATTENDANCE_REVIEWED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_FINANCIAL_STATEMENTS =>
                            'البيانات المالية للتدريب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_FINANCIAL_STATEMENTS:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST => 'اختبار التدريب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST_PASSED =>
                            'اجتاز اختبار التدريب',
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST_FAILED =>
                            'لم يجتاز اختبار التدريب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_TEST_PASSED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_MACHINE_SENT => 'تسليم الجهاز',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_MACHINE_SENT:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_REVIEW_REQUEST_SENT =>
                            'إرسال طلب المراجعة',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_REVIEW_REQUEST_SENT:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL =>
                            'إرسال للمالية لإزالة من المخزون',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_FINANCIAL_FOR_STOCK_REMOVAL:
                    $validTransitions = [];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_STOCK_NOT_AVAILABLE:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_ON_WAITING_LIST => 'وضع في قائمة الانتظار',
                    ];
                    break;
                // Stock not available flow
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_ON_WAITING_LIST:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_MARKETING_FOR_DONATION =>
                            'إرسال للتسويق للتبرع',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_MARKETING_FOR_DONATION:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_MARKETING_SUPPORT_COMPLETE =>
                            'اكتمال الدعم التسويقي',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_MARKETING_SUPPORT_COMPLETE:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_PURCHASING_DEPARTMENT =>
                            'إرسال لقسم المشتريات',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_PURCHASING_DEPARTMENT:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_SEARCHING_VENDORS_WITH_OFFERS =>
                            'البحث عن موردين بعروض أسعار',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_SEARCHING_VENDORS_WITH_OFFERS:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_OFFERS_SENT_TO_MANAGEMENT =>
                            'إرسال العروض لإدارة البرنامج',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_OFFERS_SENT_TO_MANAGEMENT:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_ACCEPTED_OFFER =>
                            'قبول العرض من الإدارة',
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_REFUSED_OFFER =>
                            'رفض العرض من الإدارة',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_ACCEPTED_OFFER:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_FINANCIAL_DEPARTMENT =>
                            'إرساله لقسم المالية لإجراءات الصرف',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_SENT_TO_FINANCIAL_DEPARTMENT:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_PAYMENT_RECEIPT_UPLOADED =>
                            'رفع إيصال الدفع',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_PAYMENT_RECEIPT_UPLOADED:
                    $validTransitions = [];
                    break;
                case \App\Models\DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_REFUSED_OFFER:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowAssistance::STATUS_SEARCHING_VENDORS_WITH_OFFERS =>
                            'البحث عن موردين بعروض أسعار أخرى',
                    ];
                    break;
            }
        @endphp
        @foreach ($validTransitions as $status => $label)
            <option value="{{ $status }}">{{ $label }}</option>
        @endforeach
    </select>
</div>

{{-- Refused Reason --}}
<div class="mb-3" id="refused_reason_field" style="display: none;">
    <label class="form-label">سبب الرفض:</label>
    <textarea name="refused_reason" class="form-control" rows="3"></textarea>
</div>

{{-- Assistance Type Selection --}}
<div class="mb-3" id="assistance_type_field" style="display: none;">
    <label class="form-label">نوع المساعدة:</label>
    <select name="assistance_type" id="assistance_type" class="form-select">
        <option value="">-- اختر --</option>
        <option value="real_receipt"
            {{ ($workflow->assistance->assistance_type ?? null) === 'real_receipt' ? 'selected' : '' }}>استلام عيني
        </option>
        <option value="financial"
            {{ ($workflow->assistance->assistance_type ?? null) === 'financial' ? 'selected' : '' }}>مالي</option>
    </select>
</div>

{{-- Stock Availability --}}
<div class="mb-3" id="stock_availability_field" style="display: none;">
    <label class="form-label mt-2">رقم العنصر من المخزون:</label>
    <input type="number" name="stock_item_id" class="form-control">
</div>


{{-- Receiving Form Data --}}
<div class="mb-3" id="receiving_form_field" style="display: none;">
    <label class="form-label">بيانات نموذج الاستلام:</label>
    <textarea name="receiving_form_data" class="form-control" rows="5" placeholder="أدخل بيانات نموذج الاستلام"></textarea>
</div>

{{-- Training Schedule --}}
<div class="mb-3" id="training_schedule_field" style="display: none;">
    <label class="form-label">جدول التدريب:</label>
    <div id="training_schedule_container">
        <div class="training-item mb-2">
            <input type="datetime-local" name="training_schedule[0][date]" class="form-control mb-2"
                placeholder="تاريخ ووقت الجلسة">
            <input type="text" name="training_schedule[0][title]" class="form-control" placeholder="عنوان الجلسة">
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-secondary" onclick="addTrainingSchedule()">إضافة جلسة</button>
</div>

{{-- Program Start Date --}}
<div class="mb-3" id="program_start_date_field" style="display: none;">
    <label class="form-label">تاريخ بدء برنامج التدريب:</label>
    <input type="date" name="program_start_date" class="form-control"
        value="{{ $workflow->assistance->training_program_start_date ? $workflow->assistance->training_program_start_date->format('Y-m-d') : '' }}">
</div>

{{-- Waiting List Position --}}
<div class="mb-3" id="waiting_list_field" style="display: none;">
    <label class="form-label">الموقع في قائمة الانتظار:</label>
    <input type="number" name="waiting_list_position" class="form-control" min="1"
        value="{{ $workflow->assistance->waiting_list_position ?? '' }}" id="waiting_list_position_input">
    <small class="text-muted" id="waiting_list_auto_note" style="display: none;">سيتم تعيين الموقع تلقائياً</small>
</div>

{{-- Vendor Offers --}}
<div class="mb-3" id="vendor_offers_field" style="display: none;">
    <label class="form-label">عروض الموردين:</label>
    <div id="vendor_offers_container">
        @if ($workflow->assistance && $workflow->assistance->vendor_offers && count($workflow->assistance->vendor_offers) > 0)
            @foreach ($workflow->assistance->vendor_offers as $index => $offer)
                <div class="vendor-offer-item mb-2 border p-2">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">اسم المورد:</label>
                            <input type="text" name="vendor_offers[{{ $index }}][vendor_name]" class="form-control mb-2" value="{{ $offer['vendor_name'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">السعر:</label>
                            <input type="number" name="vendor_offers[{{ $index }}][price]" class="form-control mb-2" step="0.01" value="{{ $offer['price'] ?? '' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">ملاحظات:</label>
                            <textarea name="vendor_offers[{{ $index }}][notes]" class="form-control mb-2" rows="2">{{ $offer['notes'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">مرفق عرض السعر:</label>
                            <div class="needsclick dropzone" id="vendor_offer_{{ $index }}-dropzone"></div>
                            <input type="hidden" name="vendor_offers[{{ $index }}][quotation_file]" id="vendor_offer_{{ $index }}_file">
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="this.parentElement.remove()">حذف</button>
                </div>
            @endforeach
        @else
            <div class="vendor-offer-item mb-2 border p-2">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">اسم المورد:</label>
                        <input type="text" name="vendor_offers[0][vendor_name]" class="form-control mb-2">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">السعر:</label>
                        <input type="number" name="vendor_offers[0][price]" class="form-control mb-2" step="0.01">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">ملاحظات:</label>
                        <textarea name="vendor_offers[0][notes]" class="form-control mb-2" rows="2"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">مرفق عرض السعر:</label>
                        <div class="needsclick dropzone" id="vendor_offer_0-dropzone"></div>
                        <input type="hidden" name="vendor_offers[0][quotation_file]" id="vendor_offer_0_file">
                    </div>
                </div>
            </div>
        @endif
    </div>
    <button type="button" class="btn btn-sm btn-secondary" onclick="addVendorOffer()">إضافة عرض مورد</button>
</div>

{{-- Selected Vendor --}}
<div class="mb-3" id="selected_vendor_field" style="display: none;">
    <label class="form-label">المورد المختار:</label>
    <select name="selected_vendor_id" id="selected_vendor_id" class="form-select">
        <option value="">-- اختر المورد --</option>
        @if ($workflow->assistance && $workflow->assistance->vendor_offers)
            @foreach ($workflow->assistance->vendor_offers as $index => $offer)
                <option value="{{ $index }}"
                    {{ ($workflow->assistance->selected_vendor_id ?? null) == $index ? 'selected' : '' }}>
                    {{ $offer['vendor_name'] ?? 'مورد ' . ($index + 1) }} - {{ $offer['price'] ?? '' }}
                </option>
            @endforeach
        @endif
    </select>
</div>

{{-- Management Decision Notes --}}
<div class="mb-3" id="management_decision_field" style="display: none;">
    <label class="form-label">ملاحظات قرار الإدارة:</label>
    <textarea name="management_decision_notes" class="form-control" rows="3">{{ $workflow->assistance->management_decision_notes ?? '' }}</textarea>
</div>

{{-- Payment Receipt Upload --}}
<div class="mb-3" id="payment_receipt_field" style="display: none;">
    <label class="form-label">رفع إيصال الدفع:</label>
    <div class="needsclick dropzone" id="payment_receipt-dropzone">
    </div>
    <input type="hidden" name="payment_receipt_file" id="payment_receipt_file">
</div>


{{-- Study Case Rejection Reason and Missing Data --}}
<div class="mb-3" id="study_case_rejection_field" style="display: none;">
    <label class="form-label">سبب الرفض:</label>
    <textarea name="study_case_rejection_reason" class="form-control" rows="3"
        placeholder="أدخل سبب رفض دراسة الحالة">{{ $workflow->assistance->study_case_rejection_reason ?? '' }}</textarea>
    <label class="form-label mt-2">معلومات البيانات الناقصة:</label>
    <textarea name="missing_data_info" class="form-control" rows="5"
        placeholder="أدخل معلومات البيانات الناقصة المطلوبة لإكمال الطلب">{{ is_array($workflow->assistance->missing_data_info ?? null) ? json_encode($workflow->assistance->missing_data_info, JSON_UNESCAPED_UNICODE) : $workflow->assistance->missing_data_info ?? '' }}</textarea>
</div>

{{-- Financial Receipt File Upload --}}
<div class="mb-3" id="financial_receipt_field" style="display: none;">
    <label class="form-label">رفع إيصال الدفع المالي:</label>
    <div class="needsclick dropzone" id="financial_receipt-dropzone">
    </div>
    <input type="hidden" name="financial_receipt_file" id="financial_receipt_file">
</div>

{{-- Financial Feedback Data --}}
<div class="mb-3" id="financial_feedback_field" style="display: none;">
    <label class="form-label">الملاحظات المالية:</label>
    <textarea name="financial_feedback_data" class="form-control" rows="5" placeholder="أدخل الملاحظات المالية">{{ is_array($workflow->assistance->financial_feedback_data ?? null) ? json_encode($workflow->assistance->financial_feedback_data, JSON_UNESCAPED_UNICODE) : $workflow->assistance->financial_feedback_data ?? '' }}</textarea>
</div> 

<script>
    function toggleAssistanceStatusFields() {
        const status = document.getElementById('to_status').value;

        // Hide all fields
        document.getElementById('refused_reason_field').style.display = 'none';
        document.getElementById('assistance_type_field').style.display = 'none';
        document.getElementById('stock_availability_field').style.display = 'none';
        document.getElementById('receiving_form_field').style.display = 'none';
        document.getElementById('training_schedule_field').style.display = 'none';
        document.getElementById('program_start_date_field').style.display = 'none';
        document.getElementById('waiting_list_field').style.display = 'none';
        document.getElementById('vendor_offers_field').style.display = 'none';
        document.getElementById('selected_vendor_field').style.display = 'none';
        document.getElementById('management_decision_field').style.display = 'none';
        document.getElementById('payment_receipt_field').style.display = 'none';
        document.getElementById('study_case_rejection_field').style.display = 'none';
        document.getElementById('financial_receipt_field').style.display = 'none';
        document.getElementById('financial_feedback_field').style.display = 'none'; 

        // Show relevant fields based on status
        if (status === '{{ \App\Models\DynamicServiceWorkflow::STATUS_REFUSED }}') {
            document.getElementById('refused_reason_field').style.display = 'block';
            document.querySelector('#refused_reason_field textarea').required = true;
        } else {
            document.querySelector('#refused_reason_field textarea').required = false;
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_ASSISTANCE_TYPE_SELECTED }}') {
            document.getElementById('assistance_type_field').style.display = 'block';
            document.getElementById('assistance_type').required = true;
        } else {
            document.getElementById('assistance_type').required = false;
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_STOCK_AVAILABLE }}') {
            document.getElementById('stock_availability_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_RECEIVING_FORM_FILLED }}') {
            document.getElementById('receiving_form_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_DEPARTMENT_SCHEDULE_SET }}') {
            document.getElementById('training_schedule_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_TRAINING_PROGRAM_STARTED }}') {
            document.getElementById('program_start_date_field').style.display = 'block';
        }

        // Stock not available flow fields
        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_ON_WAITING_LIST }}') {
            const assistanceType = '{{ $workflow->assistance->assistance_type ?? "" }}';
            if (assistanceType === 'real_receipt') {
                // Hide input for real_receipt, show auto note
                document.getElementById('waiting_list_position_input').style.display = 'none';
                document.getElementById('waiting_list_auto_note').style.display = 'block';
                document.getElementById('waiting_list_field').style.display = 'block';
            } else {
                // Show input for other types
                document.getElementById('waiting_list_position_input').style.display = 'block';
                document.getElementById('waiting_list_auto_note').style.display = 'none';
                document.getElementById('waiting_list_field').style.display = 'block';
            }
        } else {
            // Reset when not showing waiting list
            document.getElementById('waiting_list_position_input').style.display = 'block';
            document.getElementById('waiting_list_auto_note').style.display = 'none';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_SEARCHING_VENDORS_WITH_OFFERS }}') {
            document.getElementById('vendor_offers_field').style.display = 'block';
            initializeVendorOffersDropzones();
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_ACCEPTED_OFFER }}') {
            document.getElementById('selected_vendor_field').style.display = 'block';
            document.getElementById('management_decision_field').style.display = 'block';
            // Populate vendor options if vendor_offers exist
            const vendorOffers = @json($workflow->assistance->vendor_offers ?? []);
            const selectVendor = document.getElementById('selected_vendor_id');
            if (vendorOffers.length > 0 && selectVendor.options.length <= 1) {
                vendorOffers.forEach((offer, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = (offer.vendor_name || 'مورد ' + (index + 1)) + ' - ' + (offer.price ||
                        '');
                    selectVendor.appendChild(option);
                });
            }
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_MANAGEMENT_REFUSED_OFFER }}') {
            document.getElementById('management_decision_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_PAYMENT_RECEIPT_UPLOADED }}') {
            document.getElementById('payment_receipt_field').style.display = 'block';
            initializePaymentReceiptDropzone();
        }

        // Study case rejection
        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_STUDY_CASE_REJECTED }}') {
            document.getElementById('study_case_rejection_field').style.display = 'block';
        }

        // Financial assistance flow fields
        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_RECEIPT_UPLOADED }}') {
            document.getElementById('financial_receipt_field').style.display = 'block';
            initializeFinancialReceiptDropzone();
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowAssistance::STATUS_FINANCIAL_FEEDBACK_RECEIVED }}') {
            document.getElementById('financial_feedback_field').style.display = 'block';
        } 
    }

    // Initialize Dropzone for financial receipt (only when field is visible)
    function initializeFinancialReceiptDropzone() {
        if (typeof financialReceiptDropzone !== 'undefined') {
            return; // Already initialized
        }

        financialReceiptDropzone = new Dropzone("#financial_receipt-dropzone", {
            url: '{{ route('admin.settings.storeMedia') }}',
            maxFilesize: 5,
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5
            },
            success: function(file, response) {
                $('#financial_receipt_file').val(response.name);
            },
            removedfile: function(file) {
                file.previewElement.remove();
                if (file.status !== 'error') {
                    $('#financial_receipt_file').val('');
                    this.options.maxFiles = this.options.maxFiles + 1;
                }
            },
            init: function() {
                @if ($workflow->assistance && $workflow->assistance->getMedia('financial_receipt')->first())
                    var file = {!! json_encode($workflow->assistance->getMedia('financial_receipt')->first()) !!};
                    if (file) {
                        this.options.addedfile.call(this, file);
                        this.options.thumbnail.call(this, file, file.preview ?? file.preview_url);
                        file.previewElement.classList.add('dz-complete');
                        $('#financial_receipt_file').val(file.file_name);
                        this.options.maxFiles = this.options.maxFiles - 1;
                    }
                @endif
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response;
                } else {
                    var message = response.errors.file;
                }
                file.previewElement.classList.add('dz-error');
                var _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]');
                _ref.forEach(function(node) {
                    node.textContent = message;
                });
            }
        });
    }

    // Initialize Dropzone for payment receipt (only when field is visible)
    function initializePaymentReceiptDropzone() {
        if (typeof paymentReceiptDropzone !== 'undefined') {
            return; // Already initialized
        }

        paymentReceiptDropzone = new Dropzone("#payment_receipt-dropzone", {
            url: '{{ route('admin.settings.storeMedia') }}',
            maxFilesize: 5,
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5
            },
            success: function(file, response) {
                $('#payment_receipt_file').val(response.name);
            },
            removedfile: function(file) {
                file.previewElement.remove();
                if (file.status !== 'error') {
                    $('#payment_receipt_file').val('');
                    this.options.maxFiles = this.options.maxFiles + 1;
                }
            },
            init: function() {
                @if ($workflow->assistance && $workflow->assistance->getMedia('payment_receipt')->first())
                    var file = {!! json_encode($workflow->assistance->getMedia('payment_receipt')->first()) !!};
                    if (file) {
                        this.options.addedfile.call(this, file);
                        this.options.thumbnail.call(this, file, file.preview ?? file.preview_url);
                        file.previewElement.classList.add('dz-complete');
                        $('#payment_receipt_file').val(file.file_name);
                        this.options.maxFiles = this.options.maxFiles - 1;
                    }
                @endif
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response;
                } else {
                    var message = response.errors.file;
                }
                file.previewElement.classList.add('dz-error');
                var _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]');
                _ref.forEach(function(node) {
                    node.textContent = message;
                });
            }
        });
    }

    // Initialize Dropzones for vendor offers
    function initializeVendorOffersDropzones() {
        const container = document.getElementById('vendor_offers_container');
        if (!container) return;

        // Initialize dropzones for existing vendor offer items
        container.querySelectorAll('.vendor-offer-item').forEach(function(item) {
            const dropzoneElement = item.querySelector('[id^="vendor_offer_"][id$="-dropzone"]');
            if (!dropzoneElement || dropzoneElement.dropzone) return;
            
            const dropzoneId = dropzoneElement.id;
            const match = dropzoneId.match(/vendor_offer_(\d+)-dropzone/);
            if (!match) return;
            
            const offerIndex = match[1];
            const inputElement = item.querySelector(`#vendor_offer_${offerIndex}_file`);

            const dropzone = new Dropzone(`#${dropzoneId}`, {
                url: '{{ route('admin.settings.storeMedia') }}',
                maxFilesize: 5,
                maxFiles: 1,
                addRemoveLinks: true,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                params: {
                    size: 5
                },
                success: function(file, response) {
                    if (inputElement) {
                        inputElement.value = response.name;
                    }
                },
                removedfile: function(file) {
                    file.previewElement.remove();
                    if (file.status !== 'error') {
                        if (inputElement) {
                            inputElement.value = '';
                        }
                        this.options.maxFiles = this.options.maxFiles + 1;
                    }
                },
                error: function(file, response) {
                    if ($.type(response) === 'string') {
                        var message = response;
                    } else {
                        var message = response.errors.file;
                    }
                    file.previewElement.classList.add('dz-error');
                    var _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]');
                    _ref.forEach(function(node) {
                        node.textContent = message;
                    });
                }
            });
            dropzoneElement.dropzone = dropzone;
        });
    }

    let vendorOfferCounter = @if ($workflow->assistance && $workflow->assistance->vendor_offers) {{ count($workflow->assistance->vendor_offers) }} @else 1 @endif;

    function addVendorOffer() {
        const container = document.getElementById('vendor_offers_container');
        const newOffer = document.createElement('div');
        newOffer.className = 'vendor-offer-item mb-2 border p-2';
        newOffer.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">اسم المورد:</label>
                    <input type="text" name="vendor_offers[${vendorOfferCounter}][vendor_name]" class="form-control mb-2">
                </div>
                <div class="col-md-6">
                    <label class="form-label">السعر:</label>
                    <input type="number" name="vendor_offers[${vendorOfferCounter}][price]" class="form-control mb-2" step="0.01">
                </div>
                <div class="col-md-12">
                    <label class="form-label">ملاحظات:</label>
                    <textarea name="vendor_offers[${vendorOfferCounter}][notes]" class="form-control mb-2" rows="2"></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">مرفق عرض السعر:</label>
                    <div class="needsclick dropzone" id="vendor_offer_${vendorOfferCounter}-dropzone"></div>
                    <input type="hidden" name="vendor_offers[${vendorOfferCounter}][quotation_file]" id="vendor_offer_${vendorOfferCounter}_file">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="this.parentElement.remove()">حذف</button>
        `;
        container.appendChild(newOffer);
        
        // Initialize dropzone for the new offer
        const dropzoneId = `vendor_offer_${vendorOfferCounter}-dropzone`;
        const inputId = `vendor_offer_${vendorOfferCounter}_file`;
        const dropzone = new Dropzone(`#${dropzoneId}`, {
            url: '{{ route('admin.settings.storeMedia') }}',
            maxFilesize: 5,
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5
            },
            success: function(file, response) {
                document.getElementById(inputId).value = response.name;
            },
            removedfile: function(file) {
                file.previewElement.remove();
                if (file.status !== 'error') {
                    document.getElementById(inputId).value = '';
                    this.options.maxFiles = this.options.maxFiles + 1;
                }
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response;
                } else {
                    var message = response.errors.file;
                }
                file.previewElement.classList.add('dz-error');
                var _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]');
                _ref.forEach(function(node) {
                    node.textContent = message;
                });
            }
        });
        
        vendorOfferCounter++;
    }

    let trainingCounter = 1;

    function addTrainingSchedule() {
        const container = document.getElementById('training_schedule_container');
        const newTraining = document.createElement('div');
        newTraining.className = 'training-item mb-2';
        newTraining.innerHTML = `
        <input type="datetime-local" name="training_schedule[${trainingCounter}][date]" class="form-control mb-2" placeholder="تاريخ ووقت الجلسة">
        <input type="text" name="training_schedule[${trainingCounter}][title]" class="form-control" placeholder="عنوان الجلسة">
        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="this.parentElement.remove()">حذف</button>
    `;
        container.appendChild(newTraining);
        trainingCounter++;
    }
</script>
