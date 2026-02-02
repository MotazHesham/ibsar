<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>نموذج دراسة الحالة</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #fff;
            color: #000;
            line-height: 1.5;
        }

        .document {
            max-width: 1000px;
            margin: 0 auto;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin: 25px 0 10px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: right;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            white-space: nowrap;
        }

        .checkbox {
            display: inline-block;
            margin-left: 15px;
            position: relative;
            padding-right: 20px;
        }

        .checkbox:before {
            content: "☐";
            position: absolute;
            right: 0;
        }

        .checkbox.checked:before {
            content: "☑";
        }

        .dotted-line {
            display: inline-block;
            min-width: 200px;
            border-bottom: 1px dotted #000;
            margin-right: 5px;
            text-align: center
        }

        .signature-line {
            display: inline-block;
            width: 300px;
            border-bottom: 1px solid #000;
            margin-right: 10px;
        }

        .summary {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
            min-height: 50px;
        }
    </style>
</head>

<body>
    <div class="document">

        <div class="section-title">البيانات الشخصية</div>

        <table>
            <tr>
                <th width="20%">اسم المستفيدة</th>
                <td width="30%">{{ $beneficiary->user->name ?? '' }}</td>
                <th width="20%">رقم الهوية</th>
                <td width="30%">{{ $beneficiary->user->identity_num ?? '' }}</td>
            </tr>
            <tr>
                <th>الجنسية</th>
                <td>{{ $beneficiary->nationality->name ?? '' }}</td>
                <th>تاريخ الميلاد</th>
                <td>{{ $beneficiary->dob ?? '' }}</td>
            </tr>
            <tr>
                <th>الحالة الاجتماعية</th>
                <td>
                    {{ $beneficiary->marital_status->name ?? '' }}
                </td>
                <td colspan="2">
                    <strong>تاريخ الوفاه / الطلاق</strong><br>
                    <span class="dotted-line">{{ $beneficiary->martial_status_date ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <th>رقم الجوال</th>
                <td>{{ $beneficiary->user->phone ?? '' }}</td>
                <th>رقم اخر</th>
                <td><span class="dotted-line">{{ $beneficiary->user->phone_2 ?? '' }}</span></td>
            </tr>
            <tr>
                <th>الحالة الصحية</th>
                <td colspan="3">
                    <span class="checkbox @if(!$beneficiary->health_condition_id) checked @endif">سليم</span>
                    <span class="checkbox @if($beneficiary->health_condition_id) checked @endif">غير سليم</span>
                </td>
            </tr>
            <tr>
                <th>وصف الحالة الصحية</th>
                <td colspan="3">{{ $beneficiary->health_condition->name ?? '' }}</td>
            </tr>
            <tr>
                <th>المؤهل العلمي</th>
                <td colspan="3">
                    {{ $beneficiary->educational_qualification->name ?? '' }}
                </td>
            </tr>
        </table>

        <div class="section-title">بيانات الدخل</div>

        <table>
            @php
                $beneficiaryIncomes = json_decode($beneficiary->incomes, true);
            @endphp
            <tr>
                <th width="20%">الدخل الشهري</th>
                <td width="80%" colspan="3">
                    <div>
                        @foreach ($incomes as $income)
                            <span class="checkbox">
                                {{ trans($income->name) }} 
                                <span class="dotted-line">{{ $beneficiaryIncomes[$income->id] ?? '' }} </span>
                            </span> 
                        @endforeach  
                    </div>
                    <br>
                    <span class="dotted-line">أجمالي الدخل : {{ $beneficiary->total_incomes ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <th>الحالة الوظيفية</th>
                <td colspan="3">
                    {{ $beneficiary->job_type->name ?? '' }}
                </td>
            </tr>
            @php
                $jobDetails = json_decode($beneficiary->job_details, true);
            @endphp
            <tr>
                <th>اسم جهة العمل</th>
                <td><span class="dotted-line">{{ $jobDetails['company_name'] ?? '' }}</span></td>
                <th>المسمى الوظيفي</th>
                <td><span class="dotted-line">{{ $jobDetails['job_title'] ?? '' }}</span></td>
            </tr>
            <tr>
                <th>عنوان جهة العمل</th>
                <td><span class="dotted-line">{{ $jobDetails['job_address'] ?? '' }}</span></td>
                <th>هاتف جهة العمل</th>
                <td><span class="dotted-line">{{ $jobDetails['job_phone'] ?? '' }}</span></td>
            </tr>
        </table>

        <div class="section-title">بيانات السكن</div>

        <table>
            <tr>
                <th width="20%">المدينة</th>
                <td width="30%">{{ $beneficiary->city->name ?? '' }}</td>
                <th width="20%">الحي</th>
                <td width="30%"><span class="dotted-line">{{ $beneficiary->district->name ?? '' }}</span></td>
            </tr>
            <tr>
                <th>العنوان الوطني</th>
                <td colspan="3"><span class="dotted-line">{{ $beneficiary->address ?? '' }}</span></td> 
            </tr>
            <tr>
                <th>نوع المنزل</th>
                <td colspan="3">
                    {{ $beneficiary->accommodation_type->name ?? '' }}
                </td>
            </tr>
            @php
                $beneficiaryCaseStudy = json_decode($beneficiary->case_study, true);
                $beneficiaryExpenses = json_decode($beneficiary->expenses, true);
                $accommodationRentLate = isset($beneficiaryExpenses['accommodation_rent_late']) && $beneficiaryExpenses['accommodation_rent_late'] > 0 ? $beneficiaryExpenses['accommodation_rent_late'] : 0;
            @endphp
            <tr>
                <th>قيمة الإيجار</th>
                <td><span class="dotted-line">{{ $beneficiary->accommodation_rent ?? '' }}</span></td>
                <th>هل يوجد تعثر في سداد الايجار</th>
                <td>
                    <span class="checkbox @if($accommodationRentLate > 0) checked @endif">نعم</span>
                    <span class="checkbox @if($accommodationRentLate == 0) checked @endif">لا</span>
                </td>
            </tr>
            <tr>
                <th>عدد الأشهر المتعثرة</th>
                <td><span class="dotted-line">{{ $beneficiaryCaseStudy['rental_month_late'] ?? '' }}</span></td>
                <th>إجمالي قيمة التعثر</th>
                <td><span class="dotted-line">{{ $beneficiaryExpenses['accommodation_rent_late'] ?? '' }}</span></td>
            </tr>
            <tr>
                <th>المستفيدة متضرره من إزالة الاحياء العشوائية</th>
                <td colspan="3">
                    <span class="checkbox @if($beneficiaryCaseStudy['removal_of_districts_affected'] == 'yes') checked @endif">نعم</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['removal_of_districts_affected'] == 'no') checked @endif">لا</span><br>
                    اسم الحي <span class="dotted-line">{{ $beneficiaryCaseStudy['removal_of_districts_district'] ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <th>المستفيدة حاصلة على دعم امانة جدة (التسكين)</th>
                <td colspan="3">
                    <span class="checkbox @if($beneficiaryCaseStudy['jeddah_municipality_support_has'] == 'yes') checked @endif">نعم</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['jeddah_municipality_support_has'] == 'no') checked @endif">لا</span><br>
                    مدة الدعم : <span class="dotted-line">{{ $beneficiaryCaseStudy['jeddah_municipality_support_support_duration'] ?? '' }}</span><br>
                    مبلغ الدعم : <span class="dotted-line">{{ $beneficiaryCaseStudy['jeddah_municipality_support_support_amount'] ?? '' }}</span>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <th width="20%">جودة المسكن</th>
                <td width="80%">
                    <span class="checkbox @if($beneficiaryCaseStudy['housing_quality'] == 'good') checked @endif">بحالة ممتازة</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['housing_quality'] == 'mid') checked @endif">بحالة متوسطة</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['housing_quality'] == 'poor') checked @endif">متهالك</span>
                </td>
            </tr>
            <tr>
                <th>جودة الأثاث</th>
                <td>
                    <span class="checkbox @if($beneficiaryCaseStudy['furniture_quality'] == 'very_good') checked @endif">ممتاز</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['furniture_quality'] == 'good') checked @endif">جيد</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['furniture_quality'] == 'need_to_change') checked @endif">يحتاج تغيير</span><br>
                    حدد الأثاث: <span class="dotted-line">{{ $beneficiaryCaseStudy['furniture_quality_details'] ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <th>جودة الأجهزة الكهربائية</th>
                <td>
                    <span class="checkbox @if($beneficiaryCaseStudy['electrical_devices_quality'] == 'very_good') checked @endif">ممتاز</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['electrical_devices_quality'] == 'good') checked @endif">جيد</span>
                    <span class="checkbox @if($beneficiaryCaseStudy['electrical_devices_quality'] == 'need_to_change') checked @endif">يحتاج تغيير</span><br>
                    حدد الأثاث: <span class="dotted-line">{{ $beneficiaryCaseStudy['electrical_devices_quality_details'] ?? '' }}</span>
                </td>
            </tr>
        </table>

        <div class="section-title">ملخص الحالة</div>
        <div class="summary">
            {!! $beneficiaryCaseStudy['summary_of_the_case'] ?? '' !!}
        </div>

        <div class="section-title">التدخل المقترح</div>
        <div class="summary">
            {!! $beneficiaryCaseStudy['proposed_intervention'] ?? '' !!}
        </div>

        <p>
            الاخصائية الاجتماعية : {{ $beneficiary->specialist->name ?? '' }}
            <br>
            التوقيع : <span class="signature-line"></span>
        </p>

    </div>

    <script>
        window.print();
    </script>
</body>

</html>
