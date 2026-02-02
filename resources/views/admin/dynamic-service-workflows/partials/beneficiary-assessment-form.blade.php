@php
    $beneficiary = $dynamicServiceOrder->beneficiaryOrder->beneficiary ?? null;
    $beneficiaryUser = $beneficiary->user ?? null;
    // Decode JSON from specialist_report if it exists, otherwise use empty array
    $report = [];
    if ($workflow->specialist_report) {
        $decoded = json_decode($workflow->specialist_report, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $report = $decoded;
        }
    }
@endphp

<div class="beneficiary-assessment-form">
    <h5 class="mb-4">نموذج تقييم مستفيد</h5>
    
    {{-- Header Section --}}
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="card-title mb-0">معلومات المستفيد</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">الجهة الطالبة للتقرير:</label>
                        <input type="text" name="assessment[requesting_authority]" class="form-control" 
                            value="{{ old('assessment.requesting_authority', $report['requesting_authority'] ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اسم المستفيد:</label>
                        <input type="text" class="form-control" value="{{ $beneficiaryUser->name ?? '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ الميلاد:</label>
                        <input type="text" class="form-control" value="{{ $beneficiary->dob ?? '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم التسجيل:</label>
                        <input type="text" class="form-control" value="{{ $beneficiary->id ?? '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مدة التقييم:</label>
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">رقم الهوية:</label>
                        <input type="text" class="form-control" value="{{ $beneficiaryUser->identity_num ?? '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحالة البصرية:</label>
                        <input type="text" class="form-control" value="{{ optional($beneficiary->disability_type ?? null)->name ?? '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">هدف التقييم:</label>
                        <input type="text" class="form-control" name="assessment[assessment_goal]" value="{{ old('assessment.assessment_goal', $report['assessment_goal'] ?? '') }}"> 
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المقيم:</label>
                        <select name="specialist_id" class="form-select">
                            <option value="">-- اختر الأخصائي --</option>
                            @foreach ($specialists as $id => $name)
                                <option value="{{ $id }}" {{ $workflow->specialist_id == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section I: Primary Assessment Areas --}}
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="card-title mb-0">أولاً: مجالات التقييم الأساسية</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 25%;">المجالات</th>
                            <th style="width: 35%;">المؤشر</th>
                            <th style="width: 40%;">التقدير</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Comprehension and Understanding Domain --}}
                        <tr>
                            <td rowspan="5" class="align-middle"><strong>الاستيعاب والفهم</strong></td>
                            <td>فهم المحتوى</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][content_understanding]" 
                                        id="content_high" value="مرتفع" 
                                        {{ old('assessment.comprehension.content_understanding', ($report['comprehension'] ?? [])['content_understanding'] ?? '') == 'مرتفع' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="content_high">مرتفع</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][content_understanding]" 
                                        id="content_medium" value="متوسط"
                                        {{ old('assessment.comprehension.content_understanding', ($report['comprehension'] ?? [])['content_understanding'] ?? '') == 'متوسط' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="content_medium">متوسط</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][content_understanding]" 
                                        id="content_low" value="منخفض"
                                        {{ old('assessment.comprehension.content_understanding', ($report['comprehension'] ?? [])['content_understanding'] ?? '') == 'منخفض' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="content_low">منخفض</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>احتياج لشرح متكرر</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][repeated_explanation]" 
                                        id="explanation_high" value="مرتفع"
                                        {{ old('assessment.comprehension.repeated_explanation', ($report['comprehension'] ?? [])['repeated_explanation'] ?? '') == 'مرتفع' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="explanation_high">مرتفع</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][repeated_explanation]" 
                                        id="explanation_medium" value="متوسط"
                                        {{ old('assessment.comprehension.repeated_explanation', ($report['comprehension'] ?? [])['repeated_explanation'] ?? '') == 'متوسط' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="explanation_medium">متوسط</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][repeated_explanation]" 
                                        id="explanation_low" value="منخفض"
                                        {{ old('assessment.comprehension.repeated_explanation', ($report['comprehension'] ?? [])['repeated_explanation'] ?? '') == 'منخفض' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="explanation_low">منخفض</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>سرعة الاستيعاب</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][comprehension_speed]" 
                                        id="speed_high" value="مرتفع"
                                        {{ old('assessment.comprehension.comprehension_speed', ($report['comprehension'] ?? [])['comprehension_speed'] ?? '') == 'مرتفع' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="speed_high">مرتفع</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][comprehension_speed]" 
                                        id="speed_medium" value="متوسط"
                                        {{ old('assessment.comprehension.comprehension_speed', ($report['comprehension'] ?? [])['comprehension_speed'] ?? '') == 'متوسط' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="speed_medium">متوسط</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][comprehension_speed]" 
                                        id="speed_low" value="منخفض"
                                        {{ old('assessment.comprehension.comprehension_speed', ($report['comprehension'] ?? [])['comprehension_speed'] ?? '') == 'منخفض' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="speed_low">منخفض</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>القدرة على الاستيعاب السمعي</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][auditory_comprehension]" 
                                        id="auditory_strong" value="قوية"
                                        {{ old('assessment.comprehension.auditory_comprehension', ($report['comprehension'] ?? [])['auditory_comprehension'] ?? '') == 'قوية' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auditory_strong">قوية</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][auditory_comprehension]" 
                                        id="auditory_medium" value="متوسطة"
                                        {{ old('assessment.comprehension.auditory_comprehension', ($report['comprehension'] ?? [])['auditory_comprehension'] ?? '') == 'متوسطة' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auditory_medium">متوسطة</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][auditory_comprehension]" 
                                        id="auditory_weak" value="ضعيفة"
                                        {{ old('assessment.comprehension.auditory_comprehension', ($report['comprehension'] ?? [])['auditory_comprehension'] ?? '') == 'ضعيفة' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auditory_weak">ضعيفة</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>مشكلات التذكر والتركيز</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][memory_concentration]" 
                                        id="memory_strong" value="قوية"
                                        {{ old('assessment.comprehension.memory_concentration', ($report['comprehension'] ?? [])['memory_concentration'] ?? '') == 'قوية' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="memory_strong">قوية</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][memory_concentration]" 
                                        id="memory_medium" value="متوسطة"
                                        {{ old('assessment.comprehension.memory_concentration', ($report['comprehension'] ?? [])['memory_concentration'] ?? '') == 'متوسطة' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="memory_medium">متوسطة</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[comprehension][memory_concentration]" 
                                        id="memory_weak" value="ضعيفة"
                                        {{ old('assessment.comprehension.memory_concentration', ($report['comprehension'] ?? [])['memory_concentration'] ?? '') == 'ضعيفة' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="memory_weak">ضعيفة</label>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Practical Application Domain --}}
                        <tr>
                            <td rowspan="2" class="align-middle"><strong>التطبيق العملي</strong></td>
                            <td>تنفيذ المهام</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[practical][task_execution]" 
                                        id="task_independent" value="مستقل"
                                        {{ old('assessment.practical.task_execution', ($report['practical'] ?? [])['task_execution'] ?? '') == 'مستقل' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="task_independent">مستقل</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[practical][task_execution]" 
                                        id="task_assisted" value="بمساعدة"
                                        {{ old('assessment.practical.task_execution', ($report['practical'] ?? [])['task_execution'] ?? '') == 'بمساعدة' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="task_assisted">بمساعدة</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[practical][task_execution]" 
                                        id="task_unable" value="غير قادر"
                                        {{ old('assessment.practical.task_execution', ($report['practical'] ?? [])['task_execution'] ?? '') == 'غير قادر' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="task_unable">غير قادر</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>الاعتماد على النفس</td>
                            <td>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[practical][self_reliance]" 
                                        id="reliance_high" value="عالي"
                                        {{ old('assessment.practical.self_reliance', ($report['practical'] ?? [])['self_reliance'] ?? '') == 'عالي' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="reliance_high">عالي</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[practical][self_reliance]" 
                                        id="reliance_medium" value="متوسط"
                                        {{ old('assessment.practical.self_reliance', ($report['practical'] ?? [])['self_reliance'] ?? '') == 'متوسط' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="reliance_medium">متوسط</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="assessment[practical][self_reliance]" 
                                        id="reliance_weak" value="ضعيف"
                                        {{ old('assessment.practical.self_reliance', ($report['practical'] ?? [])['self_reliance'] ?? '') == 'ضعيف' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="reliance_weak">ضعيف</label>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Influencing Factors Domain --}}
                        <tr>
                            <td class="align-middle"><strong>العوامل المؤثرة</strong></td>
                            <td colspan="2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assessment[factors][]" 
                                        id="factor_psychological" value="عوامل نفسية"
                                        {{ in_array('عوامل نفسية', old('assessment.factors', $report['factors'] ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="factor_psychological">عوامل نفسية</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assessment[factors][]" 
                                        id="factor_health" value="عوامل صحية"
                                        {{ in_array('عوامل صحية', old('assessment.factors', $report['factors'] ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="factor_health">عوامل صحية</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assessment[factors][]" 
                                        id="factor_assistive" value="احتياج لوسائل مساعدة إضافية"
                                        {{ in_array('احتياج لوسائل مساعدة إضافية', old('assessment.factors', $report['factors'] ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="factor_assistive">احتياج لوسائل مساعدة إضافية</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="assessment[factors][]" 
                                        id="factor_other" value="أخرى" onchange="toggleOtherFactor(this)">
                                    <label class="form-check-label" for="factor_other">أخرى</label>
                                </div>
                                <div class="mt-2" id="other_factor_input" style="display: none;">
                                    <input type="text" name="assessment[factors_other]" class="form-control" 
                                        placeholder="حدد العوامل الأخرى"
                                        value="{{ old('assessment.factors_other', $report['factors_other'] ?? '') }}">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Section II: Visual Assessment (if requested) --}}
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="card-title mb-0">ثانياً: التقييم البصري (إن طلب)</h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                بناءً على بيانات التقديم ونتائج التقييم، تم دراسة حالة المستفيد من الجوانب التعليمية، المهارية، التقنية، والحياتية، بهدف تحديد المسار التدريبي الأنسب واحتياج الدعم.
            </p>
            
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label"><strong>مستوى الجاهزية العامة:</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[readiness][]" 
                            id="readiness_no_intervention" value="لا يحتاج تدخل حاليًا"
                            {{ in_array('لا يحتاج تدخل حاليًا', old('assessment.readiness', $report['readiness'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="readiness_no_intervention">لا يحتاج تدخل حاليًا</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[readiness][]" 
                            id="readiness_high" value="مرتفع"
                            {{ in_array('مرتفع', old('assessment.readiness', $report['readiness'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="readiness_high">مرتفع</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[readiness][]" 
                            id="readiness_medium" value="متوسط"
                            {{ in_array('متوسط', old('assessment.readiness', $report['readiness'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="readiness_medium">متوسط</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[readiness][]" 
                            id="readiness_needs_support" value="يحتاج دعم"
                            {{ in_array('يحتاج دعم', old('assessment.readiness', $report['readiness'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="readiness_needs_support">يحتاج دعم</label>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label"><strong>طبيعة الاحتياج:</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[nature_of_need][]" 
                            id="need_training" value="تدريبي"
                            {{ in_array('تدريبي', old('assessment.nature_of_need', $report['nature_of_need'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="need_training">تدريبي</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[nature_of_need][]" 
                            id="need_rehabilitation" value="تأهيلي"
                            {{ in_array('تأهيلي', old('assessment.nature_of_need', $report['nature_of_need'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="need_rehabilitation">تأهيلي</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[nature_of_need][]" 
                            id="need_empowerment" value="تمكيني"
                            {{ in_array('تمكيني', old('assessment.nature_of_need', $report['nature_of_need'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="need_empowerment">تمكيني</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[nature_of_need][]" 
                            id="need_mixed" value="مختلط"
                            {{ in_array('مختلط', old('assessment.nature_of_need', $report['nature_of_need'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="need_mixed">مختلط</label>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label"><strong>مستوى الأولوية:</strong></label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[priority][]" 
                            id="priority_high" value="أولوية عالية"
                            {{ in_array('أولوية عالية', old('assessment.priority', $report['priority'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="priority_high">أولوية عالية</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[priority][]" 
                            id="priority_medium" value="أولوية متوسطة"
                            {{ in_array('أولوية متوسطة', old('assessment.priority', $report['priority'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="priority_medium">أولوية متوسطة</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assessment[priority][]" 
                            id="priority_later" value="أولوية لاحقة"
                            {{ in_array('أولوية لاحقة', old('assessment.priority', $report['priority'] ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="priority_later">أولوية لاحقة</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section III: Recommendations --}}
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="card-title mb-0">ثالثاً: التوصيات</h6>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <label class="form-label">1.</label>
                <textarea name="assessment[recommendations][0]" class="form-control" rows="2"
                    placeholder="التوصية الأولى">{{ old('assessment.recommendations.0', $report['recommendations'][0] ?? '') }}</textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">2.</label>
                <textarea name="assessment[recommendations][1]" class="form-control" rows="2"
                    placeholder="التوصية الثانية">{{ old('assessment.recommendations.1', $report['recommendations'][1] ?? '') }}</textarea>
            </div>
            <div class="mb-2">
                <label class="form-label">3.</label>
                <textarea name="assessment[recommendations][2]" class="form-control" rows="2"
                    placeholder="التوصية الثالثة">{{ old('assessment.recommendations.2', $report['recommendations'][2] ?? '') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Section IV: Notes --}}
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="card-title mb-0">رابعاً: الملاحظات</h6>
        </div>
        <div class="card-body">
            <textarea name="specialist_report" class="form-control" rows="5"
                placeholder="أدخل الملاحظات هنا...">{{ old('specialist_report', $report['notes'] ?? '') }}</textarea>
        </div>
    </div>

    {{-- Footer Section --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">إعداد:</label>
                    <input type="text" name="assessment[prepared_by]" class="form-control" 
                        value="{{ old('assessment.prepared_by', $report['prepared_by'] ?? auth()->user()->name ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">اعتماد:</label>
                    <input type="text" name="assessment[approved_by]" class="form-control" 
                        value="{{ old('assessment.approved_by', $report['approved_by'] ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">التاريخ:</label>
                    <input type="date" name="assessment[assessment_date]" class="form-control" 
                        value="{{ old('assessment.assessment_date', $report['assessment_date'] ?? date('Y-m-d')) }}">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleOtherFactor(checkbox) {
        const otherInput = document.getElementById('other_factor_input');
        if (checkbox.checked) {
            otherInput.style.display = 'block';
        } else {
            otherInput.style.display = 'none';
            otherInput.querySelector('input').value = '';
        }
    }
</script>

<style>
    .beneficiary-assessment-form .table th,
    .beneficiary-assessment-form .table td {
        vertical-align: middle;
    }
    .beneficiary-assessment-form .form-check-inline {
        margin-right: 15px;
    }
    .beneficiary-assessment-form .card {
        border: 1px solid #dee2e6;
    }
    .beneficiary-assessment-form .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
</style>
