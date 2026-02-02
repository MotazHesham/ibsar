<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_edit');
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
                'unique:users,email,' . request()->route('user')->id,
            ],
            'username' => [
                'string',
                'min:4',
                'max:255',
                'required',
                'unique:users,username,' . request()->route('user')->id,
            ],
            'phone' => [
                'string',
                'required',
                'unique:users,phone,' . request()->route('user')->id,
            ],
            'phone_2' => [
                'string',
                'nullable',
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
                'unique:users,identity_num,' . request()->route('user')->id,
            ],
        ];
    }
}
