<?php

namespace App\Http\Requests\Admin;

use App\Models\OutgoingLetter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyOutgoingLetterRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('outgoing_letter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:outgoing_letters,id',
        ];
    }
}
