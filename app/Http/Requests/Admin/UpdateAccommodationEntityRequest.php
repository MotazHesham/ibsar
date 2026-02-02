<?php

namespace App\Http\Requests\Admin;

use App\Models\AccommodationEntity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateAccommodationEntityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('accommodation_entity_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'type' => [
                'required',
                'in:' . implode(',', array_keys(AccommodationEntity::$TYPE_SELECT)),
            ],
        ];
    }
} 