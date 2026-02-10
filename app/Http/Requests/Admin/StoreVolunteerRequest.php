<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreVolunteerRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('volunteer_create');
    }

    public function rules()
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'identity_num'  => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email'],
            'phone_number'  => ['required', 'string', 'max:255'],
            'interest'      => ['nullable', 'string'],
            'initiative_name' => ['nullable', 'string'],
            'prev_experience' => ['nullable', 'string'],
            'photo'         => ['required'],
            'cv'            => ['nullable'],
        ];
    }
}
