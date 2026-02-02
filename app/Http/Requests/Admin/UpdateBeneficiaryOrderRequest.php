<?php

namespace App\Http\Requests\Admin;

use App\Models\BeneficiaryOrder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBeneficiaryOrderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('beneficiary_order_edit');
    }

    public function rules()
    {
        $rules = [
            'beneficiary_id' => [
                'required',
                'integer',
            ],
            'description' => [
                'required',
            ],
            'status_id' => [
                'required',
                'integer',
            ],
            'done' => [
                'required',
            ],
            'specialist_id' => [
                'required',
                'integer',
            ],
        ];
        
        // Add validation for contacts field if it exists
        if ($this->has('contacts') && is_array($this->contacts)) {
            $rules['contacts'] = 'array';
            $rules['contacts.*.name'] = 'required|string|max:255';
            $rules['contacts.*.family_relationship_id'] = 'nullable|integer|exists:family_relationships,id';
            $rules['contacts.*.phone'] = 'required|string|max:20';
            $rules['contacts.*.identity_num'] = 'required|string|max:20';
            $rules['contacts.*.address'] = 'nullable|string|max:500';
        }
        
        return $rules;
    }
}
