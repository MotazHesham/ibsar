<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseAttendance;
use App\Models\CourseStudent;
use App\Models\User;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    public function courseAttendance($id)
    {  
        $course = Course::find(decrypt($id)); 
        return view('frontend.courses.course-attendance', compact('course'));
    }

    public function courseCertificate($id)
    {
        $course = Course::find(decrypt($id));
        return view('frontend.courses.course-certificate', compact('course'));
    }

    public function requestCertificate(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'identity_number' => 'required|string|max:255|exists:users,identity_number',
        ]);

        $user = User::where('identity_number', $request->identity_number)->first();
        $beneficiary = $user->beneficiary ?? null;

        if (!$user || !$beneficiary) {
            session()->flash('errorMessage', trans('frontend.course_attendance.user_not_found'));
            return redirect()->back();
        }

        $courseStudent = CourseStudent::where('course_id', $request->course_id)->where('beneficiary_id', $beneficiary->id)->first();

        if (!$courseStudent) {
            session()->flash('errorMessage', trans('frontend.course_attendance.not_registered'));
            return redirect()->back();
        }
        
        session()->flash('successMessage', trans('frontend.course_certificate.success'));
        return redirect()->back();
    }

    public function checkAttendance(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'identity_number' => 'required|string|max:255',
        ]);

        $user = User::where('identity_num', $request->identity_number)->first(); 
        $beneficiary = $user->beneficiary ?? null;
        
        if (!$user || !$beneficiary) {
            session()->flash('errorMessage', trans('frontend.course_attendance.user_not_found'));
            return redirect()->back();
        }

        $courseStudent = CourseStudent::where('course_id', $request->course_id)->where('beneficiary_id', $beneficiary->id)->first();

        if (!$courseStudent) {
            session()->flash('errorMessage', trans('frontend.course_attendance.not_registered'));
            return redirect()->back();
        }

        if(CourseAttendance::where('course_id', $request->course_id)->where('course_student_id', $courseStudent->id)->where('date', date('Y-m-d'))->exists()){
            session()->flash('errorMessage', trans('frontend.course_attendance.already_attended'));
            return redirect()->back();
        }
        
        CourseAttendance::create([
            'course_id' => $request->course_id,
            'course_student_id' => $courseStudent->id, 
            'date' => now(),
        ]);

        session()->flash('successMessage', trans('frontend.course_attendance.success'));
        return redirect()->back();
    }
}
