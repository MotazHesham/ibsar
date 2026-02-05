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
        $rules = [
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

        // Only validate item fields if the donation type is "items"
        $donationId = $this->input('donation_id');
        if ($donationId) {
            $donation = \App\Models\Donation::find($donationId);
            if ($donation && $donation->donation_type === \App\Models\Donation::TYPE_ITEMS) {
                // For items donations, validate item selection and quantity
                $rules['allocation_item'] = [
                    'required',
                    'integer',
                    'min:0',
                ];
                $rules['item_quantity'] = [
                    'required',
                    'numeric',
                    'min:0.01',
                ];
            }
        }

        return $rules;
    }
}

