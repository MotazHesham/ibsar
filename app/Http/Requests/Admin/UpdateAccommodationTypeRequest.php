<?php

namespace App\Http\Requests\Admin;

use App\Models\AccommodationType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAccommodationTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('accommodation_type_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
} 