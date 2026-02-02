<?php

namespace App\Http\Requests\Admin;

use App\Models\StorageLocation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateStorageLocationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('storage_location_edit');
    }

    public function rules()
    {
        return [
            'code' => [
                'string',
                'max:255',
                'required',
                'unique:storage_locations,code,' . request()->route('storage_location')->id,
            ],
            'name' => [
                'string',
                'max:255',
                'required',
            ],
        ];
    }
}
