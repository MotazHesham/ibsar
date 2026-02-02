<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('loan_edit');
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
