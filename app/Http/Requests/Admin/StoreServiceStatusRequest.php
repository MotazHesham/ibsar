<?php

namespace App\Http\Requests\Admin;

use App\Models\ServiceStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreServiceStatusRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('service_status_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'required',
            ],
            'badge_class' => [
                'required',
            ],
        ];
    }
}
