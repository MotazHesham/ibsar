<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateServiceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('service_edit');
    }

    public function rules()
    {
        return [ 
            'title' => [
                'string',
                'max:255',
                'required',
            ],
            'quantity' => [
                'numeric',
            ],
            'start_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'end_date' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ], 
        ];
    }
}
