<?php

namespace App\Http\Requests\Admin;

use App\Models\MaritalStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMaritalStatusRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('marital_status_edit');
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
