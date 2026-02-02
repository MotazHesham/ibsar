<?php

namespace App\Http\Requests\Admin;

use App\Models\FamilyRelationship;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFamilyRelationshipRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('family_relationship_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:family_relationships,id',
        ];
    }
}
