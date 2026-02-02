<?php

namespace App\Http\Requests\Admin;

use App\Models\FrontLink;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFrontLinkRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('front_link_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'link' => [
                'string',
                'required',
            ],
            'position' => [
                'required',
            ],
        ];
    }
}
