<?php

namespace App\Http\Requests\Admin;

use App\Models\Building;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreBuildingRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('building_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
            ],
            'number_of_floors' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'number_of_apartments' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'address' => [
                'required',
            ],
            'latitude' => [
                'string',
                'nullable',
            ],
            'longitude' => [
                'string',
                'nullable',
            ],
            'apartment_specifications' => [
                'required',
            ],
        ];
    }
}
