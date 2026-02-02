{{-- Tab Navigation --}}
<ul class="nav nav-tabs" id="coursesFormTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="order-info-tab" data-bs-toggle="tab" data-bs-target="#order-info" type="button"
            role="tab" aria-controls="order-info" aria-selected="true">
            {{ trans('cruds.service.extra.order_info') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="additional-info-tab" data-bs-toggle="tab" data-bs-target="#additional-info"
            type="button" role="tab" aria-controls="additional-info" aria-selected="false">
            {{ trans('cruds.service.extra.additional_info') }}
        </button>
    </li>
</ul>
{{-- Tab Content --}}
<div class="tab-content" id="coursesFormTabsContent">
    <div class="tab-pane fade show active" id="order-info" role="tabpanel" aria-labelledby="order-info-tab">
        @include('partials.beneficiaryOrderForm.basic-data')
    </div>
    <div class="tab-pane fade" id="additional-info" role="tabpanel" aria-labelledby="additional-info-tab">

        <div class="row">
            @include('utilities.form.select', [
                'name' => 'course_id',
                'label' => 'cruds.beneficiaryOrder.fields.course',
                'isRequired' => true,
                'options' => $courses,
                'search' => false,
                'grid' => 'col-md-12',
            ])
            @include('utilities.form.radio', [
                'name' => 'certificate',
                'label' => 'cruds.courseStudent.extra.certificate',
                'isRequired' => true,
                'options' => [
                    1 => 'نعم',
                    0 => 'لا',
                ],
                'value' => 0,
                'grid' => 'col-md-3',
            ])
            @include('utilities.form.radio', [
                'name' => 'transportation',
                'label' => 'cruds.courseStudent.extra.transportation',
                'isRequired' => true,
                'options' => [
                    1 => 'نعم',
                    0 => 'لا',
                ],
                'value' => 0,
                'grid' => 'col-md-3',
            ])
            @include('utilities.form.radio', [
                'name' => 'prev_experience',
                'label' => 'cruds.courseStudent.extra.prev_experience',
                'isRequired' => true,
                'options' => [
                    1 => 'نعم',
                    0 => 'لا',
                ],
                'value' => 0,
                'grid' => 'col-md-3',
            ])
            @include('utilities.form.radio', [
                'name' => 'prev_courses',
                'label' => 'cruds.courseStudent.extra.prev_courses',
                'isRequired' => true,
                'options' => [
                    1 => 'نعم',
                    0 => 'لا',
                ],
                'value' => 0,
                'grid' => 'col-md-3',
            ])
            <div class="col-md-12" id="prev_courses_div" style="display: none;">
                <div class="row">

                    @include('utilities.form.text', [
                        'name' => 'prev_course_name',
                        'label' => 'cruds.courseStudent.extra.prev_course.name',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                    ])
                    @include('utilities.form.text', [
                        'name' => 'prev_course_trainer',
                        'label' => 'cruds.courseStudent.extra.prev_course.trainer',
                        'isRequired' => false,
                        'grid' => 'col-md-6',
                    ])
                </div>
            </div>
            @include('utilities.form.textarea', [
                'name' => 'note',
                'label' => 'cruds.courseStudent.extra.note',
                'isRequired' => true,
                'grid' => 'col-md-12',
                'editor' => false,
            ])
        </div>
        <div class="d-grid gap-2 col-6 mx-auto">
            <button class="btn btn-primary rounded-pill btn-wave" type="submit" id="submitBtn">
                {{ trans('global.save') }}
            </button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var prevCoursesRadios = document.querySelectorAll('input[name="prev_courses"]');
        var prevCoursesDiv = document.getElementById('prev_courses_div');

        if (prevCoursesRadios && prevCoursesDiv) {
            prevCoursesRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (this.value == 1) {
                        prevCoursesDiv.style.display = 'block';
                    } else {
                        prevCoursesDiv.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
