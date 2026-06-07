<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\Nationality;
use App\Models\MaritalStatus;
use App\Models\AccommodationType;
use App\Models\AccommodationEntity;
use App\Models\JobType;
use App\Models\EducationalQualification;
use App\Models\Region;
use App\Models\City;
use App\Models\District;
use App\Models\HealthCondition;
use App\Models\DisabilityType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class BeneficiaryImportController extends Controller
{
    public function showImportForm()
    {
        abort_if(Gate::denies('beneficiary_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.beneficiaries.import');
    }

    public function uploadCsv(Request $request)
    {
        abort_if(Gate::denies('beneficiary_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $filename = 'import_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('temp/imports', $filename);

            // Read CSV file
            $csvData = $this->readCsvFile($path);

            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or invalid'
                ], 400);
            }

            // Get first 5 rows for preview
            $previewData = array_slice($csvData, 0, 5);
            $headers = array_keys($previewData[0]);

            // Get available database columns
            $databaseColumns = $this->getDatabaseColumns();

            return response()->json([
                'success' => true,
                'file_path' => $path,
                'headers' => $headers,
                'preview_data' => $previewData,
                'database_columns' => $databaseColumns,
                'total_rows' => count($csvData),
                'date_format_options' => $this->importDateFormatOptions(),
                'default_date_format' => config('panel.date_format'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processImport(Request $request)
    {
        abort_if(Gate::denies('beneficiary_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'file_path' => 'required|string',
            'column_mapping' => 'required|array',
            'handle_column' => 'required|string',
            'date_format' => ['nullable', Rule::in($this->allowedImportDateFormats())],
        ]);

        try {
            $filePath = $request->input('file_path');
            $columnMapping = $request->input('column_mapping');
            $handleColumn = $request->input('handle_column');
            $csvDateFormat = $request->input('date_format') ?: config('panel.date_format');

            if (!Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import file not found'
                ], 400);
            }

            $csvData = $this->readCsvFile($filePath, false);
            $results = $this->processCsvData($csvData, $columnMapping, $handleColumn, $csvDateFormat);

            $hasFailures = count($results['failed_rows']) > 0;

            // Keep temp file when there are failed rows so the user can retry import
            if (!$hasFailures) {
                Storage::delete($filePath);
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'can_retry' => $hasFailures,
                'file_path' => $hasFailures ? $filePath : null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing import: ' . $e->getMessage()
            ], 500);
        }
    }

    private function readCsvFile($filePath, $keyName = true)
    {
        $data = [];
        $file = Storage::path($filePath);

        if (($handle = fopen($file, "r")) !== FALSE) {
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== FALSE) {
                if (count($row) === count($headers)) {
                    if ($keyName) {
                        $data[] = array_combine($headers, $row);
                    } else {
                        $data[] = $row;
                    }
                }
            }
            fclose($handle);
        }

        return $data;
    }

    private function getDatabaseColumns()
    {
        return [
            'handle' => 'Handle (External ID)',
            'name' => trans('cruds.user.fields.name'),
            'email' => trans('cruds.user.fields.email'),
            'password' => trans('cruds.user.fields.password'),
            'phone' => trans('cruds.user.fields.phone'),
            'phone_2' => trans('cruds.user.fields.phone_2'),
            'identity_num' => trans('cruds.user.fields.identity_num'),
            'nationality_id' => trans('cruds.beneficiary.fields.nationality'),
            'characteristic_of_nationality' => trans('cruds.beneficiary.fields.characteristic_of_nationality'),
            'marital_status_id' => trans('cruds.beneficiary.fields.marital_status'),
            'dob' => trans('cruds.beneficiary.fields.dob') . ' (' . config('panel.date_format') . ')',
            'martial_status_date' => trans('cruds.beneficiary.fields.martial_status_date') . ' (' . config('panel.date_format') . ')',
            'address' => trans('cruds.beneficiary.fields.address'),
            'latitude' => trans('cruds.beneficiary.fields.latitude'),
            'longitude' => trans('cruds.beneficiary.fields.longitude'),
            'region_id' => trans('cruds.beneficiary.fields.region'),
            'city_id' => trans('cruds.beneficiary.fields.city'),
            'district_id' => trans('cruds.beneficiary.fields.district'),
            'street' => trans('cruds.beneficiary.fields.street'),
            'building_number' => trans('cruds.beneficiary.fields.building_number'),
            'floor_number' => trans('cruds.beneficiary.fields.floor_number'),
            'building_additional_number' => trans('cruds.beneficiary.fields.building_additional_number'),
            'postal_code' => trans('cruds.beneficiary.fields.postal_code'),
            'total_incomes' => trans('cruds.beneficiary.fields.total_incomes'),
            'total_expenses' => trans('cruds.beneficiary.fields.total_expenses'),
            'profile_status' => trans('cruds.beneficiary.fields.profile_status'),
            'file_category' => trans('cruds.beneficiary.fields.file_category'),
            'traits' => trans('cruds.beneficiary.fields.traits'),
            'needs' => trans('cruds.beneficiary.fields.needs'),
            'note' => trans('cruds.beneficiary.fields.note'),
            'data_form_template' => trans('cruds.beneficiary.fields.data_form_template'),
            'gender' => trans('cruds.beneficiary.fields.gender'),
            'children_count' => trans('cruds.beneficiary.fields.children_count'),
            'health_condition_id' => trans('cruds.beneficiary.fields.health_condition'),
            'custom_health_condition' => trans('cruds.beneficiary.fields.custom_health_condition'),
            'disability_type_id' => trans('cruds.beneficiary.fields.disability_type'),
            'custom_disability_type' => trans('cruds.beneficiary.fields.custom_disability_type'),
            'job_type_id' => trans('cruds.beneficiary.fields.job_type'),
            'job_details' => trans('cruds.beneficiary.job_details.job_title'),
            'educational_qualification_id' => trans('cruds.beneficiary.fields.educational_qualification'),
            'created_at' => trans('cruds.beneficiary.fields.created_at') . ' (نفس صيغة التاريخ المختارة للاستيراد)',
        ];
    }

    /**
     * CSV Arabic labels (and aliases) mapped to internal select keys per column.
     *
     * @return array<string, array{select: array<string, string>, aliases?: array<string, string>}>
     */
    private function importSelectValueMappings(): array
    {
        return [
            'profile_status' => [
                'select' => Beneficiary::PROFILE_STATUS_SELECT,
                'aliases' => [
                    'معتمد' => 'approved',
                    'قيد المراجعة' => 'in_review',
                ],
            ],
            'file_category' => [
                'select' => Beneficiary::FILE_CATEGORY_SELECT,
                'aliases' => [
                    'أ' => 'a',
                    'ب' => 'b',
                    'ج' => 'c',
                    'د' => 'd',
                    'مؤقت' => 'temporary',
                ],
            ],
            'data_form_template' => [
                'select' => Beneficiary::DATA_FORM_TEMPLATE_SELECT,
                'aliases' => [
                    'النموذج الافتراضي' => 'default',
                    'نموذج العمليات' => 'operations',
                    'نموذج طفل' => 'child',
                ],
            ],
            'gender' => [
                'select' => Beneficiary::GENDER_SELECT,
                'aliases' => [
                    'ذكر' => 'male',
                    'أنثى' => 'female',
                ],
            ],
            'marital_status_id' => [
                'aliases' => [
                    'اخري' => '5',
                    'اخرى' => '5',
                    'ارملة' => '2',
                    'أعزب' => '4',
                    'الزوجة متوفية' => '2',
                    'عزباء' => '4',
                    'متزوج' => '3',
                    'متزوجة' => '3',
                    'مطلقة' => '1',
                ],
            ],
            'nationality_id' => [
                'aliases' => [
                    'إثيوبيا' => '68',
                    'إريتريا' => '79',
                    'أفغانستان' => '37',
                    'الأردن' => '6',
                    'السعودية' => '1',
                    'السودان' => '3',
                    'الصومال' => '20',
                    'العراق' => '9',
                    'الفلبين' => '31',
                    'المغرب' => '17',
                    'المملكة المتحدة' => '38',
                    'النجير' => '65',
                    'الهند' => '25',
                    'الولايات المتحدة الأمريكية' => '56',
                    'اليمن' => '4',
                    'إندونيسيا' => '32',
                    'باكستان' => '24',
                    'بدون جنسية' => '77',
                    'بروناي' => '78',
                    'بنغلاديش' => '35',
                    'تشاد' => '75',
                    'جنوب إفريقيا' => '66',
                    'سوريا' => '5',
                    'فلسطين' => '7',
                    'فلسطین' => '7',
                    'كينيا' => '67',
                    'لبنان' => '8',
                    'مالي' => '80',
                    'مصر' => '2',
                    'ميانمار' => '76',
                    'نيجيريا' => '65',
                ],
            ],
            'city_id' => [
                'aliases' => [
                    'الجموم' => '61',
                    'الطائف' => '65',
                    'الرياض' => '21',
                    'القنفذة' => '62',
                    'الكامل' => '',
                    'الليث' => '',
                    'بحرة' => '58',
                    'تربة' => '64',
                    'جدة' => '58',
                    'رابغ' => '59',
                    'مكة المكرمة' => '57',
                ],
            ],
            'health_condition_id' => [
                'model' => HealthCondition::class,
                'aliases' => [
                    'سليم' => null,
                    'لا' => null,
                    'لا يوجد' => null,
                    '-' => null,
                    'مريض' => 15,
                ],
            ],
            'educational_qualification_id' => [
                'aliases' => [
                    'ابتدائي' => 3,
                    'أمي' => 1,
                    'ثانوي' => 5,
                    'جامعي' => 14,
                    'دبلوم' => 6,
                    'ماجستير' => 8,
                    'دكتوراه' => 9,
                    'يقرأ ويكتب' => 2,
                    'متوسط' => 4,
                ],
            ],
            'job_type_id' => [
                'aliases' => [
                    'اعمال حرة' => 15,
                    'ربة منزل' => 14,
                    'متقاعد' => 12,
                    'طالب' => 13,
                    'عاطل' => 11,
                    'موظف' => 16,
                ],
            ],
            'disability_type_id' => [
                'aliases' => [
                    'لا' => null,
                    'نعم' => 13,
                ],
            ],

        ];
    }

    /**
     * Convert Arabic CSV select labels to internal database keys.
     *
     * @param  array<string, mixed>  $data
     */
    private function normalizeImportedSelectValues(array &$data): void
    {
        foreach ($this->importSelectValueMappings() as $column => $config) {
            if (!array_key_exists($column, $data)) {
                continue;
            }

            $raw = trim((string) ($data[$column] ?? ''));
            if ($raw === '') {
                unset($data[$column]);
                continue;
            }

            $select = $config['select'] ?? [];
            $aliases = $config['aliases'] ?? [];

            if (array_key_exists($raw, $select)) {
                $data[$column] = $raw;
                continue;
            }

            if (array_key_exists($raw, $aliases)) {
                $aliasValue = $aliases[$raw];
                if ($aliasValue === null || $aliasValue === '') {
                    unset($data[$column]);
                } else {
                    $data[$column] = (string) $aliasValue;
                }
                continue;
            }

            foreach ($select as $key => $label) {
                if (trim($label) === $raw) {
                    $data[$column] = $key;
                    continue 2;
                }
            }

            // if (str_ends_with($column, '_id') && ctype_digit($raw)) {
            //     $data[$column] = $raw;
            //     continue;
            // }

            if (isset($config['model'])) {
                $record = $config['model']::query()
                    ->where('name->ar', $raw)
                    ->orWhere('name->en', $raw)
                    ->first();

                if ($record) {
                    $data[$column] = (string) $record->id;
                    continue;
                }
            }

            throw new \Exception(sprintf('Invalid %s value "%s"', $column, $raw));
        }
    }

    /**
     * Wrap a plain CSV job title into the job_details JSON structure stored on beneficiaries.
     *
     * @param  array<string, mixed>  $data
     */
    private function normalizeImportedJobDetails(array &$data): void
    {
        if (!array_key_exists('job_details', $data)) {
            return;
        }

        $raw = trim((string) ($data['job_details'] ?? ''));
        if ($raw === '') {
            unset($data['job_details']);
            return;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $data['job_details'] = json_encode([
                'company_name' => $decoded['company_name'] ?? null,
                'job_title' => $decoded['job_title'] ?? null,
                'job_phone' => $decoded['job_phone'] ?? null,
                'job_address' => $decoded['job_address'] ?? null,
            ]);
            return;
        }

        $data['job_details'] = json_encode([
            'company_name' => null,
            'job_title' => $raw,
            'job_phone' => null,
            'job_address' => null,
        ]);
    }

    /**
     * PHP date() patterns accepted for CSV date columns (dob, marital status date, created_at).
     *
     * @return list<string>
     */
    private function allowedImportDateFormats(): array
    {
        return ['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d', 'm/d/Y'];
    }

    /**
     * @return array<string, string> format => label (Arabic)
     */
    private function importDateFormatOptions(): array
    {
        return [
            'd/m/Y' => 'يوم/شهر/سنة (01/05/2026) — مطابقة لوحة التحكم',
            'd-m-Y' => 'يوم-شهر-سنة (01-05-2026)',
            'Y-m-d' => 'سنة-شهر-يوم (2026-05-01)',
            'Y/m/d' => 'سنة/شهر/يوم (2026/05/01)',
            'm/d/Y' => 'شهر/يوم/سنة (05/01/2026)',
        ];
    }

    private function processCsvData($csvData, $columnMapping, $handleColumn, string $csvDateFormat)
    {
        $results = [
            'imported' => 0,
            'updated' => 0,
            'errors' => [],
            'failed_rows' => []
        ];

        // Process each row individually without transaction to allow partial success
        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header

            try {
                $mappedData = $this->mapRowData($row, $columnMapping);
                $this->normalizeImportedSelectValues($mappedData);
                $this->normalizeImportedJobDetails($mappedData);
                $this->normalizeImportedDates($mappedData, $csvDateFormat);
                $handle = $row[$handleColumn] ?? null;
                $mappedData['handle'] = $handle;

                if (!$handle) {
                    $errorMessage = "Handle column is required";
                    $results['errors'][] = "Row {$rowNumber}: {$errorMessage}";
                    $results['failed_rows'][] = [
                        'row' => $rowNumber,
                        'handle' => $handle,
                        'error' => $errorMessage,
                        'data' => $mappedData
                    ];
                    continue;
                }

                // Check if beneficiary exists by handle
                $existingBeneficiary = Beneficiary::where('handle', $handle)->first();
                if (!$existingBeneficiary) {
                    $user = User::where('identity_num', $mappedData['identity_num'])->first();
                    if ($user) {
                        $existingBeneficiary = $user->beneficiary;
                    }
                }
                if (!$existingBeneficiary) {
                    $user = User::where('phone', $mappedData['phone'])->first();
                    if ($user) {
                        $existingBeneficiary = $user->beneficiary;
                    }
                }

                if ($existingBeneficiary) {
                    // Update existing beneficiary
                    $this->updateBeneficiary($existingBeneficiary, $mappedData);
                    $results['updated']++;
                } else {
                    // Create new beneficiary
                    $this->createBeneficiary($mappedData);
                    $results['imported']++;
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $results['errors'][] = "Row {$rowNumber}: {$errorMessage}";
                $results['failed_rows'][] = [
                    'row' => $rowNumber,
                    'handle' => $mappedData[$handleColumn] ?? null,
                    'error' => $errorMessage,
                    'data' => $mappedData ?? []
                ];
            }
        }

        return $results;
    }

    private function mapRowData($row, $columnMapping)
    {
        $mappedData = [];

        foreach ($columnMapping as $dbColumn => $csvColumn) {
            if (!is_null($csvColumn) && isset($row[$csvColumn])) {
                if ($dbColumn == 'phone' || $dbColumn == 'phone_2') {
                    // if start with 5 and length is 9, then add 05 to the beginning
                    if (substr($row[$csvColumn], 0, 1) == '5' && strlen($row[$csvColumn]) == 9) {
                        $row[$csvColumn] = '05' . substr($row[$csvColumn], 1);
                    }
                    $mappedData[$dbColumn] = $row[$csvColumn];
                } else {
                    $mappedData[$dbColumn] = $row[$csvColumn];
                }
            }
        }

        return $mappedData;
    }

    /**
     * Convert date strings from the CSV format into values expected by validation and {@see Beneficiary} mutators.
     *
     * @param  array<string, mixed>  $data
     */
    private function normalizeImportedDates(array &$data, string $csvFormat): void
    {
        $panelFormat = config('panel.date_format');

        foreach (['dob', 'martial_status_date'] as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }
            $raw = $data[$field];
            if ($raw === null || $raw === '') {
                unset($data[$field]);
                continue;
            }
            if (!is_string($raw)) {
                continue;
            }
            $trimmed = trim($raw);
            if ($trimmed === '') {
                unset($data[$field]);
                continue;
            }

            if ($csvFormat === $panelFormat) {
                $data[$field] = $trimmed;
                continue;
            }

            try {
                $dt = Carbon::createFromFormat($csvFormat, $trimmed)->startOfDay();
            } catch (\Throwable $e) {
                throw new \Exception(
                    sprintf('Invalid %s "%s" for CSV date format %s', $field, $trimmed, $csvFormat)
                );
            }
            $data[$field] = $dt->format($panelFormat);
        }

        if (!array_key_exists('created_at', $data)) {
            return;
        }
        $raw = $data['created_at'];
        if ($raw === null || $raw === '') {
            unset($data['created_at']);
            return;
        }
        if (!is_string($raw)) {
            return;
        }
        $trimmed = trim($raw);
        if ($trimmed === '') {
            unset($data['created_at']);
            return;
        }

        if ($csvFormat === 'Y-m-d') {
            try {
                Carbon::createFromFormat('Y-m-d', $trimmed)->startOfDay();
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Invalid created_at "%s" for format Y-m-d', $trimmed));
            }
            $data['created_at'] = $trimmed;
            return;
        }

        try {
            $dt = Carbon::createFromFormat($csvFormat, $trimmed)->startOfDay();
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Invalid created_at "%s" for CSV date format %s', $trimmed, $csvFormat));
        }
        $data['created_at'] = $dt->format('Y-m-d');
    }

    private function validateData($data)
    {
        if (array_key_exists('children_count', $data) && trim((string) ($data['children_count'] ?? '')) === '') {
            $data['children_count'] = 0;
        }
        if (array_key_exists('total_incomes', $data) && trim((string) ($data['total_incomes'] ?? '')) === '') {
            $data['total_incomes'] = 0;
        }
        if (array_key_exists('total_expenses', $data) && trim((string) ($data['total_expenses'] ?? '')) === '') {
            $data['total_expenses'] = 0;
        }

        $rules = [
            'name' => 'required',
            'password' => 'required|min:8',
            'email' => 'nullable|email',
            'phone' => 'required|' . config('panel.phone_validation'),
            'phone_2' => 'nullable|' . config('panel.phone_validation'),
            'identity_num' => 'required',
            'nationality_id' => 'nullable|exists:nationalities,id',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
            'job_type_id' => 'nullable|exists:job_types,id',
            'job_details' => 'nullable|string',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'educational_qualification_id' => 'nullable|exists:educational_qualifications,id',
            'health_condition_id' => 'nullable|exists:health_conditions,id',
            'custom_health_condition' => 'nullable|string',
            'profile_status' => 'nullable|in:' . implode(',', array_keys(Beneficiary::PROFILE_STATUS_SELECT)),
            'file_category' => 'nullable|in:' . implode(',', array_keys(Beneficiary::FILE_CATEGORY_SELECT)),
            'traits' => 'nullable|string',
            'needs' => 'nullable|string',
            'note' => 'nullable|string',
            'data_form_template' => 'nullable|in:' . implode(',', array_keys(Beneficiary::DATA_FORM_TEMPLATE_SELECT)),
            'gender' => 'nullable|in:' . implode(',', array_keys(Beneficiary::GENDER_SELECT)),
            'children_count' => 'nullable|integer|min:0',
            'dob' => 'nullable|date_format:' . config('panel.date_format'),
            'martial_status_date' => 'nullable|date_format:' . config('panel.date_format'),
            'building_number' => 'nullable|max:4',
            'building_additional_number' => 'nullable|max:4',
            'postal_code' => 'nullable|max:5',
            'created_at' => 'nullable|date_format:Y-m-d',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorMessages = [];
            foreach ($errors->all() as $error) {
                $errorMessages[] = $error;
            }
            throw new \Exception('Validation failed: ' . implode(', ', $errorMessages));
        }

        return $data;
    }

    private function createBeneficiary($data)
    {
        $data = $this->validateData($data);

        // Set default values when not provided in CSV
        $data['profile_status'] = $data['profile_status'] ?? 'uncompleted';
        $data['form_step'] = $data['form_step'] ?? 'login_information';

        DB::beginTransaction();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'password' => $data['password'],
            'phone' => $data['phone'],
            'phone_2' => $data['phone_2'] ?? null,
            'identity_num' => $data['identity_num'],
            'approved' => 1,
            'user_type' => 'beneficiary',
        ]);

        $beneficiary = Beneficiary::create([
            'handle' => $data['handle'],
            'user_id' => $user->id,

            // Basic Information
            'nationality_id' => $data['nationality_id'] ?? null,
            'characteristic_of_nationality' => $data['characteristic_of_nationality'] ?? null,
            'dob' => $data['dob'] ?? null,
            'marital_status_id' => $data['marital_status_id'] ?? null,
            'martial_status_date' => $data['martial_status_date'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'region_id' => $data['region_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'street' => $data['street'] ?? null,
            'building_number' => $data['building_number'] ?? null,
            'building_additional_number' => $data['building_additional_number'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,

            // Work Information
            'educational_qualification_id' => $data['educational_qualification_id'] ?? null,
            'job_type_id' => $data['job_type_id'] ?? null,
            'job_details' => array_key_exists('job_details', $data) ? $data['job_details'] : null,
            'can_work' => $data['can_work'] ?? null,
            'health_condition_id' => $data['health_condition_id'] ?? null,
            'custom_health_condition' => $data['custom_health_condition'] ?? null,

            // Economic Information
            'total_incomes' => $data['total_incomes'] ?? null,
            'total_expenses' => $data['total_expenses'] ?? null,

            'profile_status' => $data['profile_status'],
            'file_category' => $data['file_category'] ?? null,
            'traits' => $data['traits'] ?? null,
            'needs' => $data['needs'] ?? null,
            'note' => $data['note'] ?? null,
            'data_form_template' => $data['data_form_template'] ?? null,
            'gender' => $data['gender'] ?? null,
            'children_count' => $data['children_count'] ?? 0,
            'form_step' => $data['form_step'],
            'created_at' => $data['created_at'] ?? date('Y-m-d'),
        ]);
        DB::commit();
        return $beneficiary;
    }

    private function updateBeneficiary($beneficiary, $data)
    {
        $data = $this->validateData($data);
        $user = $beneficiary->user;
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'password' => $data['password'],
            'phone' => $data['phone'],
            'phone_2' => $data['phone_2'] ?? null,
            'identity_num' => $data['identity_num'],
        ]);

        $beneficiary->update([

            // Basic Information
            'nationality_id' => $data['nationality_id'] ?? null,
            'characteristic_of_nationality' => $data['characteristic_of_nationality'] ?? null,
            'dob' => $data['dob'] ?? null,
            'marital_status_id' => $data['marital_status_id'] ?? null,
            'martial_status_date' => $data['martial_status_date'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'region_id' => $data['region_id'] ?? null,
            'city_id' => $data['city_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'street' => $data['street'] ?? null,
            'building_number' => $data['building_number'] ?? null,
            'building_additional_number' => $data['building_additional_number'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,

            // Work Information
            'educational_qualification_id' => $data['educational_qualification_id'] ?? null,
            'job_type_id' => $data['job_type_id'] ?? null,
            'job_details' => array_key_exists('job_details', $data) ? $data['job_details'] : $beneficiary->job_details,
            'can_work' => $data['can_work'] ?? null,
            'health_condition_id' => $data['health_condition_id'] ?? null,
            'custom_health_condition' => $data['custom_health_condition'] ?? null,

            // Economic Information
            'total_incomes' => $data['total_incomes'] ?? null,
            'total_expenses' => $data['total_expenses'] ?? null,

            'profile_status' => $data['profile_status'] ?? $beneficiary->profile_status,
            'file_category' => $data['file_category'] ?? $beneficiary->file_category,
            'traits' => $data['traits'] ?? $beneficiary->traits,
            'needs' => $data['needs'] ?? $beneficiary->needs,
            'note' => $data['note'] ?? $beneficiary->note,
            'data_form_template' => $data['data_form_template'] ?? $beneficiary->data_form_template,
            'gender' => $data['gender'] ?? $beneficiary->gender,
            'children_count' => array_key_exists('children_count', $data) ? $data['children_count'] : $beneficiary->children_count,
            'form_step' => $data['form_step'] ?? $beneficiary->form_step,
            'created_at' => $data['created_at'] ?? date('Y-m-d'),
        ]);
    }
}
