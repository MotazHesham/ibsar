<?php

namespace App\Http\Requests\Admin;

use App\Models\Region;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateRegionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('region_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'cities.*' => [
                'integer',
            ],
            'cities' => [
                'array',
            ],
        ];
    }
}
