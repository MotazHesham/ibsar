<?php

namespace App\Http\Requests\Admin;

use App\Models\BeneficiaryFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBeneficiaryFileRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('beneficiary_file_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'nullable',
            ],
        ];
    }
}
