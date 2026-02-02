<?php

namespace App\Http\Requests\Admin;

use App\Models\FrontLink;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFrontLinkRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('front_link_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:front_links,id',
        ];
    }
}
