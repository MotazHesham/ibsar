<?php

namespace App\Http\Requests\Admin;

use App\Models\FrontAchievement;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateFrontAchievementRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('front_achievement_edit');
    }

    public function rules()
    {
        return [ 
            'title' => [
                'string',
                'max:255',
                'required',
            ],
            'achievement' => [
                'string',
                'required',
            ],
        ];
    }
}
