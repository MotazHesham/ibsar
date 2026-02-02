<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\BeneficiaryOrder;
use App\Models\Service;
use App\Models\ServiceLoan;
use App\Models\ServiceLoanMember;
use App\Models\User; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; 
use Symfony\Component\HttpFoundation\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BeneficiaryOrderImport;
use App\Models\ServiceLoanPayment;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Services\ServiceLoanPaymentService; 

class BeneficiaryOrderImportController extends Controller
{  

    protected $serviceLoanPaymentService; 

    public function __construct(ServiceLoanPaymentService $serviceLoanPaymentService)
    {
        $this->serviceLoanPaymentService = $serviceLoanPaymentService; 
    }

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);
        
        // try {
            $file = $request->file('csv_file');
            $filename = 'import_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('temp/imports', $filename);  

            // Read file using Laravel-Excel
            $fullPath = Storage::path($path);
            $csvData = Excel::toArray(new BeneficiaryOrderImport, $fullPath); 
            $rows = $csvData[0];    
            
            if($request->service_type == 'loan'){ 
                $results = $this->processLoanService($rows,$request);
            }elseif($request->service_type == 'loan_payment'){
                $results = $this->processLoanPayment($rows,$request);
            }

            // Clean up temp file
            Storage::delete($path);

            return response()->json([
                'success' => true,
                'results' => $results
            ], 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Error processing import: ' . $e->getMessage(),
        //         'trace' => $e
        //     ], 500);
        // }
    }

    private function readCsvFile($filePath, $keyName = true)
    {
        $data = [];
        $file = Storage::path($filePath);

        if (($handle = fopen($file, "r")) !== false) {
            $headers = fgetcsv($handle);

            // Ensure headers are UTF-8
            $headers = array_map(fn($h) => mb_convert_encoding($h, 'UTF-8', 'auto'), $headers);

            while (($row = fgetcsv($handle)) !== false) {
                // Convert each value in the row to UTF-8
                $row = array_map(fn($v) => mb_convert_encoding($v, 'UTF-8', 'auto'), $row);

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

    private function processLoanPayment($csvData,$request)
    {
        $results = [
            'imported_loan_payment' => 0, 
            'updated_loan_payment' => 0,  
            'errors' => [],
            'failed_rows' => []
        ];

        // Process each row individually without transaction to allow partial success
        foreach ($csvData as $index => $row) {
            if($index == 0){
                continue;
            }
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header
            
            try {   
                
                $serviceLoanMember = ServiceLoanMember::where('handle', $row['handle_loan'])->first();
                
                if($serviceLoanMember){
                    $serviceLoanPayment = ServiceLoanPayment::where('handle', $row['handle_payment'])->first();

                    $serviceLoanPaymentData = [
                        'amount' => $row['amount'],
                        'payment_status' => 'paid',
                        'payment_method' => 'other',
                        'payment_reference_number' => $row['handle_payment'],
                        'paid_date' => $row['date'],
                        'handle' => $row['handle_payment'],
                    ];
                    if($serviceLoanPayment){
                        $serviceLoanPayment->update($serviceLoanPaymentData);
                        $results['updated_loan_payment']++;
                    }else{
                        $serviceLoanPaymentData['service_loan_id'] = $serviceLoanMember->service_loan_id;
                        $serviceLoanPayment = ServiceLoanPayment::create($serviceLoanPaymentData);
                        $results['imported_loan_payment']++; 
                    }

                    $this->serviceLoanPaymentService->acceptPayment($serviceLoanPayment);
                }else{
                    $results['errors'][] = "Row {$rowNumber}: Loan member not found";
                }

            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $results['errors'][] = "Row {$rowNumber}: {$errorMessage}";
                $results['failed_rows'][] = [
                    'row' => $rowNumber, 
                    'error' => $errorMessage,
                    'data' => $row ?? []
                ];
            }
        }

        return $results;
    }   

    private function processLoanService($csvData,$request)
    {
        $results = [
            'imported_beneficiary' => 0,
            'imported_beneficiary_order' => 0,
            'imported_service_loan' => 0,
            'imported_service_loan_member' => 0,
            'updated_beneficiary' => 0,
            'updated_beneficiary_order' => 0,
            'updated_service_loan' => 0,
            'updated_service_loan_member' => 0,
            'errors' => [],
            'failed_rows' => []
        ];

        // Process each row individually without transaction to allow partial success
        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header
            
            try {  
                $identityNum = $row['identity_num'];   
                $user = User::where('identity_num', $identityNum)->first(); 

                $userData = [
                    'name' => $row['name'], 
                    // 'phone' => $phone,  
                    'approved' => 1, 
                    'identity_num' => $identityNum,
                    'user_type' => 'beneficiary', 
                ];

                $beneficiaryData = [
                    'address' => $row['city'], 
                ];

                if ($user) { 
                    $user->update($userData);
                    $beneficiary = $user->beneficiary;

                    $beneficiary->update($beneficiaryData); 
                    $results['updated_beneficiary']++;
                } else {
                    $user = User::create($userData);
                    $beneficiaryData['user_id'] = $user->id;
                    $beneficiary = Beneficiary::create($beneficiaryData); 
                    $results['imported_beneficiary']++;
                }

                $service = Service::find($request->service_id);

                $beneficiaryOrder = BeneficiaryOrder::where('handle', $row['handle'])->first();
                $serviceLoan = null;
                $serviceLoanMember = null;

                if($service->key_name == 'group_loan'){
                    $serviceLoan = ServiceLoan::where('group_name', $row['group_name'])->first();
                    if($serviceLoan){
                        $beneficiaryOrder = $serviceLoan->beneficiary_order;
                        $serviceLoanMember = ServiceLoanMember::where('name', $row['name'])->where('identity_number', $row['identity_num'])->first();
                    }
                }


                $beneficiaryOrderData = [ 
                    'beneficiary_id' => $beneficiary->id,
                    'service_id' => $service->id,
                    'accept_status' => 'yes',
                    'title' => $row['project_short_description'],
                    'description' => '',
                    'service_type' => $service->type,
                    'specialist_id' => $row['specialist_id'],
                    'created_at' => Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
                ];
                $serviceLoanData = [ 
                    'status' => 'loan_paid',
                    'created_at' => Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
                ]; 
                $serviceLoanMemberData = [
                    'beneficiary_id' => $beneficiary->id,  
                    'status' => 'approved',
                    'handle' => $row['handle'],
                    'name' => $row['name'],
                    'identity_number' => $row['identity_num'],
                    'member_position' => 'member', 
                    'project_type' => $row['project_type'],  
                    'project_short_description' => $row['project_short_description'], 
                    'installment' => $row['installment'],
                    'months' => $row['months'], 
                    'amount' => $row['amount'],
                ];


                if($service->key_name == 'group_loan'){
                    $serviceLoanData['group_name'] = $row['group_name'];
                }else{
                    $beneficiaryOrderData['handle'] = $row['handle'];
                }

                if($request->service_type == 'loan'){ 
                    if($beneficiaryOrder){
                        $beneficiaryOrder->update($beneficiaryOrderData);
                        $results['updated_beneficiary_order']++;
                        $serviceLoan = $beneficiaryOrder->serviceLoan;

                    }else{
                        $beneficiaryOrder = BeneficiaryOrder::create($beneficiaryOrderData);
                        $results['imported_beneficiary_order']++;
                    }

                    if($serviceLoan){
                        $serviceLoan->update($serviceLoanData);
                        $results['updated_service_loan']++;
                    }else{
                        $serviceLoanData['beneficiary_order_id'] = $beneficiaryOrder->id;
                        $serviceLoan = ServiceLoan::create($serviceLoanData);
                        $results['imported_service_loan']++;
                    } 

                    if($serviceLoanMember){
                        $serviceLoanMember->update($serviceLoanMemberData);
                        $results['updated_service_loan_member']++;
                    }else{ 
                        $serviceLoanMemberData['service_loan_id'] = $serviceLoan->id;
                        $serviceLoanMember = ServiceLoanMember::create($serviceLoanMemberData);
                        $results['imported_service_loan_member']++;
                    }

                    $serviceLoan->amount = $serviceLoan->members->sum('amount');
                    $serviceLoan->installment = $serviceLoan->members->sum('installment');
                    $serviceLoan->months = $row['months'];
                    $serviceLoan->save();

                    $serviceLoan->installments()->delete();
                    $serviceLoan->addInstallments(Date::excelToDateTimeObject($row['installment_date'])->format('Y-m-d'));
                }

            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $results['errors'][] = "Row {$rowNumber}: {$errorMessage}";
                $results['failed_rows'][] = [
                    'row' => $rowNumber, 
                    'error' => $errorMessage,
                    'data' => $row ?? []
                ];
            }
        }

        return $results;
    }   
} 