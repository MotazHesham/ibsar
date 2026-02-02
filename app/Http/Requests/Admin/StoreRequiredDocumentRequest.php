<?php

namespace App\Http\Requests\Admin;

use App\Models\RequiredDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreRequiredDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('required_document_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'marital_status_id' => [ 
                'nullable',
                'exists:marital_statuses,id',
            ],
        ];
    }
}
