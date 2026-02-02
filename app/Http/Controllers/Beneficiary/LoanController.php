<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryOrder;
use App\Models\Loan;
use App\Models\ServiceLoan;
use App\Models\ServiceLoanMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Beneficiary\UpdateLoanRequest;

class LoanController extends Controller
{
    public function update(UpdateLoanRequest $request)
    {
        $beneficiary = Auth::user()->beneficiary;
        
        $seriveloanMember = ServiceLoanMember::find($request->id);

        $serviceLoan = ServiceLoan::find($seriveloanMember->service_loan_id);
            
        if (!$seriveloanMember) {
            return redirect()->back()->with('error', trans('cruds.serviceLoan.fields.order_not_found'));
        }

        $loan = Loan::find($request->loan_id);

        $seriveloanMember->update([
            'status' => $request->status, 
        ]);
        
        if($request->status == 'approved'){
            $seriveloanMember->update([
                'beneficiary_id' => $beneficiary->id,
                'loan_id' => $request->loan_id,
                'service_loan_id' => $serviceLoan->id, 
                'beneficiary_id' => $beneficiary->id,   
                'project_type' => $request->project_type,
                'project_location' => $request->project_location,
                'district_id' => $request->district_id,
                'street' => $request->street,
                'project_start_date' => $request->project_start_date,
                'project_years_of_experience' => $request->project_years_of_experience,
                'project_short_description' => $request->project_short_description,
                'project_financial_source' => $request->project_financial_source,
                'purpose_of_loan' => $request->purpose_of_loan,
                'has_previous_loan' => $request->has_previous_loan,
                'previous_loan_number' => $request->previous_loan_number,
                'amount' => $loan->amount,
                'installment' => $loan->installment,
                'months' => $loan->months,
                'loan_id' => $request->loan_id,
            ]);

            $serviceLoan->amount += $loan->amount;
            $serviceLoan->installment += $loan->installment; 
            $serviceLoan->save();
        }
        
        return redirect()->route('beneficiary.home')->with('success', trans('cruds.serviceLoan.fields.loan_accepted'));
    } 
}
