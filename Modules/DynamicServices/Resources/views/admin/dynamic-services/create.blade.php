@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.dynamicServicesManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.dynamicService.title'),
                'url' => route('admin.dynamic-services.index'),
            ],
            ['title' => trans('global.create') . ' ' . trans('cruds.dynamicService.title_singular'), 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <form method="POST" action="{{ route('admin.dynamic-services.store') }}" enctype="multipart/form-data" id="dynamicServiceForm">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-3">
                        <h6 class="card-title">
                            {{ trans('global.create') }} {{ trans('cruds.dynamicService.title_singular') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @include('utilities.form.text', [
                                'name' => 'title',
                                'label' => 'cruds.dynamicService.fields.title',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                            ])
                            @include('utilities.form.text', [
                                'name' => 'slug',
                                'label' => 'cruds.dynamicService.fields.slug',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'attributes' => 'onchange="generateSlug()"',
                            ])
                            @include('utilities.form.textarea', [
                                'name' => 'description',
                                'label' => 'cruds.dynamicService.fields.description',
                                'isRequired' => false,
                                'grid' => 'col-md-12',
                                'editor' => false,
                            ])
                            @include('utilities.form.select', [
                                'name' => 'status',
                                'label' => 'cruds.dynamicService.fields.status',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'options' => [
                                    'active' => trans('global.active'),
                                    'inactive' => trans('global.inactive'),
                                ],
                                'value' => 'active',
                            ])
                            @include('utilities.form.select', [
                                'name' => 'category',
                                'label' => 'cruds.dynamicService.fields.category',
                                'isRequired' => true,
                                'grid' => 'col-md-6',
                                'options' => [
                                    'training' => 'التدريب والتأهيل',
                                    'assistance' => 'مساعدات',
                                    'social_programs' => 'برامج اجتماعية', 
                                ],
                                'attributes' => 'onchange=toggleTrainingServiceType()',
                            ])
                            <div class="col-md-6 mb-3" id="training-service-type-container" style="display: none;">
                                @include('utilities.form.radio', [
                                    'name' => 'service_type',
                                    'label' => 'نوع الخدمة',
                                    'isRequired' => false,
                                    'grid' => 'col-md-12',
                                    'options' => [
                                        'individual' => 'فردي',
                                        'group' => 'جماعي',
                                    ],
                                    'value' => old('service_type', 'individual'),
                                    'helperBlock' => 'يرجى اختيار نوع الخدمة',
                                ])
                            </div>
                            @include('utilities.form.dropzone', [
                                'name' => 'icon',
                                'id' => 'icon',
                                'label' => 'cruds.dynamicService.fields.icon',
                                'isRequired' => false,
                                'grid' => 'col-md-6',
                                'url' => route('admin.dynamic-services.storeMedia'),
                            ])
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header p-3">
                        <h6 class="card-title">
                            {{ trans('cruds.dynamicService.fields.form_builder') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addFormField()">
                                <i class="fas fa-plus"></i> {{ trans('global.add_field') }}
                            </button>
                        </div>
                        <div id="form-fields-container">
                            <!-- Form fields will be added here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <button class="btn btn-primary-light rounded-pill btn-wave" type="submit">
                {{ trans('global.save') }}
            </button>
            <a href="{{ route('admin.dynamic-services.index') }}" class="btn btn-secondary-light rounded-pill btn-wave">
                {{ trans('global.cancel') }}
            </a>
        </div>
    </form>

    <!-- Field Template (Hidden) -->
    <template id="field-template">
        <div class="form-field-item border rounded p-3 mb-3" data-field-id="">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0">{{ trans('cruds.dynamicService.fields.field') }} #<span class="field-number"></span></h6>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeFormField(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">{{ trans('cruds.dynamicService.fields.field_label') }} *</label>
                    <input type="text" class="form-control field-label" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ trans('cruds.dynamicService.fields.field_type') }} *</label>
                    <select class="form-select field-type" required onchange="toggleFieldOptions(this)">
                        <option value="text">{{ trans('cruds.dynamicService.fields.field_types.text') }}</option>
                        <option value="textarea">{{ trans('cruds.dynamicService.fields.field_types.textarea') }}</option>
                        <option value="select">{{ trans('cruds.dynamicService.fields.field_types.select') }}</option>
                        <option value="radio">{{ trans('cruds.dynamicService.fields.field_types.radio') }}</option>
                        <option value="checkbox">{{ trans('cruds.dynamicService.fields.field_types.checkbox') }}</option>
                        <option value="date">{{ trans('cruds.dynamicService.fields.field_types.date') }}</option>
                        <option value="time">{{ trans('cruds.dynamicService.fields.field_types.time') }}</option>
                        <option value="number">{{ trans('cruds.dynamicService.fields.field_types.number') }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ trans('cruds.dynamicService.fields.field_grid') }}</label>
                    <select class="form-select field-grid">
                        <option value="col-md-6">col-md-6</option>
                        <option value="col-md-4">col-md-4</option>
                        <option value="col-md-3">col-md-3</option>
                        <option value="col-md-12">col-md-12</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input class="form-check-input field-required" type="checkbox" id="required-">
                        <label class="form-check-label" for="required-">
                            {{ trans('cruds.dynamicService.fields.field_required') }}
                        </label>
                    </div>
                </div>
                <div class="col-12 field-options-container" style="display: none;">
                    <label class="form-label">{{ trans('cruds.dynamicService.fields.field_options') }}</label>
                    <textarea class="form-control field-options" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                    <small class="form-text text-muted">{{ trans('cruds.dynamicService.fields.field_options_help') }}</small>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('scripts')
    @parent
    <script>
        let fieldCounter = 0;

        function addFormField() {
            fieldCounter++;
            const template = document.getElementById('field-template');
            const container = document.getElementById('form-fields-container');
            const clone = template.content.cloneNode(true);
            
            // Update field ID and number
            const fieldItem = clone.querySelector('.form-field-item');
            fieldItem.setAttribute('data-field-id', fieldCounter);
            fieldItem.querySelector('.field-number').textContent = fieldCounter;
            
            // Update required checkbox ID
            const requiredCheckbox = clone.querySelector('.field-required');
            requiredCheckbox.id = 'required-' + fieldCounter;
            
            container.appendChild(clone);
        }

        function removeFormField(button) {
            button.closest('.form-field-item').remove();
        }

        function toggleFieldOptions(select) {
            const fieldItem = select.closest('.form-field-item');
            const optionsContainer = fieldItem.querySelector('.field-options-container');
            const fieldType = select.value;
            
            if (['select', 'radio', 'checkbox'].includes(fieldType)) {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }

        function generateSlug() {
            const title = document.querySelector('input[name="title"]').value;
            const slug = title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            document.querySelector('input[name="slug"]').value = slug;
        }

        function toggleTrainingServiceType() {
            const categorySelect = document.querySelector('select[name="category"]');
            const trainingServiceTypeContainer = document.getElementById('training-service-type-container');
            
            if (categorySelect && trainingServiceTypeContainer) {
                if (categorySelect.value === 'training') {
                    trainingServiceTypeContainer.style.display = 'block';
                } else {
                    trainingServiceTypeContainer.style.display = 'none';
                    // Reset the value when hidden
                    const radioInputs = trainingServiceTypeContainer.querySelectorAll('input[type="radio"]');
                    radioInputs.forEach(input => input.checked = false);
                }
            }
        }

        // Check on page load if category is already set
        document.addEventListener('DOMContentLoaded', function() {
            toggleTrainingServiceType();
        });

        // Form submission
        document.getElementById('dynamicServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formFields = [];
            document.querySelectorAll('.form-field-item').forEach((item, index) => {
                const fieldData = {
                    id: index + 1,
                    label: item.querySelector('.field-label').value,
                    type: item.querySelector('.field-type').value,
                    required: item.querySelector('.field-required').checked,
                    grid: item.querySelector('.field-grid').value,
                    options: item.querySelector('.field-options').value.split('\n').filter(opt => opt.trim())
                };
                formFields.push(fieldData);
            });
            
            // Add hidden input for form fields
            const formFieldsInput = document.createElement('input');
            formFieldsInput.type = 'hidden';
            formFieldsInput.name = 'form_fields';
            formFieldsInput.value = JSON.stringify(formFields);
            this.appendChild(formFieldsInput);
            
            // Handle program_meetings - they're already in the form as array inputs, so they'll be submitted automatically
            
            // Submit form
            this.submit();
        });

        // Add initial field
        document.addEventListener('DOMContentLoaded', function() {
            addFormField();
        });
    </script>
@endsection
