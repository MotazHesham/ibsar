{{-- Social Programs Workflow Form --}}
<div class="mb-3">
    <label class="form-label">الانتقال إلى حالة:</label>
    <select name="to_status" id="to_status" class="form-select" required onchange="toggleSocialProgramsStatusFields()">
        <option value="">-- اختر الحالة --</option>
        @php
            $validTransitions = [];
            // Social programs workflow transitions
            switch ($workflow->current_status) {
                case \App\Models\DynamicServiceWorkflow::STATUS_PENDING_REVIEW:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflow::STATUS_REFUSED => 'رفض الطلب',
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_ACCEPTED => 'قبول الطلب',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_ACCEPTED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_COMPLETED_CHECK =>
                            'التحقق من اكتمال البرنامج',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_COMPLETED_CHECK:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_COMPLETED => 'البرنامج مكتمل',
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_NOT_COMPLETED =>
                            'البرنامج غير مكتمل',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_NOT_COMPLETED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_ON_WAITING_LIST => 'إضافة إلى قائمة الانتظار',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_ON_WAITING_LIST:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_COMPLETED => 'تحويل إلى مكتمل',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_COMPLETED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_DOCUMENT_PREPARED => 'إعداد الوثائق',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_DOCUMENT_PREPARED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_SENT_TO_EXECUTIVE_MANAGEMENT =>
                            'إرسال للإدارة التنفيذية',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_SENT_TO_EXECUTIVE_MANAGEMENT:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_ACCEPTED =>
                            'قرار الإدارة: مقبول',
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_REFUSED =>
                            'قرار الإدارة: مرفوض',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_ACCEPTED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_PROCEEDED =>
                            'المضي قدماً في البرنامج',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_REFUSED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW =>
                            'العودة لإدارة البرنامج للمراجعة',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_DOCUMENT_PREPARED =>
                            'إعادة إعداد الوثائق',
                        \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية',
                    ];
                    break;
                case \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_PROCEEDED:
                    $validTransitions = [
                        \App\Models\DynamicServiceWorkflow::STATUS_COMPLETED => 'إكمال العملية',
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
    <textarea name="refused_reason" class="form-control" rows="3" required>{{ $workflow->refused_reason ?? '' }}</textarea>
</div>

{{-- Waiting List Position --}}
<div class="mb-3" id="waiting_list_field" style="display: none;">
    <label class="form-label">الموقع في قائمة الانتظار:</label>
    <input type="number" name="waiting_list_position" class="form-control" min="1"
        value="{{ $workflow->socialPrograms->waiting_list_position ?? '' }}">
</div>

{{-- Document Data --}}
<div class="mb-3" id="document_data_field" style="display: none;">
    <label class="form-label">بيانات الوثائق:</label>
    <textarea name="document_data" class="form-control" rows="5"
        placeholder="أدخل بيانات الوثائق">{{ is_array($workflow->socialPrograms->document_data ?? null) ? json_encode($workflow->socialPrograms->document_data, JSON_UNESCAPED_UNICODE) : ($workflow->socialPrograms->document_data ?? '') }}</textarea>
</div>

{{-- Executive Decision Notes --}}
<div class="mb-3" id="executive_decision_notes_field" style="display: none;">
    <label class="form-label">ملاحظات قرار الإدارة التنفيذية:</label>
    <textarea name="executive_decision_notes" class="form-control" rows="5"
        placeholder="أدخل ملاحظات قرار الإدارة التنفيذية">{{ $workflow->socialPrograms->executive_decision_notes ?? '' }}</textarea>
</div>

{{-- Program Proceeded Date --}}
<div class="mb-3" id="program_proceeded_date_field" style="display: none;">
    <label class="form-label">تاريخ المضي قدماً في البرنامج:</label>
    <input type="date" name="program_proceeded_date" class="form-control"
        value="{{ $workflow->socialPrograms->program_proceeded_date ? $workflow->socialPrograms->program_proceeded_date->format('Y-m-d') : '' }}">
</div>

{{-- Review Notes --}}
<div class="mb-3" id="review_notes_field" style="display: none;">
    <label class="form-label">ملاحظات المراجعة:</label>
    <textarea name="review_notes" class="form-control" rows="5"
        placeholder="أدخل ملاحظات المراجعة">{{ $workflow->socialPrograms->review_notes ?? '' }}</textarea>
</div>

<script>
    function toggleSocialProgramsStatusFields() {
        const status = document.getElementById('to_status').value;

        // Hide all fields
        document.getElementById('refused_reason_field').style.display = 'none';
        document.getElementById('waiting_list_field').style.display = 'none';
        document.getElementById('document_data_field').style.display = 'none';
        document.getElementById('executive_decision_notes_field').style.display = 'none';
        document.getElementById('program_proceeded_date_field').style.display = 'none';
        document.getElementById('review_notes_field').style.display = 'none';

        // Show relevant fields based on status
        if (status === '{{ \App\Models\DynamicServiceWorkflow::STATUS_REFUSED }}') {
            document.getElementById('refused_reason_field').style.display = 'block';
            document.querySelector('#refused_reason_field textarea').required = true;
        } else {
            document.querySelector('#refused_reason_field textarea').required = false;
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_ON_WAITING_LIST }}') {
            document.getElementById('waiting_list_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_DOCUMENT_PREPARED }}') {
            document.getElementById('document_data_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_ACCEPTED }}' ||
            status === '{{ \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_EXECUTIVE_REFUSED }}') {
            document.getElementById('executive_decision_notes_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_PROGRAM_PROCEEDED }}') {
            document.getElementById('program_proceeded_date_field').style.display = 'block';
        }

        if (status === '{{ \App\Models\DynamicServiceWorkflowSocialPrograms::STATUS_BACK_TO_PROGRAM_MANAGEMENT_REVIEW }}') {
            document.getElementById('review_notes_field').style.display = 'block';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleSocialProgramsStatusFields();
    });
</script>

