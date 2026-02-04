<?php

namespace App\Http\Requests\Admin;

use App\Models\Donation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('donation_create');
    }

    public function rules()
    {
        return [
            'donator_id' => [
                'required',
                'integer',
                'exists:donators,id',
            ],
            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],
            'donation_type' => [
                'required',
                'in:money,items',
            ],
            'total_amount' => [
                'required_if:donation_type,money',
                'nullable',
                'numeric',
                'min:0',
            ],
            'donated_at' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'notes' => [
                'nullable',
            ],
            'items' => [
                'required_if:donation_type,items',
                'array',
            ],
            'items.*.item_name' => [
                'required_if:donation_type,items',
                'string',
                'max:' . config('panel.max_characters_short'),
            ],
            'items.*.quantity' => [
                'required_if:donation_type,items',
                'numeric',
                'min:0',
            ],
            'items.*.unit_price' => [
                'required_if:donation_type,items',
                'numeric',
                'min:0',
            ],
        ];
    }
}

