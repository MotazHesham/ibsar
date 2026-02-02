<?php

namespace App\Http\Requests\Admin;

use App\Models\CourseStudent;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCourseStudentRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('course_student_edit');
    }

    public function rules()
    {
        return [
            'course_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
