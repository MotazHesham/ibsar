<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('loan_create');
    }

    public function rules()
    {
        return [
            'amount' => [ 
                'required',
            ],
        ];
    }
}
