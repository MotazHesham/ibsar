<?php

namespace App\Http\Requests\Admin;

use App\Models\FrontPartner;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateFrontPartnerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('front_partner_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'image' => [
                'required',
            ],
        ];
    }
}
