<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreDonationAllocationRequest extends FormRequest
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
            'beneficiary_order_id' => [
                'required',
                'integer',
                'exists:beneficiary_orders,id',
            ],
            'allocated_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
        ];
    }
}

