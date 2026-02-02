<?php

namespace App\Http\Requests\Admin;

use App\Models\BeneficiaryCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBeneficiaryCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('beneficiary_category_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}

