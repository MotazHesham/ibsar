<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'identity_num'    => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email'],
            'phone_number'    => ['required', 'string', 'max:255'],
            'interest'        => ['nullable', 'string'],
            'initiative_name' => ['nullable', 'string'],
            'prev_experience' => ['nullable', 'string'],
            'photo'           => ['required', 'image', 'max:2048'],
            'cv'              => ['nullable', 'file', 'max:2048'],
        ];
    }
}
