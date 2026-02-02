<?php

namespace App\Http\Requests\Admin;

use App\Models\FrontReview;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFrontReviewRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('front_review_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'max:255',
                'nullable',
            ],
            'review' => [
                'string',
                'nullable',
            ],
            'rate' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
