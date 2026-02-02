<?php

namespace App\Http\Requests\Admin;

use App\Models\StorageLocation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyStorageLocationRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('storage_location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:storage_locations,id',
        ];
    }
}
