<?php

namespace App\Http\Requests\Admin;

use App\Models\Archive;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateArchiveRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('archive_edit');
    }

    public function rules()
    {
        return [
            'archived_at' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'archived_by_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
