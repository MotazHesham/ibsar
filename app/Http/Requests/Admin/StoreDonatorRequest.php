<?php

namespace App\Http\Requests\Admin;

use App\Models\Donator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreDonatorRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('donator_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:' . config('panel.max_characters_short'),
                'required',
            ],
            'email' => [
                'nullable',
                'email',
                'max:' . config('panel.max_characters_short'),
            ],
            'phone' => [
                'nullable',
                'max:' . config('panel.max_characters_short'),
            ],
            'notes' => [
                'nullable',
            ],
        ];
    }
}

