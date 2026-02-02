@extends('layouts.master')
@section('content')

<div class="card"> 

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.course-students.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.id') }}
                        </th>
                        <td>
                            {{ $courseStudent->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.course') }}
                        </th>
                        <td>
                            {{ $courseStudent->course->trainer ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.beneficiary') }}
                        </th>
                        <td>
                            {{ $courseStudent->beneficiary->user->name ?? '' }}
                        </td>
                    </tr> 
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.certificate') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $courseStudent->certificate ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.transportation') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $courseStudent->transportation ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.prev_experience') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $courseStudent->prev_experience ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.prev_courses') }}
                        </th>
                        <td>
                            @php
                                $prev_courses = json_decode($courseStudent->prev_courses);
                            @endphp
                            @if($prev_courses)
                                <ul> 
                                    <li><span class="badge bg-warning-transparent">{{ trans('cruds.courseStudent.extra.prev_course.name') }}</span>{{ $prev_courses->name }}</li> 
                                    <li><span class="badge bg-warning-transparent">{{ trans('cruds.courseStudent.extra.prev_course.trainer') }}</span>{{ $prev_courses->trainer }}</li> 
                                </ul>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.fields.attend_same_course_before') }}
                        </th>
                        <td>
                            <input type="checkbox" disabled="disabled" {{ $courseStudent->attend_same_course_before ? 'checked' : '' }}>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.extra.note') }}
                        </th>
                        <td>
                            {{ $courseStudent->note }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.courseStudent.extra.attendance') }}
                        </th>
                        <td>
                            @foreach ($courseStudent->attendance as $raw)
                                {{ $raw->date }}
                                <br>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.course-students.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection