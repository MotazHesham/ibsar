{{-- Report Modal for Training Category --}}
@php
    $categoryData = $workflow->training;
    $report = null;
    $hasReport = false;
    if ($categoryData && $categoryData->specialist_report) {
        $decoded = json_decode($categoryData->specialist_report, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $report = $decoded;
            $hasReport = true;
        } else {
            $hasReport = true;
        }
    }
@endphp

@if($hasReport)
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalLabel">تقرير التقييم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($report)
                        {{-- Header Information --}}
                        @if(isset($report['requesting_authority']) || isset($report['assessment_goal']))
                            <div class="card mb-3">
                                <div class="card-body">
                                    @if(isset($report['requesting_authority']))
                                        <p class="mb-2"><strong>الجهة الطالبة للتقرير:</strong> {{ $report['requesting_authority'] }}</p>
                                    @endif
                                    @if(isset($report['assessment_goal']))
                                        <p class="mb-2"><strong>هدف التقييم:</strong> {{ $report['assessment_goal'] }}</p>
                                    @endif
                                    @if(isset($report['assessment_date']))
                                        <p class="mb-0"><strong>تاريخ التقييم:</strong> {{ $report['assessment_date'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Section I: Primary Assessment Areas --}}
                        @if(isset($report['comprehension']) || isset($report['practical']) || isset($report['factors']))
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>أولاً: مجالات التقييم الأساسية</strong>
                                </div>
                                <div class="card-body">
                                    {{-- Comprehension and Understanding --}}
                                    @if(isset($report['comprehension']))
                                        <div class="mb-3">
                                            <strong>الاستيعاب والفهم:</strong>
                                            <ul class="list-unstyled ms-3 mb-0 mt-2">
                                                @if(isset($report['comprehension']['content_understanding']))
                                                    <li>فهم المحتوى: <span class="badge bg-info">{{ $report['comprehension']['content_understanding'] }}</span></li>
                                                @endif
                                                @if(isset($report['comprehension']['repeated_explanation']))
                                                    <li>احتياج لشرح متكرر: <span class="badge bg-info">{{ $report['comprehension']['repeated_explanation'] }}</span></li>
                                                @endif
                                                @if(isset($report['comprehension']['comprehension_speed']))
                                                    <li>سرعة الاستيعاب: <span class="badge bg-info">{{ $report['comprehension']['comprehension_speed'] }}</span></li>
                                                @endif
                                                @if(isset($report['comprehension']['auditory_comprehension']))
                                                    <li>القدرة على الاستيعاب السمعي: <span class="badge bg-info">{{ $report['comprehension']['auditory_comprehension'] }}</span></li>
                                                @endif
                                                @if(isset($report['comprehension']['memory_concentration']))
                                                    <li>مشكلات التذكر والتركيز: <span class="badge bg-info">{{ $report['comprehension']['memory_concentration'] }}</span></li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Practical Application --}}
                                    @if(isset($report['practical']))
                                        <div class="mb-3">
                                            <strong>التطبيق العملي:</strong>
                                            <ul class="list-unstyled ms-3 mb-0 mt-2">
                                                @if(isset($report['practical']['task_execution']))
                                                    <li>تنفيذ المهام: <span class="badge bg-info">{{ $report['practical']['task_execution'] }}</span></li>
                                                @endif
                                                @if(isset($report['practical']['self_reliance']))
                                                    <li>الاعتماد على النفس: <span class="badge bg-info">{{ $report['practical']['self_reliance'] }}</span></li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif

                                    {{-- Influencing Factors --}}
                                    @if(isset($report['factors']) && is_array($report['factors']) && count($report['factors']) > 0)
                                        <div class="mb-0">
                                            <strong>العوامل المؤثرة:</strong>
                                            <ul class="list-unstyled ms-3 mb-0 mt-2">
                                                @foreach($report['factors'] as $factor)
                                                    <li><span class="badge bg-warning">{{ $factor }}</span></li>
                                                @endforeach
                                                @if(isset($report['factors_other']) && !empty($report['factors_other']))
                                                    <li>أخرى: {{ $report['factors_other'] }}</li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Section II: Visual Assessment --}}
                        @if(isset($report['readiness']) || isset($report['nature_of_need']) || isset($report['priority']))
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>ثانياً: التقييم البصري</strong>
                                </div>
                                <div class="card-body">
                                    @if(isset($report['readiness']) && is_array($report['readiness']) && count($report['readiness']) > 0)
                                        <p class="mb-2"><strong>مستوى الجاهزية العامة:</strong>
                                            @foreach($report['readiness'] as $readiness)
                                                <span class="badge bg-secondary me-1">{{ $readiness }}</span>
                                            @endforeach
                                        </p>
                                    @endif
                                    @if(isset($report['nature_of_need']) && is_array($report['nature_of_need']) && count($report['nature_of_need']) > 0)
                                        <p class="mb-2"><strong>طبيعة الاحتياج:</strong>
                                            @foreach($report['nature_of_need'] as $need)
                                                <span class="badge bg-secondary me-1">{{ $need }}</span>
                                            @endforeach
                                        </p>
                                    @endif
                                    @if(isset($report['priority']) && is_array($report['priority']) && count($report['priority']) > 0)
                                        <p class="mb-0"><strong>مستوى الأولوية:</strong>
                                            @foreach($report['priority'] as $priority)
                                                <span class="badge bg-secondary me-1">{{ $priority }}</span>
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Section III: Recommendations --}}
                        @if(isset($report['recommendations']) && is_array($report['recommendations']))
                            @php
                                $hasRecommendations = false;
                                foreach($report['recommendations'] as $rec) {
                                    if(!empty($rec)) {
                                        $hasRecommendations = true;
                                        break;
                                    }
                                }
                            @endphp
                            @if($hasRecommendations)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <strong>ثالثاً: التوصيات</strong>
                                    </div>
                                    <div class="card-body">
                                        <ol class="mb-0">
                                            @foreach($report['recommendations'] as $index => $recommendation)
                                                @if(!empty($recommendation))
                                                    <li>{{ $recommendation }}</li>
                                                @endif
                                            @endforeach
                                        </ol>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Section IV: Notes --}}
                        @if(isset($report['notes']) && !empty($report['notes']))
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>رابعاً: الملاحظات</strong>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $report['notes'] }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Footer --}}
                        @if(isset($report['prepared_by']) || isset($report['approved_by']))
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="row">
                                        @if(isset($report['prepared_by']))
                                            <div class="col-md-4">
                                                <p class="mb-0"><strong>إعداد:</strong> {{ $report['prepared_by'] }}</p>
                                            </div>
                                        @endif
                                        @if(isset($report['approved_by']))
                                            <div class="col-md-4">
                                                <p class="mb-0"><strong>اعتماد:</strong> {{ $report['approved_by'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Fallback: if specialist_report is not JSON, display as plain text --}}
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-0">{{ $categoryData->specialist_report }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printReport()">طباعة</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Print-friendly version (hidden) --}}
    <div id="printReport" style="display: none;">
        @php
            $dynamicServiceOrder = $workflow->dynamicServiceOrder;
            $beneficiary = $dynamicServiceOrder->beneficiaryOrder->beneficiary ?? null;
            $beneficiaryUser = $beneficiary->user ?? null;
        @endphp
        <div class="print-container" style="direction: rtl; font-family: Arial, sans-serif; padding: 20px;">
            @if($report)
            <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 15px;">
                <h2 style="margin: 0;">نموذج تقييم مستفيد</h2>
            </div>

            {{-- Beneficiary Information Table --}}
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="border: 1px solid #000; padding: 8px; width: 50%;"><strong>الجهة الطالبة للتقرير:</strong> {{ $report['requesting_authority'] ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 8px; width: 50%;"><strong>اسم المستفيد:</strong> {{ $beneficiaryUser->name ?? '' }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px;"><strong>تاريخ الميلاد:</strong> {{ $beneficiary->dob ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 8px;"><strong>رقم الهوية:</strong> {{ $beneficiaryUser->identity_num ?? '' }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px;"><strong>رقم التسجيل:</strong> {{ $beneficiary->id ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 8px;"><strong>الحالة البصرية:</strong> {{ optional($beneficiary->disability_type ?? null)->name ?? '' }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 8px;"><strong>هدف التقييم:</strong> {{ $report['assessment_goal'] ?? '' }}</td>
                    <td style="border: 1px solid #000; padding: 8px;"><strong>المقيم:</strong> {{ $workflow->specialist->name ?? '' }}</td>
                </tr>
            </table>

            {{-- Section I: Primary Assessment Areas --}}
            <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 5px;">أولاً: مجالات التقييم الأساسية</h3>
            
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th style="border: 1px solid #000; padding: 8px; text-align: right; width: 25%;">المجالات</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: right; width: 35%;">المؤشر</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: right; width: 40%;">التقدير</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Comprehension and Understanding --}}
                    @if(isset($report['comprehension']))
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px; vertical-align: top;" rowspan="5"><strong>الاستيعاب والفهم</strong></td>
                            <td style="border: 1px solid #000; padding: 8px;">فهم المحتوى</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $report['comprehension']['content_understanding'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">احتياج لشرح متكرر</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $report['comprehension']['repeated_explanation'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">سرعة الاستيعاب</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $report['comprehension']['comprehension_speed'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">القدرة على الاستيعاب السمعي</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $report['comprehension']['auditory_comprehension'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">مشكلات التذكر والتركيز</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $report['comprehension']['memory_concentration'] ?? '' }}</td>
                        </tr>
                    @endif

                    {{-- Practical Application --}}
                    @if(isset($report['practical']))
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px; vertical-align: top;" rowspan="2"><strong>التطبيق العملي</strong></td>
                            <td style="border: 1px solid #000; padding: 8px;">تنفيذ المهام</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $report['practical']['task_execution'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">الاعتماد على النفس</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $report['practical']['self_reliance'] ?? '' }}</td>
                        </tr>
                    @endif

                    {{-- Influencing Factors --}}
                    @if(isset($report['factors']) && is_array($report['factors']) && count($report['factors']) > 0)
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;"><strong>العوامل المؤثرة</strong></td>
                            <td style="border: 1px solid #000; padding: 8px;" colspan="2">
                                @foreach($report['factors'] as $factor)
                                    {{ $factor }}{{ !$loop->last ? '، ' : '' }}
                                @endforeach
                                @if(isset($report['factors_other']) && !empty($report['factors_other']))
                                    @if(count($report['factors']) > 0)، @endifأخرى: {{ $report['factors_other'] }}
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            {{-- Section II: Visual Assessment --}}
            @if(isset($report['readiness']) || isset($report['nature_of_need']) || isset($report['priority']))
                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 5px;">ثانياً: التقييم البصري (إن طلب)</h3>
                <p style="text-align: justify; margin-bottom: 15px;">
                    بناءً على بيانات التقديم ونتائج التقييم، تم دراسة حالة المستفيد من الجوانب التعليمية، المهارية، التقنية، والحياتية، بهدف تحديد المسار التدريبي الأنسب واحتياج الدعم.
                </p>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; width: 33%;">
                            <strong>مستوى الجاهزية العامة:</strong><br>
                            @if(isset($report['readiness']) && is_array($report['readiness']))
                                @foreach($report['readiness'] as $readiness)
                                    {{ $readiness }}{{ !$loop->last ? '، ' : '' }}
                                @endforeach
                            @endif
                        </td>
                        <td style="border: 1px solid #000; padding: 8px; width: 33%;">
                            <strong>طبيعة الاحتياج:</strong><br>
                            @if(isset($report['nature_of_need']) && is_array($report['nature_of_need']))
                                @foreach($report['nature_of_need'] as $need)
                                    {{ $need }}{{ !$loop->last ? '، ' : '' }}
                                @endforeach
                            @endif
                        </td>
                        <td style="border: 1px solid #000; padding: 8px; width: 33%;">
                            <strong>مستوى الأولوية:</strong><br>
                            @if(isset($report['priority']) && is_array($report['priority']))
                                @foreach($report['priority'] as $priority)
                                    {{ $priority }}{{ !$loop->last ? '، ' : '' }}
                                @endforeach
                            @endif
                        </td>
                    </tr>
                </table>
            @endif

            {{-- Section III: Recommendations --}}
            @if(isset($report['recommendations']) && is_array($report['recommendations']))
                @php
                    $hasRecommendations = false;
                    foreach($report['recommendations'] as $rec) {
                        if(!empty($rec)) {
                            $hasRecommendations = true;
                            break;
                        }
                    }
                @endphp
                @if($hasRecommendations)
                    <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 5px;">ثالثاً: التوصيات</h3>
                    <ol style="padding-right: 20px;">
                        @foreach($report['recommendations'] as $index => $recommendation)
                            @if(!empty($recommendation))
                                <li style="margin-bottom: 10px;">{{ $recommendation }}</li>
                            @endif
                        @endforeach
                    </ol>
                @endif
            @endif

            {{-- Section IV: Notes --}}
            @if(isset($report['notes']) && !empty($report['notes']))
                <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #000; padding-bottom: 5px;">رابعاً: الملاحظات</h3>
                <div style="border: 1px solid #000; padding: 15px; min-height: 100px;">
                    {{ $report['notes'] }}
                </div>
            @endif

            {{-- Footer --}}
            <table style="width: 100%; border-collapse: collapse; margin-top: 50px;">
                <tr>
                    <td style="padding: 8px; width: 33%;">
                        <strong>إعداد:</strong> {{ $report['prepared_by'] ?? '' }}
                    </td>
                    <td style="padding: 8px; width: 33%;">
                        <strong>اعتماد:</strong> {{ $report['approved_by'] ?? '' }}
                    </td>
                    <td style="padding: 8px; width: 33%;">
                        <strong>التاريخ:</strong> {{ $report['assessment_date'] ?? '' }}
                    </td>
                </tr>
            </table>
            @else
                {{-- Fallback: Plain text report --}}
                <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 15px;">
                    <h2 style="margin: 0;">تقرير الأخصائي</h2>
                </div>
                <div style="border: 1px solid #000; padding: 20px; min-height: 300px;">
                    {{ $categoryData->specialist_report }}
                </div>
            @endif
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printReport, #printReport * {
                visibility: visible;
            }
            #printReport {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                display: block !important;
            }
            .print-container {
                page-break-inside: avoid;
            }
            table {
                page-break-inside: avoid;
            }
        }
    </style>

    <script>
        function printReport() {
            window.print();
        }
    </script>
@endif

