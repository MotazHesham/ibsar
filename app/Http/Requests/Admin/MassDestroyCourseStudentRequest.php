<?php

namespace App\Http\Requests\Admin;

use App\Models\CourseStudent;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyCourseStudentRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('course_student_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:course_students,id',
        ];
    }
}
