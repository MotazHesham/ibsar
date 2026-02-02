<ul class="list-group list-group-flush border rounded-3 mt-3 shadow-sm">
    <li class="list-group-item p-3">
        <div class="card shadow-sm">
            <div class="ribbon-2 ribbon-secondary ribbon-left">
                <span class="ribbon-text">{{ trans('cruds.beneficiary.profile.profile_status') }}</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-end gap-2 justify-content-between align-items-center flex-wrap mb-3">
                    <span></span>
                    <span
                        class="badge bg-success">{{ $beneficiary->profile_status ? \App\Models\Beneficiary::PROFILE_STATUS_SELECT[$beneficiary->profile_status] : '-' }}</span>
                </div>
            </div>
        </div>
        <div class="mt-5">
            @if ($beneficiary->profile_status == 'approved')
                <form action="{{ route('admin.beneficiaries.update-case-study', $beneficiary) }}" class="p-3"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        @php
                            $beneficiaryExpenses = json_decode($beneficiary->expenses, true);
                            $beneficiaryCaseStudy = json_decode($beneficiary->case_study, true);
                        @endphp

                        <h4>{{ trans('cruds.beneficiary.extra.study_case_file') }}</h4>

                        @if (
                            $beneficiary->accommodation_type_id == 1 &&
                                (isset($beneficiaryExpenses['accommodation_rent_late']) && $beneficiaryExpenses['accommodation_rent_late'] > 0))
                            @include('utilities.form.text', [
                                'name' => 'rental_month_late',
                                'label' => 'cruds.beneficiary.fields.rental_month_late',
                                'isRequired' => false,
                                'grid' => 'col-md-12',
                                'value' => $beneficiaryCaseStudy['rental_month_late'] ?? '',
                            ])
                        @endif
                        @include('utilities.form.radio', [
                            'name' => 'removal_of_districts_affected',
                            'label' => 'cruds.beneficiary.fields.removal_of_districts_affected',
                            'isRequired' => false,
                            'options' => [
                                'yes' => 'نعم',
                                'no' => 'لا',
                            ],
                            'value' => $beneficiaryCaseStudy['removal_of_districts_affected'] ?? 'no',
                            'grid' => 'col-md-6',
                        ])
                        @include('utilities.form.text', [
                            'name' => 'removal_of_districts_district',
                            'label' => 'cruds.beneficiary.fields.removal_of_districts_district',
                            'isRequired' => false,
                            'grid' => 'col-md-6',
                            'value' => $beneficiaryCaseStudy['removal_of_districts_district'] ?? '',
                        ])
                        @include('utilities.form.radio', [
                            'name' => 'jeddah_municipality_support_has',
                            'label' => 'cruds.beneficiary.fields.jeddah_municipality_support_has',
                            'isRequired' => false,
                            'options' => [
                                'yes' => 'نعم',
                                'no' => 'لا',
                            ],
                            'value' => $beneficiaryCaseStudy['jeddah_municipality_support_has'] ?? 'no',
                            'grid' => 'col-md-4',
                        ])
                        <div class="col-md-8" id="jeddah_municipality_support_fields" style="display: {{ ($beneficiaryCaseStudy['jeddah_municipality_support_has'] ?? 'no') === 'yes' ? 'block' : 'none' }};">
                            <div class="row">
                                @include('utilities.form.text', [
                                    'name' => 'jeddah_municipality_support_support_duration',
                                    'label' => 'cruds.beneficiary.fields.jeddah_municipality_support_support_duration',
                                    'isRequired' => false,
                                    'grid' => 'col-md-6',
                                    'value' => $beneficiaryCaseStudy['jeddah_municipality_support_support_duration'] ?? '',
                                ])
                                @include('utilities.form.text', [
                                    'name' => 'jeddah_municipality_support_support_amount',
                                    'label' => 'cruds.beneficiary.fields.jeddah_municipality_support_support_amount',
                                    'isRequired' => false,
                                    'grid' => 'col-md-6',
                                    'value' => $beneficiaryCaseStudy['jeddah_municipality_support_support_amount'] ?? '',
                                ])
                            </div>
                        </div>
                        @include('utilities.form.radio', [
                            'name' => 'housing_quality',
                            'label' => 'cruds.beneficiary.fields.housing_quality',
                            'isRequired' => false,
                            'options' => \App\Models\Beneficiary::HOUSING_QUALITY_SELECT,
                            'value' => $beneficiaryCaseStudy['housing_quality'] ?? 'good',
                            'grid' => 'col-md-4',
                        ])
                        @include('utilities.form.radio', [
                            'name' => 'furniture_quality',
                            'label' => 'cruds.beneficiary.fields.furniture_quality',
                            'isRequired' => false,
                            'options' => \App\Models\Beneficiary::FURNITURE_QUALITY_SELECT,
                            'value' => $beneficiaryCaseStudy['furniture_quality'] ?? '',
                            'grid' => 'col-md-4',
                        ])
                        @include('utilities.form.radio', [
                            'name' => 'electrical_devices_quality',
                            'label' => 'cruds.beneficiary.fields.electrical_devices_quality',
                            'isRequired' => false,
                            'options' => \App\Models\Beneficiary::ELECTRICAL_DEVICES_QUALITY_SELECT,
                            'value' => $beneficiaryCaseStudy['electrical_devices_quality'] ?? '',
                            'grid' => 'col-md-4',
                        ])
                        @include('utilities.form.text', [
                            'name' => 'furniture_quality_details',
                            'label' => 'cruds.beneficiary.fields.furniture_quality_details',
                            'isRequired' => false,
                            'value' => $beneficiaryCaseStudy['furniture_quality_details'] ?? '',
                            'grid' => 'col-md-6',
                        ])
                        @include('utilities.form.text', [
                            'name' => 'electrical_devices_quality_details',
                            'label' => 'cruds.beneficiary.fields.electrical_devices_quality_details',
                            'isRequired' => false,
                            'value' => $beneficiaryCaseStudy['electrical_devices_quality_details'] ?? '',
                            'grid' => 'col-md-6',
                        ])
                        @include('utilities.form.textarea', [
                            'name' => 'summary_of_the_case',
                            'label' => 'cruds.beneficiary.fields.summary_of_the_case',
                            'isRequired' => false,
                            'value' => $beneficiaryCaseStudy['summary_of_the_case'] ?? '',
                            'grid' => 'col-md-12',
                        ])
                        @include('utilities.form.textarea', [
                            'name' => 'proposed_intervention',
                            'label' => 'cruds.beneficiary.fields.proposed_intervention',
                            'isRequired' => false,
                            'value' => $beneficiaryCaseStudy['proposed_intervention'] ?? '',
                            'grid' => 'col-md-12',
                        ])
                    </div>
                    <button type="submit" class="btn btn-primary  w-20 mt-3">
                        {{ trans('global.update') }}
                    </button>
                    <button type="submit" name="print" class="btn btn-info  w-20 mt-3">
                        {{ trans('global.print') }}
                    </button>
                </form>
            @endif
                <form action="{{ route('admin.beneficiaries.update-status', $beneficiary) }}" class="p-3"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        @php
                            $beneficiaryExpenses = json_decode($beneficiary->expenses, true);
                            $beneficiaryCaseStudy = json_decode($beneficiary->case_study, true);
                        @endphp
                        @include('utilities.form.radio', [
                            'name' => 'profile_status',
                            'label' => 'cruds.beneficiary.fields.profile_status',
                            'isRequired' => true,
                            'options' => [
                                'in_review' => 'قيد المراجعة',
                                'approved' => 'موافق عليه',
                                'rejected' => 'مرفوض',
                            ],
                            'value' => $beneficiary->profile_status,
                            'grid' => 'col-md-6',
                        ])
                        @can('beneficiary_control_all_access')
                            @include('utilities.form.select', [
                                'name' => 'specialist_id',
                                'label' => 'cruds.beneficiary.fields.specialist',
                                'isRequired' => false,
                                'grid' => 'col-md-6',
                                'options' => getSpecialistUsers($beneficiary->marital_status_id)->pluck(
                                    'name',
                                    'id'),
                                'search' => true,
                                'value' => $beneficiary->specialist_id,
                            ])
                        @endcan
                        <div class="col-md-12" id="rejection_reason_wrapper" style="display: none;">
                            @include('utilities.form.textarea', [
                                'name' => 'rejection_reason',
                                'label' => 'cruds.beneficiary.fields.rejection_reason',
                                'isRequired' => false,
                                'value' => $beneficiary->rejection_reason,
                            ])
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary  w-20 mt-3">
                        {{ trans('global.update') }}
                    </button>
                </form> 
        </div>
    </li>
</ul>

@section('scripts')
    @parent
    <script>
        handleRejectionReasonToggle('{{ $beneficiary->profile_status }}');

        function handleRejectionReasonToggle(value) {
            var RejectionReasonWrapper = document.getElementById('rejection_reason_wrapper');
            if(RejectionReasonWrapper){
                if (value === 'rejected') {
                    RejectionReasonWrapper.style.display = 'block';
                } else {
                    RejectionReasonWrapper.style.display = 'none';
                    document.getElementById('rejection_reason').value = '';
                }
            }
        }
        var StatusSelect = document.getElementsByName('profile_status');
        if (StatusSelect.length > 0) {
            StatusSelect.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    handleRejectionReasonToggle(this.value);
                });
            });
        }

        // Handle Jeddah Municipality Support fields visibility
        function handleJeddahMunicipalitySupportToggle(value) {
            var supportFieldsWrapper = document.getElementById('jeddah_municipality_support_fields');
            if (value === 'yes') {
                supportFieldsWrapper.style.display = 'block';
            } else {
                supportFieldsWrapper.style.display = 'none';
                // Clear the fields when hidden
                document.querySelector('input[name="jeddah_municipality_support_support_duration"]').value = '';
                document.querySelector('input[name="jeddah_municipality_support_support_amount"]').value = '';
            }
        }

        var JeddahMunicipalitySupportRadios = document.getElementsByName('jeddah_municipality_support_has');
        if (JeddahMunicipalitySupportRadios.length > 0) {
            JeddahMunicipalitySupportRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    handleJeddahMunicipalitySupportToggle(this.value);
                });
            });
        }
    </script>
@endsection
