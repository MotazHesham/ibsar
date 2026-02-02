<?php

namespace App\Http\Requests\Admin;

use App\Models\BeneficiaryCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyBeneficiaryCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('beneficiary_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:beneficiary_categories,id',
        ];
    }
}

