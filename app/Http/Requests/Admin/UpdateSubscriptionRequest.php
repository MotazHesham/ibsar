<?php

namespace App\Http\Requests\Admin;

use App\Models\Subscription;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('subscription_edit');
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
            ],
        ];
    }
}
