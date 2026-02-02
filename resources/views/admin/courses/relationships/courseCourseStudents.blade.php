<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover w-100 datatable-courseCourseStudents">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.courseStudent.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.courseStudent.fields.beneficiary') }}
                        </th> 
                        <th>
                            {{ trans('cruds.courseStudent.fields.certificate') }}
                        </th>
                        <th>
                            {{ trans('cruds.courseStudent.fields.transportation') }}
                        </th>
                        <th>
                            {{ trans('cruds.courseStudent.fields.prev_experience') }}
                        </th>
                        <th>
                            {{ trans('cruds.courseStudent.fields.prev_courses') }}
                        </th> 
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courseStudents as $key => $courseStudent)
                        <tr data-entry-id="{{ $courseStudent->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $courseStudent->id ?? '' }}
                            </td>
                            <td>
                                {{ $courseStudent->beneficiary->user->name ?? '' }}
                            </td> 
                            <td>
                                <span style="display:none">{{ $courseStudent->certificate ?? '' }}</span>
                                <input type="checkbox" disabled="disabled"
                                    {{ $courseStudent->certificate ? 'checked' : '' }}>
                            </td>
                            <td>
                                <span style="display:none">{{ $courseStudent->transportation ?? '' }}</span>
                                <input type="checkbox" disabled="disabled"
                                    {{ $courseStudent->transportation ? 'checked' : '' }}>
                            </td>
                            <td>
                                <span style="display:none">{{ $courseStudent->prev_experience ?? '' }}</span>
                                <input type="checkbox" disabled="disabled"
                                    {{ $courseStudent->prev_experience ? 'checked' : '' }}>
                            </td>
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
                            <td>
                                @include('partials.datatablesActions', [ 
                                    'viewGate' => 'course_student_show',
                                    'editGate' => false,
                                    'deleteGate' => false,
                                    'crudRoutePart' => 'course-students',
                                    'row' => $courseStudent, 
                                ])
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons) 

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
            });
            let table = $('.datatable-courseCourseStudents:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
