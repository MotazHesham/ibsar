<?php

namespace App\Http\Requests\Admin;

use App\Models\RequiredDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyRequiredDocumentRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('required_document_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:required_documents,id',
        ];
    }
}
