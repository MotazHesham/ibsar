<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller; 
use App\Models\BeneficiaryFamily;
use Illuminate\Http\Request;

class GeneralController extends Controller
{ 
    public function getByBeneficiary(Request $request)
    {
        $beneficiaryFamilies = BeneficiaryFamily::where('beneficiary_id', $request->beneficiary_id)->get()->pluck('name', 'id');
        return response()->json($beneficiaryFamilies);
    }
}
