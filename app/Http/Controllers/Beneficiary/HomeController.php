<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\ServiceLoan;
use App\Models\BeneficiaryOrder;
use App\Models\District;
use App\Models\Loan;
use App\Models\ServiceLoanMember;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    { 
        $beneficiary = auth()->user()->beneficiary;
        
        // Get pending loan information through beneficiary order
        $pendingLoan = null;
        $seriveloanMember = ServiceLoanMember::where('identity_number', auth()->user()->identity_num)
            ->where('status', 'pending')
            ->where('member_position', 'member')
            ->first(); 

        $districts = District::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), ''); 
        $loans = Loan::get();
        foreach ($loans as $loan) {
            $loan->name = 'قيمة القرض: ' . $loan->amount . ' - قيمة القسط: ' . $loan->installment . ' - عدد الشهور: ' . $loan->months;
            $loan->id = $loan->id;
        }
            
        if($seriveloanMember){
            $pendingLoan = $seriveloanMember->serviceLoan ?? null;
            $pendingLoan->order = $seriveloanMember->serviceLoan->beneficiary_order ?? null;
        }
        
        return view('beneficiary.home', compact('beneficiary', 'pendingLoan','seriveloanMember','districts','loans'));
    }
}
