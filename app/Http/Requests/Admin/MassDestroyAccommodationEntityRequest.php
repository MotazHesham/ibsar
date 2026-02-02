<?php

namespace App\Http\Requests\Admin;

use App\Models\AccommodationEntity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAccommodationEntityRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('accommodation_entity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:accommodation_entities,id',
        ];
    }
} 