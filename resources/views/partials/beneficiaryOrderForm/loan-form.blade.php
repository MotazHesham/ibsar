<style>
    .contact-item {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }

    .contact-item:hover {
        background-color: #e9ecef;
    }

    .remove-contact {
        transition: all 0.2s ease;
    }

    .remove-contact:hover {
        transform: scale(1.05);
    }

    .member-item {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }

    .member-item:hover {
        background-color: #e9ecef;
    }

    .remove-member {
        transition: all 0.2s ease;
    }

    .remove-member:hover {
        transform: scale(1.05);
    }

    /* Validation styling */
    .form-control.is-valid,
    .form-select.is-valid {
        border-color: #198754;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 3.03-3.03a.75.75 0 0 1 1.06 1.06L4.84 7.77a.75.75 0 0 1-1.06 0L2.3 6.73Z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4M7.2 4.6l-1.4 1.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    /* Navigation buttons styling */
    .btn-navigation {
        min-width: 120px;
        transition: all 0.3s ease;
    }

    .btn-navigation:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Tab content transitions */
    .tab-pane {
        transition: opacity 0.3s ease-in-out;
    }

    .tab-pane.fade {
        opacity: 0;
    }

    .tab-pane.fade.show {
        opacity: 1;
    }

    /* SweetAlert customization */
    .swal-wide {
        width: 600px !important;
        max-width: 90vw !important;
    }

    .swal-wide .swal2-content {
        white-space: pre-line;
        text-align: left;
        font-family: monospace;
        font-size: 0.9rem;
    }

    /* Field validation feedback */
    .invalid-feedback {
        display: block;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .valid-feedback {
        display: block;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Disabled tab styling */
    .disabled-tab {
        pointer-events: none;
        cursor: not-allowed;
        opacity: 0.6;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
    }

    .disabled-tab:hover {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
    }

    .disabled-tab.active {
        opacity: 1;
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        cursor: default;
        pointer-events: none;
    }

    .disabled-tab.completed {
        opacity: 0.8;
        background-color: #198754;
        border-color: #198754;
        color: #fff;
        cursor: default;
        pointer-events: none;
    }

    /* Tab progress indicator */
    .nav-tabs .nav-item {
        position: relative;
    }

    .nav-tabs .nav-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -1px;
        width: 2px;
        height: 20px;
        background-color: #dee2e6;
        transform: translateY(-50%);
        z-index: 1;
    }

    .nav-tabs .nav-item.completed::after {
        background-color: #198754;
    }

    /* Progress indicator */
    .form-progress {
        background: linear-gradient(90deg, #0d6efd 0%, #dee2e6 100%);
        height: 4px;
        border-radius: 2px;
        margin-bottom: 1rem;
        position: relative;
        overflow: hidden;
    }

    .form-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #198754 0%, #0d6efd 100%);
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    /* Enhanced disabled tab styling */
    .disabled-tab::before {
        content: '🔒';
        margin-right: 0.5rem;
        font-size: 0.875rem;
        opacity: 0.7;
    }

    .disabled-tab.active::before {
        content: '📍';
        opacity: 1;
    }

    .disabled-tab.completed::before {
        content: '✅';
        opacity: 1;
    }

    /* Make tabs look like progress indicators */
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        background-color: #f8f9fa;
        border-radius: 0.375rem 0.375rem 0 0;
        padding: 0.5rem 0.5rem 0;
    }

    .nav-tabs .nav-item {
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link {
        border: 2px solid transparent;
        border-radius: 0.375rem 0.375rem 0 0;
        margin-right: 0.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        border-color: transparent;
        background-color: transparent;
    }

    .nav-tabs .nav-link.active {
        border-color: #0d6efd;
        background-color: #fff;
        color: #0d6efd;
        border-bottom-color: #fff;
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link.completed {
        border-color: #198754;
        background-color: #fff;
        color: #198754;
        border-bottom-color: #fff;
        margin-bottom: -2px;
    }
</style>

{{-- Progress Bar --}}
<div class="form-progress">
    <div class="form-progress-bar" id="formProgressBar" style="width: 20%;"></div>
</div>

{{-- Tab Navigation --}}
<ul class="nav nav-tabs" id="loanFormTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active disabled-tab" id="order-info-tab" type="button" role="tab"
            aria-controls="order-info" aria-selected="true">
            {{ trans('cruds.service.extra.order_info') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link disabled-tab" id="project-tab" type="button" role="tab" aria-controls="project"
            aria-selected="false">
            {{ trans('cruds.serviceLoan.fields.project_information') }}
        </button>
    </li>
    <li class="nav-item" role="presentation" id="kafil-nav-item" style="display: none;">
        <button class="nav-link disabled-tab" id="kafil-tab" type="button" role="tab" aria-controls="kafil"
            aria-selected="false">
            {{ trans('cruds.serviceLoan.fields.kafil_information') }}
        </button>
    </li>
    <li class="nav-item" role="presentation" id="members-nav-item" style="display: none;">
        <button class="nav-link disabled-tab" id="members-tab" type="button" role="tab" aria-controls="members"
            aria-selected="false">
            {{ trans('cruds.serviceLoan.fields.members') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link disabled-tab" id="additional-tab" type="button" role="tab"
            aria-controls="additional" aria-selected="false">
            {{ trans('cruds.serviceLoan.fields.additional_information') }}
        </button>
    </li>
</ul>

{{-- Tab Content --}}
<div class="tab-content" id="loanFormTabsContent">
    {{-- Order Information Tab --}}
    <div class="tab-pane fade show active" id="order-info" role="tabpanel" aria-labelledby="order-info-tab">
        @include('partials.beneficiaryOrderForm.basic-data')

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-end mt-4">
            <button type="button" class="btn btn-primary btn-navigation" id="nextToProject">
                {{ trans('global.next') }} <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-left @else ti-arrow-right @endif"></i>
            </button>
        </div>
    </div>

    {{-- Project Information Tab --}}
    <div class="tab-pane fade" id="project" role="tabpanel" aria-labelledby="project-tab">
        <div class="row gy-4 mt-3">
            @include('utilities.form.select', [
                'name' => 'service_id',
                'label' => 'cruds.serviceLoan.fields.loan_type',
                'isRequired' => true,
                'options' => $services,
                'value' => $services[1]->id ?? null,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'project_type',
                'label' => 'cruds.serviceLoan.fields.project_type',
                'isRequired' => false,
                'options' => \App\Models\ServiceLoanMember::PROJECT_TYPE_SELECT,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'project_location',
                'label' => 'cruds.serviceLoan.fields.project_location',
                'isRequired' => false,
                'options' => \App\Models\ServiceLoanMember::PROJECT_LOCATION_SELECT,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'district_id',
                'label' => 'cruds.serviceLoan.fields.district',
                'isRequired' => false,
                'options' => $districts ?? ['' => trans('global.pleaseSelect')],
                'search' => true,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'street',
                'label' => 'cruds.serviceLoan.fields.street',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.date', [
                'name' => 'project_start_date',
                'id' => 'project_start_date',
                'label' => 'cruds.serviceLoan.fields.project_start_date',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'project_years_of_experience',
                'label' => 'cruds.serviceLoan.fields.project_years_of_experience',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'loan_id',
                'label' => 'cruds.serviceLoan.fields.loan',
                'isRequired' => true,
                'options' => $loans->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), ''),
                'grid' => 'col-md-3',
            ])
            @include('utilities.form.textarea', [
                'name' => 'project_short_description',
                'label' => 'cruds.serviceLoan.fields.project_short_description',
                'isRequired' => false,
                'grid' => 'col-md-12',
            ])

            @include('utilities.form.select', [
                'name' => 'project_financial_source',
                'label' => 'cruds.serviceLoan.fields.project_financial_source',
                'isRequired' => false,
                'options' => \App\Models\ServiceLoanMember::PROJECT_FINANCIAL_SOURCE_SELECT,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'purpose_of_loan',
                'label' => 'cruds.serviceLoan.fields.purpose_of_loan',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'has_previous_loan',
                'label' => 'cruds.serviceLoan.fields.has_previous_loan',
                'isRequired' => false,
                'options' => [
                    '' => trans('global.pleaseSelect'),
                    'yes' => trans('global.yes'),
                    'no' => trans('global.no'),
                ],
                'grid' => 'col-md-3',
            ])

            <div class="col-md-3" id="previous_loan_number_field" style="display: none;">
                @include('utilities.form.text', [
                    'name' => 'previous_loan_number',
                    'label' => 'cruds.serviceLoan.fields.previous_loan_number',
                    'isRequired' => false,
                    'grid' => '',
                ])
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary btn-navigation" id="backToOrderInfo">
                <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-right @else ti-arrow-left @endif"></i> {{ trans('global.previous') }}
            </button>
            <button type="button" class="btn btn-primary btn-navigation" id="nextToKafil" style="display: none;">
                {{ trans('global.next') }} <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-left @else ti-arrow-right @endif"></i>
            </button>
            <button type="button" class="btn btn-primary btn-navigation" id="nextToMembers" style="display: none;">
                {{ trans('global.next') }} <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-left @else ti-arrow-right @endif"></i>
            </button>
        </div>
    </div>

    {{-- Kafil Information Tab --}}
    <div class="tab-pane fade" id="kafil" role="tabpanel" aria-labelledby="kafil-tab">
        <div class="row gy-4 mt-3">
            @include('utilities.form.text', [
                'name' => 'kafil_name',
                'label' => 'cruds.serviceLoan.fields.kafil_name',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_identity_num',
                'label' => 'cruds.serviceLoan.fields.kafil_identity_num',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'accommodation_type_id',
                'label' => 'cruds.serviceLoan.fields.accommodation_type',
                'isRequired' => false,
                'options' => $accommodationTypes ?? ['' => trans('global.pleaseSelect')],
                'search' => true,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'marital_status_id',
                'label' => 'cruds.serviceLoan.fields.marital_status',
                'isRequired' => false,
                'options' => $maritalStatuses ?? ['' => trans('global.pleaseSelect')],
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'educational_qualification_id',
                'label' => 'cruds.serviceLoan.fields.educational_qualification',
                'isRequired' => false,
                'options' => $educationalQualifications ?? ['' => trans('global.pleaseSelect')],
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'job_type_id',
                'label' => 'cruds.serviceLoan.fields.job_type',
                'isRequired' => false,
                'options' => $jobTypes ?? ['' => trans('global.pleaseSelect')],
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.select', [
                'name' => 'kafil_district_id',
                'label' => 'cruds.serviceLoan.fields.kafil_district',
                'isRequired' => false,
                'options' => $districts ?? ['' => trans('global.pleaseSelect')],
                'search' => true,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_street',
                'label' => 'cruds.serviceLoan.fields.kafil_street',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_nearby_address',
                'label' => 'cruds.serviceLoan.fields.kafil_nearby_address',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_phone',
                'label' => 'cruds.serviceLoan.fields.kafil_phone',
                'isRequired' => false,
                'type' => 'tel',
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_phone2',
                'label' => 'cruds.serviceLoan.fields.kafil_phone2',
                'isRequired' => false,
                'type' => 'tel',
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_work_phone',
                'label' => 'cruds.serviceLoan.fields.kafil_work_phone',
                'isRequired' => false,
                'type' => 'tel',
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_work_address',
                'label' => 'cruds.serviceLoan.fields.kafil_work_address',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_email',
                'label' => 'cruds.serviceLoan.fields.kafil_email',
                'isRequired' => false,
                'type' => 'email',
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_work_name',
                'label' => 'cruds.serviceLoan.fields.kafil_work_name',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_mail_box',
                'label' => 'cruds.serviceLoan.fields.kafil_mail_box',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])

            @include('utilities.form.text', [
                'name' => 'kafil_postal_code',
                'label' => 'cruds.serviceLoan.fields.kafil_postal_code',
                'isRequired' => false,
                'grid' => 'col-md-3',
            ])
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary btn-navigation" id="backToProject">
                <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-right @else ti-arrow-left @endif"></i> {{ trans('global.previous') }}
            </button>
            <button type="button" class="btn btn-primary btn-navigation" id="nextToAdditionalFromKafil">
                {{ trans('global.next') }} <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-left @else ti-arrow-right @endif"></i>
            </button>
        </div>
    </div>

    {{-- Additional Information Tab --}}
    <div class="tab-pane fade" id="additional" role="tabpanel" aria-labelledby="additional-tab">
        <div class="row gy-4 mt-3">
            <div class="form-group mb-3">
                <label class="form-label">
                    {{ trans('cruds.serviceLoan.fields.contacts') }}
                    <span class="text-danger">*</span>
                </label>

                <div id="contacts-container">
                    <div class="contacts-list">
                        <!-- Existing contacts will be populated here -->
                    </div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary btn-sm" id="contacts-add-btn">
                            <i class="ti ti-plus"></i> {{ trans('global.add_contact') }}
                        </button>
                    </div>
                </div>

                @if ($errors->has('contacts'))
                    <div class="invalid-feedback">
                        {{ $errors->first('contacts') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary btn-navigation" id="backToProjectFromAdditional">
                <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-right @else ti-arrow-left @endif"></i> {{ trans('global.previous') }}
            </button>
            <button type="submit" class="btn btn-success btn-navigation" id="finishForm">
                {{ trans('global.finish') }} <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-left @else ti-arrow-right @endif"></i>
            </button>
        </div>
    </div>

    {{-- Members Tab --}}
    <div class="tab-pane fade" id="members" role="tabpanel" aria-labelledby="members-tab">
        <div class="row gy-4 mt-3">
            <div class="form-group mb-3">
                <label class="form-label">
                    {{ trans('cruds.serviceLoan.fields.members') }}
                </label>


                @include('utilities.form.text', [
                    'name' => 'group_name',
                    'label' => 'cruds.serviceLoan.fields.group_name',
                    'isRequired' => false,
                    'grid' => 'col-md-3',
                ])
                <div id="members-container">
                    <div class="members-list">
                        <!-- Existing members will be populated here -->
                    </div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary btn-sm" id="members-add-btn">
                            <i class="ti ti-plus"></i> {{ trans('global.add_member') }}
                        </button>
                    </div>
                </div>

                @if ($errors->has('members'))
                    <div class="invalid-feedback">
                        {{ $errors->first('members') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary btn-navigation" id="backToAdditional">
                <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-right @else ti-arrow-left @endif"></i> {{ trans('global.previous') }}
            </button>
            <button type="button" class="btn btn-primary btn-navigation" id="nextToAdditional">
                {{ trans('global.next') }} <i class="ti @if(app()->getLocale() == 'ar') ti-arrow-left @else ti-arrow-right @endif"></i>
            </button>
        </div>
    </div>
</div>

@section('scripts')
    @parent
    <script>
        (function() {
            'use strict';

            // Tab navigation functionality
            let currentTabIndex = 0;
            let tabs = [
                'order-info',
                'project'
            ];

            // Function to update tabs array based on service type
            function updateTabsArray() {
                const serviceId = document.querySelector('select[name="service_id"]')?.value;
                const serviceKeyNames = @json($serviceKeyNames ?? []);
                const serviceKeyName = serviceKeyNames[serviceId] || '';
                const previousTabsLength = tabs.length;
                
                // Reset to base tabs
                tabs = ['order-info', 'project'];
                
                // Add conditional third tab
                if (serviceKeyName === 'individual_loan') {
                    tabs.push('kafil');
                } else if (serviceKeyName === 'group_loan') {
                    tabs.push('members');
                }
                
                // Always add additional tab last
                tabs.push('additional');
                
                // Debug logging
                console.log('Service ID:', serviceId);
                console.log('Service Key Name:', serviceKeyName);
                console.log('Updated tabs array:', tabs);
                console.log('Current tab index:', currentTabIndex);
                
                // Check if current tab is still valid
                const currentTabId = tabs[currentTabIndex];
                if (!currentTabId) {
                    // Current tab is no longer valid, go back to project tab
                    currentTabIndex = 1;
                    console.log('Redirecting to project tab');
                } else if (currentTabIndex >= tabs.length) {
                    // Current tab index is out of bounds, adjust to last tab
                    currentTabIndex = tabs.length - 1;
                    console.log('Adjusting to last tab');
                }
                
                // If tabs array length changed, we need to redirect
                if (previousTabsLength !== tabs.length) {
                    console.log('Tabs array length changed, redirecting');
                    showTab(currentTabIndex);
                } else {
                    // Just update progress and navigation
                    console.log('Updating progress and navigation');
                    updateTabProgress();
                    updateNavigationButtons();
                }
            }

            // Function to show tab by index
            function showTab(tabIndex) {
                if (tabIndex < 0 || tabIndex >= tabs.length) return;

                // Hide all tabs
                tabs.forEach((tabId, index) => {
                    const tabPane = document.getElementById(tabId);
                    const tabButton = document.getElementById(`${tabId}-tab`);
                    const navItem = tabButton?.closest('.nav-item');

                    if (tabPane && tabButton) {
                        tabPane.classList.remove('show', 'active');
                        tabButton.classList.remove('active');
                        tabButton.setAttribute('aria-selected', 'false');

                        // Add completed class to previously visited tabs
                        if (index < currentTabIndex) {
                            tabButton.classList.add('completed');
                            if (navItem) navItem.classList.add('completed');
                        } else {
                            tabButton.classList.remove('completed');
                            if (navItem) navItem.classList.remove('completed');
                        }
                    }
                });

                // Show target tab
                const targetTabPane = document.getElementById(tabs[tabIndex]);
                const targetTabButton = document.getElementById(`${tabs[tabIndex]}-tab`);
                const targetNavItem = targetTabButton?.closest('.nav-item');

                if (targetTabPane && targetTabButton) {
                    targetTabPane.classList.add('show', 'active');
                    targetTabButton.classList.add('active');
                    targetTabButton.setAttribute('aria-selected', 'true');

                    // Remove completed class from current tab
                    targetTabButton.classList.remove('completed');
                    if (targetNavItem) targetNavItem.classList.remove('completed');
                }

                currentTabIndex = tabIndex;
                updateNavigationButtons();
                updateTabProgress();
            }

            // Function to update tab progress indicators
            function updateTabProgress() {
                const progressBar = document.getElementById('formProgressBar');
                const progressPercentage = ((currentTabIndex + 1) / tabs.length) * 100;
                
                if (progressBar) {
                    progressBar.style.width = progressPercentage + '%';
                }
                
                tabs.forEach((tabId, index) => {
                    const tabButton = document.getElementById(`${tabId}-tab`);
                    const navItem = tabButton?.closest('.nav-item');
                    
                    if (tabButton && navItem) {
                        if (index < currentTabIndex) {
                            // Completed tabs
                            tabButton.classList.add('completed');
                            navItem.classList.add('completed');
                        } else if (index === currentTabIndex) {
                            // Current tab
                            tabButton.classList.remove('completed');
                            navItem.classList.remove('completed');
                        } else {
                            // Future tabs
                            tabButton.classList.remove('completed');
                            navItem.classList.remove('completed');
                        }
                    }
                });
            }

            // Function to validate current tab
            function validateCurrentTab() {
                const currentTabId = tabs[currentTabIndex];
                const requiredFields = [];
                const emptyFields = [];

                // Get all required fields in current tab
                const tabPane = document.getElementById(currentTabId);
                if (tabPane) {
                    const requiredInputs = tabPane.querySelectorAll(
                        'input[required], select[required], textarea[required]');
                    requiredInputs.forEach(input => {
                        if (!input.value.trim()) {
                            requiredFields.push(input);
                            emptyFields.push(input.name || input.getAttribute('placeholder') || 'Field');
                        }
                    });
                }

                return {
                    isValid: requiredFields.length === 0,
                    invalidFields: requiredFields,
                    emptyFieldNames: emptyFields
                };
            }

            // Function to show validation errors
            function showValidationErrors(invalidFields, emptyFieldNames = []) {
                // Remove previous error styling
                document.querySelectorAll('.is-invalid').forEach(field => {
                    field.classList.remove('is-invalid');
                });

                // Add error styling to invalid fields
                invalidFields.forEach(field => {
                    field.classList.add('is-invalid');
                });

                // Show error message with specific field names
                let errorMessage = '{{ trans('global.please_fill_required_fields') }}';
                if (emptyFieldNames.length > 0) {
                    errorMessage += `\n\n${emptyFieldNames.join(', ')}`;
                }

                Swal.fire({
                    icon: 'error',
                    title: '{{ trans('global.validation_error') }}',
                    text: errorMessage,
                    confirmButtonText: '{{ trans('global.ok') }}',
                    customClass: {
                        popup: 'swal-wide'
                    }
                });

                // Scroll to first invalid field
                if (invalidFields.length > 0) {
                    invalidFields[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            // Function to update navigation buttons visibility
            function updateNavigationButtons() {
                const serviceId = document.querySelector('select[name="service_id"]')?.value;
                const serviceKeyNames = @json($serviceKeyNames ?? []);
                const serviceKeyName = serviceKeyNames[serviceId] || '';
                
                // Get all navigation buttons
                const nextToProject = document.getElementById('nextToProject');
                const nextToKafil = document.getElementById('nextToKafil');
                const nextToMembers = document.getElementById('nextToMembers');
                const nextToAdditional = document.getElementById('nextToAdditional');
                const nextToAdditionalFromKafil = document.getElementById('nextToAdditionalFromKafil');
                const nextToMembersFromAdditional = document.getElementById('nextToMembersFromAdditional');
                
                // Show/hide next buttons based on service type and current tab
                if (nextToKafil) {
                    nextToKafil.style.display = (serviceKeyName === 'individual_loan' && currentTabIndex === 1) ? 'block' : 'none';
                }
                if (nextToMembers) {
                    nextToMembers.style.display = (serviceKeyName === 'group_loan' && currentTabIndex === 1) ? 'block' : 'none';
                }
                if (nextToAdditional) {
                    const additionalIndex = tabs.indexOf('additional');
                    const currentTabId = tabs[currentTabIndex];
                    if (currentTabId === 'members') {
                        nextToAdditional.style.display = 'block';
                    } else if (currentTabId === 'kafil') {
                        nextToAdditional.style.display = 'block';
                    } else {
                        nextToAdditional.style.display = 'none';
                    }
                }
                if (nextToAdditionalFromKafil) {
                    nextToAdditionalFromKafil.style.display = (serviceKeyName === 'individual_loan' && currentTabIndex === tabs.indexOf('kafil')) ? 'block' : 'none';
                }
                if (nextToMembersFromAdditional) {
                    nextToMembersFromAdditional.style.display = (serviceKeyName === 'group_loan' && currentTabIndex === tabs.indexOf('additional')) ? 'block' : 'none';
                }
            }

            // Navigation button event listeners
            function setupNavigationButtons() {
                // Next to Project
                const nextToProject = document.getElementById('nextToProject');
                if (nextToProject) {
                    nextToProject.addEventListener('click', () => {
                        const validation = validateCurrentTab();
                        if (validation.isValid) {
                            showTab(1); // Go to project tab
                        } else {
                            showValidationErrors(validation.invalidFields, validation.emptyFieldNames);
                        }
                    });
                }

                // Back to Order Info
                const backToOrderInfo = document.getElementById('backToOrderInfo');
                if (backToOrderInfo) {
                    backToOrderInfo.addEventListener('click', () => {
                        showTab(0); // Go to order info tab
                    });
                }

                // Next to Kafil
                const nextToKafil = document.getElementById('nextToKafil');
                if (nextToKafil) {
                    nextToKafil.addEventListener('click', () => {
                        const validation = validateCurrentTab();
                        if (validation.isValid) {
                            const kafilIndex = tabs.indexOf('kafil');
                            if (kafilIndex !== -1) {
                                showTab(kafilIndex);
                            }
                        } else {
                            showValidationErrors(validation.invalidFields, validation.emptyFieldNames);
                        }
                    });
                }

                // Next to Members
                const nextToMembers = document.getElementById('nextToMembers');
                if (nextToMembers) {
                    nextToMembers.addEventListener('click', () => {
                        const validation = validateCurrentTab();
                        if (validation.isValid) {
                            const membersIndex = tabs.indexOf('members');
                            if (membersIndex !== -1) {
                                showTab(membersIndex);
                            }
                        } else {
                            showValidationErrors(validation.invalidFields, validation.emptyFieldNames);
                        }
                    });
                }

                // Next to Additional
                const nextToAdditional = document.getElementById('nextToAdditional');
                if (nextToAdditional) {
                    nextToAdditional.addEventListener('click', () => {
                        const validation = validateCurrentTab();
                        if (validation.isValid) {
                            const additionalIndex = tabs.indexOf('additional');
                            if (additionalIndex !== -1) {
                                showTab(additionalIndex);
                            }
                        } else {
                            showValidationErrors(validation.invalidFields, validation.emptyFieldNames);
                        }
                    });
                }

                // Back to Project
                const backToProject = document.getElementById('backToProject');
                if (backToProject) {
                    backToProject.addEventListener('click', () => {
                        showTab(1); // Go to project tab
                    });
                }

                // Next to Additional from Kafil
                const nextToAdditionalFromKafil = document.getElementById('nextToAdditionalFromKafil');
                if (nextToAdditionalFromKafil) {
                    nextToAdditionalFromKafil.addEventListener('click', () => {
                        const validation = validateCurrentTab();
                        if (validation.isValid) {
                            const additionalIndex = tabs.indexOf('additional');
                            if (additionalIndex !== -1) {
                                showTab(additionalIndex);
                            }
                        } else {
                            showValidationErrors(validation.invalidFields, validation.emptyFieldNames);
                        }
                    });
                }

                // Back to Project from Additional
                const backToProjectFromAdditional = document.getElementById('backToProjectFromAdditional');
                if (backToProjectFromAdditional) {
                    backToProjectFromAdditional.addEventListener('click', () => {
                        const projectIndex = tabs.indexOf('project');
                        if (projectIndex !== -1) {
                            showTab(projectIndex);
                        }
                    });
                }

                // Next to Members from Additional
                const nextToMembersFromAdditional = document.getElementById('nextToMembersFromAdditional');
                if (nextToMembersFromAdditional) {
                    nextToMembersFromAdditional.addEventListener('click', () => {
                        const validation = validateCurrentTab();
                        if (validation.isValid) {
                            const membersIndex = tabs.indexOf('members');
                            if (membersIndex !== -1) {
                                showTab(membersIndex);
                            }
                        } else {
                            showValidationErrors(validation.invalidFields, validation.emptyFieldNames);
                        }
                    });
                }

                // Back to Additional
                const backToAdditional = document.getElementById('backToAdditional');
                if (backToAdditional) {
                    backToAdditional.addEventListener('click', () => {
                        const additionalIndex = tabs.indexOf('additional');
                        if (additionalIndex !== -1) {
                            showTab(additionalIndex);
                        }
                    });
                }
            }

            const container = document.getElementById('contacts-container');
            const contactsList = container.querySelector('.contacts-list');
            const addBtn = document.getElementById('contacts-add-btn');
            let contactIndex = 0;

            // Get family relationships for dropdown
            const familyRelationships = @json($familyRelationships ?? []);

            // Convert to array format if needed
            const familyRelationshipsArray = Array.isArray(familyRelationships) ? familyRelationships : Object.entries(
                familyRelationships).map(([id, name]) => ({
                id,
                name
            }));

            // Template for a single contact
            function createContactTemplate(index, data = {}) {
                return `
            <div class="contact-item border rounded p-3 mb-3" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">{{ trans('global.contact') }} #${index + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-contact" data-index="${index}">
                        <i class="ti ti-trash"></i> {{ trans('global.remove') }}
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ trans('global.name') }}</label>
                        <input type="text" 
                            class="form-control contact-name" 
                            name="contacts[${index}][name]" 
                            value="${data.name || ''}" >
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">{{ trans('cruds.beneficiaryFamily.fields.family_relationship') }}</label>
                        <select class="form-select contact-family-relationship" 
                                name="contacts[${index}][family_relationship_id]">
                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                            ${familyRelationshipsArray.map(rel => 
                                `<option value="${rel.id}" ${data.family_relationship_id == rel.id ? 'selected' : ''}>${rel.name}</option>`
                            ).join('')}
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">{{ trans('global.phone') }}</label>
                        <input type="tel" 
                            class="form-control contact-phone" 
                            name="contacts[${index}][phone]" 
                            value="${data.phone || ''}" >
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">{{ trans('global.identity_num') }}</label>
                        <input type="text" 
                            class="form-control contact-identity" 
                            name="contacts[${index}][identity_num]" 
                            value="${data.identity_num || ''}" >
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">{{ trans('global.address') }}</label>
                        <textarea class="form-control contact-address" 
                                name="contacts[${index}][address]" 
                                rows="2">${data.address || ''}</textarea>
                    </div>
                </div>
            </div>
        `;
            }

            // Add new contact
            function addContact(data = {}) {
                const contactHtml = createContactTemplate(contactIndex, data);
                contactsList.insertAdjacentHTML('beforeend', contactHtml);
                contactIndex++;
                updateContactNumbers();

                // Scroll to the new contact
                const newContact = contactsList.lastElementChild;
                if (newContact) {
                    newContact.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }

            // Remove contact
            function removeContact(index) {
                const contactItem = container.querySelector(`[data-index="${index}"]`);
                if (contactItem) {
                    contactItem.remove();
                    updateContactNumbers();
                }
            }

            // Update contact numbers
            function updateContactNumbers() {
                const contactItems = container.querySelectorAll('.contact-item');
                contactItems.forEach((item, index) => {
                    const title = item.querySelector('h6');
                    title.textContent = `{{ trans('global.contact') }} #${index + 1}`;
                });
            }

            // Initialize existing contacts if any
            @if (isset($value) && $value)
                const existingContacts = @json($value);
                if (Array.isArray(existingContacts)) {
                    existingContacts.forEach(contact => {
                        addContact(contact);
                    });
                }
            @endif

            // Add event listeners
            addBtn.addEventListener('click', () => {
                addContact();
            });

            // Event delegation for remove buttons
            contactsList.addEventListener('click', (e) => {
                if (e.target.closest('.remove-contact')) {
                    const index = e.target.closest('.remove-contact').dataset.index;
                    const contactItem = container.querySelector(`[data-index="${index}"]`);

                    if (contactItem && confirm('{{ trans('global.areYouSure') }}')) {
                        removeContact(index);
                    }
                }
            }); 

            // Members functionality
            const membersContainer = document.getElementById('members-container');
            const membersList = membersContainer.querySelector('.members-list');
            const membersAddBtn = document.getElementById('members-add-btn');
            let memberIndex = 0;

            // Template for a single member
            function createMemberTemplate(index, data = {}) {
                return `
            <div class="member-item border rounded p-3 mb-3" data-index="${index}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">{{ trans('global.member') }} #${index + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-member" data-index="${index}">
                        <i class="ti ti-trash"></i> {{ trans('global.remove') }}
                    </button>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ trans('global.name') }}</label>
                        <input type="text" 
                            class="form-control member-name" 
                            name="members[${index}][name]" 
                            value="${data.name || ''}">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">{{ trans('global.identity_num') }}</label>
                        <input type="text" 
                            class="form-control member-identity" 
                            name="members[${index}][identity_number]" 
                            value="${data.identity_number || ''}">
                    </div>
                </div>
            </div>
        `;
            }

            // Add new member
            function addMember(data = {}) {
                const memberHtml = createMemberTemplate(memberIndex, data);
                membersList.insertAdjacentHTML('beforeend', memberHtml);
                memberIndex++;
                updateMemberNumbers();

                // Scroll to the new member
                const newMember = membersList.lastElementChild;
                if (newMember) {
                    newMember.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });
                }
            }

            // Remove member
            function removeMember(index) {
                const memberItem = membersContainer.querySelector(`[data-index="${index}"]`);
                if (memberItem) {
                    memberItem.remove();
                    updateMemberNumbers();
                }
            }

            // Update member numbers
            function updateMemberNumbers() {
                const memberItems = membersContainer.querySelectorAll('.member-item');
                memberItems.forEach((item, index) => {
                    const title = item.querySelector('h6');
                    title.textContent = `{{ trans('global.member') }} #${index + 1}`;
                });
            }

            // Initialize existing members if any
            @if (isset($value) && $value)
                const existingMembers = @json($value);
                if (Array.isArray(existingMembers)) {
                    existingMembers.forEach(member => {
                        addMember(member);
                    });
                }
            @endif

            // Add event listeners for members
            membersAddBtn.addEventListener('click', () => {
                addMember();
            });

            // Event delegation for member remove buttons
            membersList.addEventListener('click', (e) => {
                if (e.target.closest('.remove-member')) {
                    const index = e.target.closest('.remove-member').dataset.index;
                    const memberItem = membersContainer.querySelector(`[data-index="${index}"]`);

                    if (memberItem && confirm('{{ trans('global.areYouSure') }}')) {
                        removeMember(index);
                    }
                }
            }); 

            // Simple function to toggle kafil and members nav based on service_id
            function toggleNavTabs() {
                const serviceId = document.querySelector('select[name="service_id"]');
                const kafilNav = document.getElementById('kafil-nav-item');
                const membersNav = document.getElementById('members-nav-item');
                
                if (!serviceId || !kafilNav || !membersNav) return;
                
                const serviceKeyNames = @json($serviceKeyNames ?? []);
                const serviceKeyName = serviceKeyNames[serviceId.value] || '';
                
                if (serviceKeyName === 'individual_loan') {
                    kafilNav.style.display = 'block';
                    membersNav.style.display = 'none';
                } else if (serviceKeyName === 'group_loan') {
                    kafilNav.style.display = 'none';
                    membersNav.style.display = 'block';
                } else {
                    kafilNav.style.display = 'none';
                    membersNav.style.display = 'none';
                }
                
                // Update tabs array and navigation
                updateTabsArray();
            }

            // Wait for page to load, then set up the functionality
            window.addEventListener('load', function() {
                // Prevent direct tab clicking
                document.querySelectorAll('.nav-link').forEach(tab => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    });
                });

                // Initial setup
                updateTabsArray();
                toggleNavTabs();
                setupNavigationButtons();
                updateTabProgress();

                // Listen for changes
                const serviceSelect = document.querySelector('select[name="service_id"]');
                if (serviceSelect) {
                    serviceSelect.addEventListener('change', toggleNavTabs);
                }

                // Handle previous loan number field visibility
                function handlePreviousLoanToggle(value) {
                    const previousLoanField = document.getElementById('previous_loan_number_field');
                    if (previousLoanField) {
                        if (value === 'yes') {
                            previousLoanField.style.display = 'block';
                        } else {
                            previousLoanField.style.display = 'none';
                            // Clear the field when hidden
                            const previousLoanInput = document.querySelector('input[name="previous_loan_number"]');
                            if (previousLoanInput) {
                                previousLoanInput.value = '';
                            }
                        }
                    }
                }

                // Initialize previous loan field visibility
                const hasPreviousLoanSelect = document.querySelector('select[name="has_previous_loan"]');
                if (hasPreviousLoanSelect) {
                    // Set initial state
                    handlePreviousLoanToggle(hasPreviousLoanSelect.value);
                    
                    // Add event listener
                    hasPreviousLoanSelect.addEventListener('change', function() {
                        handlePreviousLoanToggle(this.value);
                    });
                }

                // Add real-time validation feedback
                document.addEventListener('input', function(e) {
                    if (e.target.hasAttribute('required')) {
                        if (e.target.value.trim()) {
                            e.target.classList.remove('is-invalid');
                            e.target.classList.add('is-valid');
                        } else {
                            e.target.classList.remove('is-valid');
                            e.target.classList.add('is-invalid');
                        }
                    }
                });

                document.addEventListener('change', function(e) {
                    if (e.target.hasAttribute('required')) {
                        if (e.target.value.trim()) {
                            e.target.classList.remove('is-invalid');
                            e.target.classList.add('is-valid');
                        } else {
                            e.target.classList.remove('is-valid');
                            e.target.classList.add('is-invalid');
                        }
                    }
                });
            });

        })();
    </script>
@endsection
