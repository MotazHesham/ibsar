<?php

namespace App\Http\Requests\Admin;

use App\Models\Slider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSliderRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('slider_edit');
    }

    public function rules()
    {
        return [
            'image' => [
                'required',
            ],
            'title' => [
                'string',
                'nullable',
            ],
            'sub_title' => [
                'string',
                'nullable',
            ],
            'button_name' => [
                'string',
                'max:255',
                'nullable',
            ],
            'button_link' => [
                'string',
                'nullable',
            ],
        ];
    }
}
