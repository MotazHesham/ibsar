@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiariesManagment.title'), 'url' => route('admin.beneficiaries.index')],
            ['title' => trans('global.list') . ' ' . trans('cruds.beneficiary.title'), 'url' => route('admin.beneficiaries.index')],
            ['title' => 'استيراد المستفيدين', 'url' => '#'],
        ];
        $buttons = [
            [
                'title' => trans('global.back'),
                'url' => route('admin.beneficiaries.index'),
                'permission' => 'beneficiary_access',
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">استيراد المستفيدين من ملف CSV</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> التعليمات</h5>
                        <ul class="mb-0">
                            <li>قم برفع ملف CSV يحتوي على بيانات المستفيدين</li>
                            <li>قم بتعيين أعمدة CSV إلى حقول قاعدة البيانات</li>
                            <li>اختر عمود المعرف الفريد (handle) للتحديثات</li>
                            <li>راجع المعاينة قبل المعالجة</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- الخطوة 1: رفع الملف -->
            <div id="step1" class="import-step">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="csv_file">اختر ملف CSV</label>
                            <input type="file" id="csv_file" name="csv_file" class="form-control" accept=".csv,.txt">
                            <small class="form-text text-muted">الحد الأقصى لحجم الملف: 10 ميجابايت. الصيغ المدعومة: CSV, TXT</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <br>
                            <button type="button" id="uploadBtn" class="btn btn-primary btn-block">
                                <i class="fas fa-upload"></i> رفع ومعاينة
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الخطوة 2: تعيين الأعمدة -->
            <div id="step2" class="import-step" style="display: none;">
                <div class="row">
                    <div class="col-md-12">
                        <h5>الخطوة 2: تعيين أعمدة CSV إلى حقول قاعدة البيانات</h5>
                        <div class="alert alert-warning">
                            <strong>مهم:</strong> اختر عمود المعرف الفريد (handle) الذي سيتم استخدامه لتحديث السجلات الموجودة.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6>معاينة CSV (أول 5 صفوف)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="previewTable">
                                <thead id="previewHeaders">
                                </thead>
                                <tbody id="previewBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>تعيين الأعمدة</h6>
                        <div id="columnMapping">
                            <!-- سيتم إنشاء تعيين الأعمدة هنا -->
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" id="processBtn" class="btn btn-success">
                            <i class="fas fa-play"></i> معالجة الاستيراد
                        </button>
                        <button type="button" id="backToStep1" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> العودة إلى الرفع
                        </button>
                    </div>
                </div>
            </div>

            <!-- الخطوة 3: النتائج -->
            <div id="step3" class="import-step" style="display: none;">
                <div class="row">
                    <div class="col-md-12">
                        <h5>نتائج الاستيراد</h5>
                        <div id="importResults">
                            <!-- ستتم عرض النتائج هنا -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function() {
            let csvData = null;
            let databaseColumns = null;
            let filePath = null;

            // الخطوة 1: رفع الملف
            $('#uploadBtn').click(function() {
                const fileInput = $('#csv_file')[0];
                const file = fileInput.files[0];

                if (!file) {
                    alert('يرجى اختيار ملف CSV');
                    return;
                }

                const formData = new FormData();
                formData.append('csv_file', file);
                formData.append('_token', '{{ csrf_token() }}');

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري الرفع...');

                $.ajax({
                    url: '{{ route("admin.beneficiaries.import.upload") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            csvData = response.preview_data;
                            databaseColumns = response.database_columns;
                            filePath = response.file_path;
                            
                            displayPreview(response.headers, response.preview_data);
                            generateColumnMapping(response.headers, response.database_columns);
                            
                            $('#step1').hide();
                            $('#step2').show();
                        } else {
                            alert('خطأ: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'فشل الرفع';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert('خطأ: ' + message);
                    },
                    complete: function() {
                        $('#uploadBtn').prop('disabled', false).html('<i class="fas fa-upload"></i> رفع ومعاينة');
                    }
                });
            });

            // العودة إلى الخطوة 1
            $('#backToStep1').click(function() {
                $('#step2').hide();
                $('#step1').show();
                $('#csv_file').val('');
            });

            // معالجة الاستيراد
            $('#processBtn').click(function() {
                const columnMapping = {};
                const handleColumn = $('#handle_column').val();

                if (!handleColumn) {
                    alert('يرجى اختيار عمود المعرف');
                    return;
                }

                $('.column-mapping-select').each(function() {
                    const dbColumn = $(this).data('db-column');
                    const csvColumn = $(this).val();
                    if (csvColumn) {
                        columnMapping[dbColumn] = csvColumn;
                    }
                });

                if (Object.keys(columnMapping).length === 0) {
                    alert('يرجى تعيين عمود واحد على الأقل');
                    return;
                }

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...');

                $.ajax({
                    url: '{{ route("admin.beneficiaries.import.process") }}',
                    type: 'POST',
                    data: {
                        file_path: filePath,
                        column_mapping: columnMapping,
                        handle_column: handleColumn,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            displayResults(response.results);
                            $('#step2').hide();
                            $('#step3').show();
                        } else {
                            alert('خطأ: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let message = 'فشلت المعالجة';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        alert('خطأ: ' + message);
                    },
                    complete: function() {
                        $('#processBtn').prop('disabled', false).html('<i class="fas fa-play"></i> معالجة الاستيراد');
                    }
                });
            });

            function displayPreview(headers, data) {
                let headerHtml = '<tr>';
                headers.forEach(function(header) {
                    headerHtml += '<th>' + header + '</th>';
                });
                headerHtml += '</tr>';
                $('#previewHeaders').html(headerHtml);

                let bodyHtml = '';
                data.forEach(function(row) {
                    bodyHtml += '<tr>';
                    headers.forEach(function(header) {
                        bodyHtml += '<td>' + (row[header] || '') + '</td>';
                    });
                    bodyHtml += '</tr>';
                });
                $('#previewBody').html(bodyHtml);
            }

            function generateColumnMapping(csvHeaders, dbColumns) {
                let html = '<div class="form-group">';
                html += '<label for="handle_column">عمود المعرف (المعرف الفريد) *</label>';
                html += '<select id="handle_column" class="form-control" required>';
                html += '<option value="">اختر عمود للتعريف الفريد</option>';
                let count = 0;
                csvHeaders.forEach(function(header) {
                    html += '<option value="' + count + '">' + header + '</option>';
                    count++;
                });
                html += '</select>';
                html += '<small class="form-text text-muted">سيتم استخدام هذا العمود لتحديد السجلات الموجودة للتحديثات</small>';
                html += '</div>';

                html += '<hr><h6>حقول قاعدة البيانات</h6>';
                html += '<div class="row">';

                Object.keys(dbColumns).forEach(function(dbColumn) {
                    html += '<div class="form-group col-md-4">';
                    html += '<label for="' + dbColumn + '">' + dbColumns[dbColumn] + '</label>';
                    html += '<select class="form-control column-mapping-select" data-db-column="' + dbColumn + '">';
                    html += '<option value="">-- غير معين --</option>';
                    let count = 0;
                    csvHeaders.forEach(function(header) {
                        html += '<option value="' + count + '">' + header + '</option>';
                        count++;
                    }); 
                    html += '</select>';
                    html += '</div>';
                });
                html += '</div>';
                $('#columnMapping').html(html);
            }

            function displayResults(results) {
                let html = '<div class="alert alert-success">';
                html += '<h6>ملخص الاستيراد</h6>';
                html += '<ul class="mb-0">';
                html += '<li>السجلات المستوردة: ' + results.imported + '</li>';
                html += '<li>السجلات المحدثة: ' + results.updated + '</li>';
                if (results.failed_rows && results.failed_rows.length > 0) {
                    html += '<li>الصفوف الفاشلة: ' + results.failed_rows.length + '</li>';
                }
                html += '</ul>';
                html += '</div>';

                if (results.failed_rows && results.failed_rows.length > 0) {
                    html += '<div class="alert alert-danger">';
                    html += '<h6>تفاصيل الصفوف الفاشلة:</h6>';
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-sm table-bordered">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th>الصف</th>'; 
                    html += '<th>الخطأ</th>';
                    html += '<th>البيانات</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';
                    
                    results.failed_rows.forEach(function(failedRow) {
                        html += '<tr>';
                        html += '<td>' + failedRow.row + '</td>'; 
                        html += '<td><span class="text-danger">' + failedRow.error + '</span></td>';
                        html += '<td>';
                        if (failedRow.data && Object.keys(failedRow.data).length > 0) {
                            html += '<small>';
                            Object.keys(failedRow.data).forEach(function(key) {
                                html += '<strong>' + key + ':</strong> ' + failedRow.data[key] + '<br>';
                            });
                            html += '</small>';
                        } else {
                            html += '<em>لا توجد بيانات</em>';
                        }
                        html += '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody>';
                    html += '</table>';
                    html += '</div>';
                    html += '</div>';
                }

                html += '<div class="mt-3">';
                html += '<a href="{{ route("admin.beneficiaries.index") }}" class="btn btn-primary">';
                html += '<i class="fas fa-list"></i> العودة إلى قائمة المستفيدين';
                html += '</a>';
                html += '<a href="{{ route("admin.beneficiaries.import") }}" class="btn btn-secondary ml-2">';
                html += '<i class="fas fa-upload"></i> استيراد ملف آخر';
                html += '</a>';
                html += '</div>';

                $('#importResults').html(html);
            }
        });
    </script>
@endsection 