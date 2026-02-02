<?php

namespace App\Http\Requests\Admin;

use App\Models\AccommodationType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyAccommodationTypeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('accommodation_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:accommodation_types,id',
        ];
    }
} 