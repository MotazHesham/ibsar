<?php

namespace App\Http\Requests\Admin;

use App\Models\FamilyRelationship;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFamilyRelationshipRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('family_relationship_create');
    }

    public function rules()
    {
        return [
            'gender' => [
                'required',
            ],
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
