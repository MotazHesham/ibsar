<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Beneficiary\RegisterRequest;  
use App\Services\BeneficiaryService; 
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected BeneficiaryService $beneficiaryService
    ) {}
    
    public function register(RegisterRequest $request)
    { 
        $beneficiary = $this->beneficiaryService->createBeneficiary($request);

        Auth::loginUsingId($beneficiary->user_id);

        return redirect()->route('beneficiary.home');
    }
}
