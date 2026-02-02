<?php

namespace App\Http\Requests\Admin;

use App\Models\BeneficiaryOrderFollowup;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyBeneficiaryOrderFollowupRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('beneficiary_order_followup_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:beneficiary_order_followups,id',
        ];
    }
}
