<form method="POST" action="{{ route('admin.beneficiary-families.store') }}" 
    enctype="multipart/form-data" onsubmit="modalAjaxSubmit(event)">
    @csrf
    <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->id }}">
    <div class="modal-header">
        <h5 class="modal-title" id="addFamilyModalLabel">
            {{ trans('global.add') }} {{ trans('cruds.beneficiaryFamily.title_singular') }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">

        <div class="row gy-2">

            @include('utilities.form.photo-ajax', [
                'name' => 'photo',
                'id' => 'familyPhoto',
                'url' => route('admin.beneficiary-families.storeMedia'),
                'label' => 'cruds.beneficiaryFamily.fields.photo',
                'isRequired' => false, 
            ])
            @include('utilities.form.text', [
                'name' => 'identity_num',
                'label' => 'cruds.beneficiaryFamily.fields.identity_num',
                'isRequired' => true,
                'grid' => 'col-md-6', 
            ])
            @include('utilities.form.text', [
                'name' => 'name',
                'label' => 'cruds.beneficiaryFamily.fields.name',
                'isRequired' => true,
                'grid' => 'col-md-6', 
            ])
            @include('utilities.form.select', [
                'name' => 'educational_qualification_id',
                'label' => 'cruds.beneficiaryFamily.fields.educational_qualification',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => $educational_qualifications, 
            ])
            @include('utilities.form.select', [
                'name' => 'gender',
                'label' => 'cruds.beneficiaryFamily.fields.gender',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => ['' => trans('global.pleaseSelect')] + App\Models\BeneficiaryFamily::GENDER_SELECT, 
            ])
            @include('utilities.form.date-ajax', [
                'name' => 'dob',
                'id' => 'dobFamily',
                'label' => 'cruds.beneficiaryFamily.fields.dob',
                'isRequired' => true,
                'hijri' => true,
                'grid' => 'col-md-6', 
            ])
            @include('utilities.form.text', [
                'name' => 'phone',
                'label' => 'cruds.beneficiaryFamily.fields.phone',
                'isRequired' => true,
                'grid' => 'col-md-6', 
            ])
            @include('utilities.form.text', [
                'name' => 'email',
                'label' => 'cruds.beneficiaryFamily.fields.email',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'type' => 'email', 
            ])
            @include('utilities.form.select-ajax', [
                'name' => 'family_relationship_id',
                'label' => 'cruds.beneficiaryFamily.fields.family_relationship',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => $family_relationships, 
            ])
            @include('utilities.form.select', [
                'name' => 'marital_status_id',
                'label' => 'cruds.beneficiaryFamily.fields.marital_status',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => $marital_statuses, 
            ]) 
            @include('utilities.form.select', [
                'name' => 'can_work',
                'label' => 'cruds.beneficiaryFamily.fields.can_work',
                'isRequired' => true,
                'grid' => 'col-md-6',
                'options' => ['' => trans('global.pleaseSelect')] + App\Models\BeneficiaryFamily::CAN_WORK_SELECT, 
            ])
            <div class="col-md-12">
                <div class="row">

                    @include('utilities.form.select', [
                        'name' => 'has_health_condition',
                        'id' => 'modal_has_health_condition',
                        'label' => 'cruds.beneficiaryFamily.fields.has_health_condition',
                        'isRequired' => true,
                        'options' => [
                            '' => trans('global.pleaseSelect'),
                            '1' => trans('global.yes'),
                            '0' => trans('global.no'),
                        ],
                        'grid' => 'col', 
                    ])
                    <div id="modal_health_condition_wrapper" style="display: none;" class="col">
                        @include('utilities.form.select', [
                            'name' => 'health_condition_id',
                            'id' => 'modal_health_condition_id',
                            'label' => 'cruds.beneficiaryFamily.fields.health_condition',
                            'isRequired' => false,
                            'options' => $health_conditions->toArray(),
                            'grid' => '', 
                        ])
                    </div>
                    <div id="modal_custom_health_condition_wrapper" style="display: none;" class="col">
                        @include('utilities.form.text', [
                            'name' => 'custom_health_condition',
                            'id' => 'modal_custom_health_condition',
                            'label' => 'cruds.beneficiaryFamily.fields.custom_health_condition',
                            'isRequired' => false,
                            'grid' => '', 
                        ])
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row">

                    @include('utilities.form.select', [
                        'name' => 'has_disability',
                        'id' => 'modal_has_disability',
                        'label' => 'cruds.beneficiaryFamily.fields.has_disability',
                        'isRequired' => true,
                        'options' => [
                            '' => trans('global.pleaseSelect'),
                            '1' => trans('global.yes'),
                            '0' => trans('global.no'),
                        ],
                        'grid' => 'col', 
                    ])
                    <div id="modal_disability_type_wrapper" style="display: none;" class="col">
                        @include('utilities.form.select', [
                            'name' => 'disability_type_id',
                            'id' => 'modal_disability_type_id',
                            'label' => 'cruds.beneficiaryFamily.fields.disability_type',
                            'isRequired' => false,
                            'options' => $disability_types->toArray(),
                            'grid' => '', 
                        ])
                    </div>
                    <div id="modal_custom_disability_type_wrapper" style="display: none;" class="col">
                        @include('utilities.form.text', [
                            'name' => 'custom_disability_type',
                            'id' => 'modal_custom_disability_type',
                            'label' => 'cruds.beneficiaryFamily.fields.custom_disability_type',
                            'isRequired' => false,
                            'grid' => '', 
                        ])
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            {{ trans('global.cancel') }}
        </button>
        <button type="submit" class="btn btn-primary">
            {{ trans('global.save') }}
        </button>
    </div>

</form>

<script>
    /* Health Condition Toggle */
    var modalHasHealthConditionSelect = document.getElementById('modal_has_health_condition');
    var modalHealthConditionWrapper = document.getElementById('modal_health_condition_wrapper');
    var modalHealthConditionSelect = document.getElementById('modal_health_condition_id');
    var modalCustomHealthConditionWrapper = document.getElementById('modal_custom_health_condition_wrapper');

    if (modalHasHealthConditionSelect && modalHealthConditionWrapper) {
        modalHasHealthConditionSelect.addEventListener('change', function() {
            if (this.value === '1') {
                modalHealthConditionWrapper.style.display = 'block';
            } else {
                modalHealthConditionWrapper.style.display = 'none';
                modalCustomHealthConditionWrapper.style.display = 'none';
                document.getElementById('modal_health_condition_id').value = '';
                document.getElementById('modal_custom_health_condition').value = '';
            }
        });
    }

    if (modalHealthConditionSelect && modalCustomHealthConditionWrapper) {
        modalHealthConditionSelect.addEventListener('change', function() { 
            var selectedText = this.options[this.selectedIndex].text.trim().toLowerCase();
            if (selectedText == 'other' || selectedText == 'أخرى') {
                modalCustomHealthConditionWrapper.style.display = 'block';
            } else {
                modalCustomHealthConditionWrapper.style.display = 'none';
                document.getElementById('modal_custom_health_condition').value = '';
            }
        });
    }
    /* Health Condition Toggle */

    /* Disability Type Toggle */
    var modalHasDisabilitySelect = document.getElementById('modal_has_disability');
    var modalDisabilityTypeWrapper = document.getElementById('modal_disability_type_wrapper');
    var modalDisabilityTypeSelect = document.getElementById('modal_disability_type_id');
    var modalCustomDisabilityTypeWrapper = document.getElementById('modal_custom_disability_type_wrapper');

    if (modalHasDisabilitySelect && modalDisabilityTypeWrapper) {
        modalHasDisabilitySelect.addEventListener('change', function() {
            if (this.value === '1') {
                modalDisabilityTypeWrapper.style.display = 'block';
            } else {
                modalDisabilityTypeWrapper.style.display = 'none';
                modalCustomDisabilityTypeWrapper.style.display = 'none';
                document.getElementById('modal_disability_type_id').value = '';
                document.getElementById('modal_custom_disability_type').value = '';
            }
        });
    }

    if (modalDisabilityTypeSelect && modalCustomDisabilityTypeWrapper) {
        modalDisabilityTypeSelect.addEventListener('change', function() {
            var selectedText = this.options[this.selectedIndex].text.trim().toLowerCase();
            if (selectedText == 'other' || selectedText == 'أخرى') {
                modalCustomDisabilityTypeWrapper.style.display = 'block';
            } else {
                modalCustomDisabilityTypeWrapper.style.display = 'none';
                document.getElementById('modal_custom_disability_type').value = '';
            }
        });
    }
    /* Disability Type Toggle */
</script>