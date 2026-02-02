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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
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
                'total_rows' => count($csvData)
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
        ]);
        
        try {
            $filePath = $request->input('file_path');
            $columnMapping = $request->input('column_mapping');
            $handleColumn = $request->input('handle_column');

            if (!Storage::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import file not found'
                ], 400);
            }

            $csvData = $this->readCsvFile($filePath, false);
            $results = $this->processCsvData($csvData, $columnMapping, $handleColumn);

            // Clean up temp file
            Storage::delete($filePath);

            return response()->json([
                'success' => true,
                'results' => $results
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
                    if($keyName){
                        $data[] = array_combine($headers, $row);
                    }else{
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
            'created_at' => trans('cruds.beneficiary.fields.created_at') . ' (Y-m-d)',
        ];
    }

    private function processCsvData($csvData, $columnMapping, $handleColumn)
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
                $handle = $row[$handleColumn] ?? null;

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
                if($dbColumn == 'phone' || $dbColumn == 'phone_2'){
                    // if start with 5 and length is 9, then add 05 to the beginning
                    if(substr($row[$csvColumn], 0, 1) == '5' && strlen($row[$csvColumn]) == 9){
                        $row[$csvColumn] = '05' . substr($row[$csvColumn], 1); 
                    }
                    $mappedData[$dbColumn] = $row[$csvColumn];
                }else{
                    $mappedData[$dbColumn] = $row[$csvColumn];
                }
            }
        }

        return $mappedData;
    }

    private function validateData($data)
    { 
        $rules = [ 
            'name' => 'required',
            'password' => 'required|min:8',
            'email' => 'nullable|email',
            'phone' => 'required|' . config('panel.phone_validation'),
            'phone_2' => 'nullable|' . config('panel.phone_validation'),
            'identity_num' => 'required|' . config('panel.identity_validation'),
            'nationality_id' => 'nullable|exists:nationalities,id',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
            'job_type_id' => 'nullable|exists:job_types,id',
            'region_id' => 'nullable|exists:regions,id',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'educational_qualification_id' => 'nullable|exists:educational_qualifications,id', 
            'can_work' => 'nullable|in:' . implode(',', array_keys(Beneficiary::CAN_WORK_SELECT)), 
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
        
        // Set default values
        $data['profile_status'] = 'uncompleted';
        $data['form_step'] = 'login_information'; 

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
            'can_work' => $data['can_work'] ?? null,

            // Economic Information
            'total_incomes' => $data['total_incomes'] ?? null,
            'total_expenses' => $data['total_expenses'] ?? null, 

            'created_at' => $data['created_at'] ?? date('Y-m-d'),
        ]);
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
            'can_work' => $data['can_work'] ?? null,

            // Economic Information
            'total_incomes' => $data['total_incomes'] ?? null,
            'total_expenses' => $data['total_expenses'] ?? null, 

            'created_at' => $data['created_at'] ?? date('Y-m-d'),
        ]);
    }
} 