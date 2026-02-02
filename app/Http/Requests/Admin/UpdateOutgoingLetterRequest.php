<?php

namespace App\Http\Requests\Admin;

use App\Models\OutgoingLetter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateOutgoingLetterRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('outgoing_letter_edit');
    }

    public function rules()
    {
        return [
            'letter_number' => [
                'string',
                'nullable',
            ],
            'letter_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'delivered_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'recevier_id' => [
                'required',
                'integer',
            ],
            'subject' => [
                'string',
                'max:500',
                'required',
            ],
            'letter' => [
                'required',
            ],
            'priority' => [
                'required',
            ],
            'outgoing_type' => [
                'required',
            ],
            'attachments' => [
                'array',
            ],
        ];
    }
}
