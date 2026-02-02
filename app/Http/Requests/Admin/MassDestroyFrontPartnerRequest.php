<?php

namespace App\Http\Requests\Admin;

use App\Models\FrontPartner;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFrontPartnerRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('front_partner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:front_partners,id',
        ];
    }
}
