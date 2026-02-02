<?php

namespace App\Http\Requests\Admin;

use App\Models\BeneficiaryOrderFollowup;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBeneficiaryOrderFollowupRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('beneficiary_order_followup_create');
    }

    public function rules()
    {
        return [
            'beneficiary_order_id' => [
                'required',
                'integer',
            ],
            'comment' => [
                'required',
            ],
            'attachments' => [
                'array',
            ],
            'date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
