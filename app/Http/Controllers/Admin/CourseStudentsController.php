<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MassDestroyCourseStudentRequest;
use App\Http\Requests\Admin\StoreCourseStudentRequest;
use App\Http\Requests\Admin\UpdateCourseStudentRequest;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;
use App\Models\Course;
use App\Models\CourseStudent;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CourseStudentsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('course_student_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = CourseStudent::with(['course', 'beneficiary', 'beneficiary_family'])->select(sprintf('%s.*', (new CourseStudent)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'course_student_show';
                $editGate      = 'course_student_edit';
                $deleteGate    = 'course_student_delete';
                $crudRoutePart = 'course-students';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('course_trainer', function ($row) {
                return $row->course ? $row->course->trainer : '';
            });

            $table->addColumn('beneficiary_dob', function ($row) {
                return $row->beneficiary ? $row->beneficiary->dob : '';
            });

            $table->addColumn('beneficiary_family_name', function ($row) {
                return $row->beneficiary_family ? $row->beneficiary_family->name : '';
            });

            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });
            $table->editColumn('certificate', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->certificate ? 'checked' : null) . '>';
            });
            $table->editColumn('transportation', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->transportation ? 'checked' : null) . '>';
            });
            $table->editColumn('prev_experience', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->prev_experience ? 'checked' : null) . '>';
            });
            $table->editColumn('prev_courses', function ($row) {
                return $row->prev_courses ? $row->prev_courses : '';
            });
            $table->editColumn('attend_same_course_before', function ($row) {
                return '<input type="checkbox" disabled ' . ($row->attend_same_course_before ? 'checked' : null) . '>';
            });

            $table->rawColumns(['actions', 'placeholder', 'course', 'beneficiary', 'beneficiary_family', 'certificate', 'transportation', 'prev_experience', 'attend_same_course_before']);

            return $table->make(true);
        }

        return view('admin.courseStudents.index');
    }

    public function create()
    {
        abort_if(Gate::denies('course_student_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $courses = Course::pluck('trainer', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiaries = Beneficiary::pluck('dob', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiary_families = BeneficiaryFamily::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.courseStudents.create', compact('beneficiaries', 'beneficiary_families', 'courses'));
    }

    public function store(StoreCourseStudentRequest $request)
    {
        $courseStudent = CourseStudent::create($request->all());

        return redirect()->route('admin.course-students.index');
    }

    public function edit(CourseStudent $courseStudent)
    {
        abort_if(Gate::denies('course_student_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $courses = Course::pluck('trainer', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiaries = Beneficiary::pluck('dob', 'id')->prepend(trans('global.pleaseSelect'), '');

        $beneficiary_families = BeneficiaryFamily::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $courseStudent->load('course', 'beneficiary', 'beneficiary_family');

        return view('admin.courseStudents.edit', compact('beneficiaries', 'beneficiary_families', 'courseStudent', 'courses'));
    }

    public function update(UpdateCourseStudentRequest $request, CourseStudent $courseStudent)
    {
        $courseStudent->update($request->all());

        return redirect()->route('admin.course-students.index');
    }

    public function show(CourseStudent $courseStudent)
    {
        abort_if(Gate::denies('course_student_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $courseStudent->load('course', 'beneficiary', 'beneficiary_family');

        return view('admin.courseStudents.show', compact('courseStudent'));
    }

    public function destroy(CourseStudent $courseStudent)
    {
        abort_if(Gate::denies('course_student_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $courseStudent->delete();

        return back();
    }

    public function massDestroy(MassDestroyCourseStudentRequest $request)
    {
        $courseStudents = CourseStudent::find(request('ids'));

        foreach ($courseStudents as $courseStudent) {
            $courseStudent->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
