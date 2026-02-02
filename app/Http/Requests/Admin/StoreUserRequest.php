<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
            ],
            'email' => [
                'required',
                'unique:users',
            ],
            'username' => [
                'string',
                'min:4',
                'max:255',
                'required',
                'unique:users',
            ],
            'phone' => [
                'string',
                'required',
                'unique:users',
            ],
            'phone_2' => [
                'string',
                'nullable',
            ],
            'password' => [
                'required',
            ],
            'roles.*' => [
                'integer',
            ],
            'roles' => [
                'required',
                'array',
            ],
            'identity_num' => [
                'string',
                'required',
                'unique:users',
            ],
        ];
    }
}
