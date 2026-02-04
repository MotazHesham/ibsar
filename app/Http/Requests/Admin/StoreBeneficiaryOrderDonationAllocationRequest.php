<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreBeneficiaryOrderDonationAllocationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('donation_allocation_create');
    }

    public function rules()
    {
        return [
            'donation_id' => [
                'required',
                'integer',
                'exists:donations,id',
            ],
            'allocated_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ];
    }
}

