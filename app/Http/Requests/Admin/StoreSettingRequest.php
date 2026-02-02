<?php

namespace App\Http\Requests\Admin;

use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSettingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('setting_create');
    }

    public function rules()
    {
        return [
            'key' => [
                'string',
                'required',
            ],
            'name' => [
                'string',
                'nullable',
            ], 
            'lang' => [
                'string',
                'nullable',
            ],
            'group_name' => [
                'string',
                'nullable',
            ],
            'order_level' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'grid_col' => [
                'string',
                'nullable',
            ],
        ];
    }
}
