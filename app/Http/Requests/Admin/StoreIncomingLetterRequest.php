<?php

namespace App\Http\Requests\Admin;

use App\Models\IncomingLetter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreIncomingLetterRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('incoming_letter_create');
    }

    public function rules()
    {
        return [ 
            'external_number' => [
                'string',
                'nullable',
                'unique:incoming_letters,external_number',
            ],
            'letter_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'received_date' => [
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
            'incoming_type' => [
                'required',
            ],
            'attachments' => [
                'array',
            ],
        ];
    }
}
