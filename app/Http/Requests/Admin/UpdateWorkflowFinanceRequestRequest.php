<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateWorkflowFinanceRequestRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('workflow_finance_request_edit');
    }

    public function rules()
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'journal_reference' => ['nullable', 'string', 'max:255'],
            'action' => ['required', Rule::in(['save', 'post'])],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => 'التكلفة',
            'journal_reference' => 'مرجع القيد',
            'notes' => 'ملاحظات',
        ];
    }
}
