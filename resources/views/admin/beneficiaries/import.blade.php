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
                            <li>اختر صيغة التاريخ المستخدمة في أعمدة التاريخ داخل ملف CSV</li>
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
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label for="csv_date_format">صيغة التاريخ في ملف CSV</label>
                            <select id="csv_date_format" class="form-control" style="max-width: 36rem;"></select>
                            <small class="form-text text-muted">تُستخدم لقراءة تاريخ الميلاد، تاريخ الحالة الاجتماعية، وتاريخ الإنشاء (إن وُجد في التعيين).</small>
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
            let csvHeaders = null;
            let lastImportSettings = null;

            function collectImportSettings() {
                const columnMapping = {};
                const handleColumn = $('#handle_column').val();

                $('.column-mapping-select').each(function() {
                    const dbColumn = $(this).data('db-column');
                    const csvColumn = $(this).val();
                    if (csvColumn) {
                        columnMapping[dbColumn] = csvColumn;
                    }
                });

                return {
                    columnMapping: columnMapping,
                    handleColumn: handleColumn,
                    dateFormat: $('#csv_date_format').val(),
                };
            }

            function restoreImportSettings(settings) {
                if (!settings) {
                    return;
                }

                $('#handle_column').val(settings.handleColumn || '');
                if (settings.dateFormat) {
                    $('#csv_date_format').val(settings.dateFormat);
                }

                $('.column-mapping-select').each(function() {
                    const dbColumn = $(this).data('db-column');
                    $(this).val(settings.columnMapping[dbColumn] || '');
                });
            }

            function goBackToMapping() {
                restoreImportSettings(lastImportSettings);
                $('#step3').hide();
                $('#step2').show();
                $('html, body').animate({
                    scrollTop: $('#step2').offset().top - 20
                }, 300);
            }

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
                            csvHeaders = response.headers;
                            lastImportSettings = null;

                            fillDateFormatSelect(
                                response.date_format_options || {},
                                response.default_date_format || '{{ config('panel.date_format') }}'
                            );
                            
                            displayPreview(response.headers, response.preview_data);
                            generateColumnMapping(response.headers, response.database_columns);
                            
                            $('#step1').hide();
                            $('#step2').show();
                            $('#step3').hide();
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
                $('#step3').hide();
                $('#step1').show();
                $('#csv_file').val('');
                lastImportSettings = null;
            });

            $(document).on('click', '#retryImportBtn', function() {
                goBackToMapping();
            });

            // معالجة الاستيراد
            $('#processBtn').click(function() {
                const handleColumn = $('#handle_column').val();

                if (!handleColumn) {
                    alert('يرجى اختيار عمود المعرف');
                    return;
                }

                const columnMapping = collectImportSettings().columnMapping;

                if (Object.keys(columnMapping).length === 0) {
                    alert('يرجى تعيين عمود واحد على الأقل');
                    return;
                }

                lastImportSettings = collectImportSettings();

                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...');

                $.ajax({
                    url: '{{ route("admin.beneficiaries.import.process") }}',
                    type: 'POST',
                    data: {
                        file_path: filePath,
                        column_mapping: lastImportSettings.columnMapping,
                        handle_column: lastImportSettings.handleColumn,
                        date_format: lastImportSettings.dateFormat,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.file_path) {
                                filePath = response.file_path;
                            }
                            displayResults(response.results, response.can_retry);
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

            function fillDateFormatSelect(options, defaultFormat) {
                let html = '';
                const keys = Object.keys(options);
                if (keys.length === 0) {
                    const fallback = {
                        'd/m/Y': 'يوم/شهر/سنة (01/05/2026)',
                        'd-m-Y': 'يوم-شهر-سنة (01-05-2026)',
                        'Y-m-d': 'سنة-شهر-يوم (2026-05-01)',
                        'Y/m/d': 'سنة/شهر/يوم (2026/05/01)',
                        'm/d/Y': 'شهر/يوم/سنة (05/01/2026)'
                    };
                    Object.keys(fallback).forEach(function(fmt) {
                        html += '<option value="' + fmt + '">' + fallback[fmt] + '</option>';
                    });
                } else {
                    keys.forEach(function(fmt) {
                        html += '<option value="' + fmt + '">' + options[fmt] + '</option>';
                    });
                }
                $('#csv_date_format').html(html);
                if ($('#csv_date_format option[value="' + defaultFormat + '"]').length) {
                    $('#csv_date_format').val(defaultFormat);
                }
            }

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

            function generateColumnMapping(headers, dbColumns, savedSettings) {
                let html = '<div class="form-group">';
                html += '<label for="handle_column">عمود المعرف (المعرف الفريد) *</label>';
                html += '<select id="handle_column" class="form-control" required>';
                html += '<option value="">اختر عمود للتعريف الفريد</option>';
                let count = 0;
                headers.forEach(function(header) {
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
                    headers.forEach(function(header) {
                        html += '<option value="' + count + '">' + header + '</option>';
                        count++;
                    }); 
                    html += '</select>';
                    html += '</div>';
                });
                html += '</div>';
                $('#columnMapping').html(html);

                if (savedSettings) {
                    restoreImportSettings(savedSettings);
                }
            }

            function displayResults(results, canRetry) {
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
                if (canRetry && results.failed_rows && results.failed_rows.length > 0) {
                    html += '<button type="button" id="retryImportBtn" class="btn btn-warning">';
                    html += '<i class="fas fa-redo"></i> إعادة المحاولة (تعديل تعيين الأعمدة)';
                    html += '</button>';
                }
                html += '<a href="{{ route("admin.beneficiaries.index") }}" class="btn btn-primary ms-2">';
                html += '<i class="fas fa-list"></i> العودة إلى قائمة المستفيدين';
                html += '</a>';
                html += '<a href="{{ route("admin.beneficiaries.import") }}" class="btn btn-secondary ms-2">';
                html += '<i class="fas fa-upload"></i> استيراد ملف آخر';
                html += '</a>';
                html += '</div>';

                $('#importResults').html(html);
            }
        });
    </script>
@endsection 