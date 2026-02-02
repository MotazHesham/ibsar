@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.course.title'),
                'url' => route('admin.courses.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.course.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header p-3"> 
                    <a class="btn btn-success" href="{{ route('admin.courses.qr-attendance',encrypt($course->id))}}">
                        {{ trans('cruds.course.extra.qr_attendance') }}
                    </a>
                    <a class="btn btn-info" href="{{ route('admin.courses.qr-certificate',encrypt($course->id))}}">
                        {{ trans('cruds.course.extra.qr_certificate') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="form-group">
                            <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.courses.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.id') }}
                                    </th>
                                    <td>
                                        {{ $course->id }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.title') }}
                                    </th>
                                    <td>
                                        {{ $course->title }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.short_description') }}
                                    </th>
                                    <td>
                                        {{ $course->short_description }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.description') }}
                                    </th>
                                    <td>
                                        {!! $course->description !!}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.attend_type') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Course::ATTEND_TYPE_SELECT[$course->attend_type] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.certificate') }}
                                    </th>
                                    <td>
                                        {{ App\Models\Course::CERTIFICATE_SELECT[$course->certificate] ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.trainer') }}
                                    </th>
                                    <td>
                                        {{ $course->trainer }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.start_at') }}
                                    </th>
                                    <td>
                                        {{ $course->start_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.end_at') }}
                                    </th>
                                    <td>
                                        {{ $course->end_at }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.published') }}
                                    </th>
                                    <td>
                                        <input type="checkbox" disabled="disabled"
                                            {{ $course->published ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ trans('cruds.course.fields.photo') }}
                                    </th>
                                    <td>
                                        @if ($course->photo)
                                            <a href="{{ $course->photo->getUrl() }}" target="_blank"
                                                style="display: inline-block">
                                                <img src="{{ $course->photo->getUrl('thumb') }}">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <a class="btn btn-light mt-3 mb-3" href="{{ route('admin.courses.index') }}">
                                {{ trans('global.back_to_list') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card"> 
                <ul class="nav nav-tabs" role="tablist" id="relationship-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#course_course_students" role="tab" data-toggle="tab">
                            {{ trans('cruds.courseStudent.title_singular') }}
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel" id="course_course_students">
                        @includeIf('admin.courses.relationships.courseCourseStudents', [
                            'courseStudents' => $course->courseCourseStudents,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
