<form method="POST" action="{{ route($routeName ?? 'admin.beneficiaries.update', $beneficiary->id) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="step" value="basic_information">
    <div class="row gy-4">
        @field('name')
            @include('utilities.form.text', [
                'name' => 'name',
                'label' => 'cruds.user.fields.name',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('name'),
                'grid' => 'col-md-6',
                'value' => $user->name ?? '',
            ])
        @endfield
        @field('nationality_id')
            @include('utilities.form.select', [
                'name' => 'nationality_id',
                'label' => 'cruds.beneficiary.fields.nationality',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('nationality_id'),
                'options' => $nationalities,
                'grid' => 'col-md-6',
                'value' => $beneficiary->nationality_id ?? '',
                'search' => true,
            ])
        @endfield
        @field('characteristic_of_nationality')
            @include('utilities.form.select', [
                'name' => 'characteristic_of_nationality',
                'label' => 'cruds.beneficiary.fields.characteristic_of_nationality',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('characteristic_of_nationality'),
                'options' => \App\Models\Beneficiary::CHARACTERISTIC_OF_NATIONALITY_SELECT,
                'grid' => 'col-md-6',
                'value' => $beneficiary->characteristic_of_nationality ?? '',
            ])
        @endfield
        @field('dob')
            @include('utilities.form.date', [
                'name' => 'dob',
                'id' => 'dobBeneficiary',
                'label' => 'cruds.beneficiary.fields.dob',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('dob'),
                'grid' => 'col-md-4',
                'hijri' => true,
                'value' => $beneficiary->dob ?? '',
            ])
        @endfield
        @field('marital_status_id')
            @include('utilities.form.select', [
                'name' => 'marital_status_id',
                'label' => 'cruds.beneficiary.fields.marital_status',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('marital_status_id'),
                'options' => $marital_statuses,
                'grid' => 'col-md-4',
                'value' => $beneficiary->marital_status_id ?? '',
            ])
        @endfield
        @field('martial_status_date')
            @include('utilities.form.date', [
                'name' => 'martial_status_date',
                'id' => 'martialStatusDateBeneficiary',
                'label' => 'cruds.beneficiary.fields.martial_status_date',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('martial_status_date'),
                'grid' => 'col-md-4', 
                'value' => $beneficiary->martial_status_date ?? '',
            ])
        @endfield
        @field('city_id')
            @include('utilities.form.select', [
                'name' => 'city_id',
                'label' => 'cruds.beneficiary.fields.city',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('city_id'),
                'options' => $cities,
                'grid' => 'col-md-2',
                'value' => $beneficiary->city_id ?? '',
                'search' => true,
            ])
        @endfield
        @field('district_id')
            @include('utilities.form.select', [
                'name' => 'district_id',
                'label' => 'cruds.beneficiary.fields.district',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('district_id'),
                'options' => ['' => trans('global.pleaseSelect')],
                'grid' => 'col-md-2',
                'value' => $beneficiary->district_id ?? '',
                'search' => true,
            ])
        @endfield
        @field('street')
            @include('utilities.form.text', [
                'name' => 'street',
                'label' => 'cruds.beneficiary.fields.street',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('street'),
                'grid' => 'col-md-2',
                'value' => $beneficiary->street ?? '',
            ])
        @endfield
        @field('building_number')
            @include('utilities.form.text', [
                'name' => 'building_number',
                'label' => 'cruds.beneficiary.fields.building_number',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('building_number'),
                'grid' => 'col-md-2',
                'value' => $beneficiary->building_number ?? '',
            ])
        @endfield
        @field('building_additional_number')
            @include('utilities.form.text', [
                'name' => 'building_additional_number',
                'label' => 'cruds.beneficiary.fields.building_additional_number',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('building_additional_number'),
                'grid' => 'col-md-2',
                'value' => $beneficiary->building_additional_number ?? '',
            ])
        @endfield
        @field('postal_code')
            @include('utilities.form.text', [
                'name' => 'postal_code',
                'label' => 'cruds.beneficiary.fields.postal_code',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('postal_code'),
                'grid' => 'col-md-2',
                'value' => $beneficiary->postal_code ?? '',
            ])
        @endfield
        @field('address')
            @include('utilities.form.text', [
                'name' => 'address',
                'label' => 'cruds.beneficiary.fields.address',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('address'),
                'grid' => 'col-md-6',
                'value' => $beneficiary->address ?? '',
                'attributes' => 'readonly',
            ])
        @endfield
        @field('map')
            @include('utilities.form.map', [
                'name' => 'map',
                'label' => 'cruds.beneficiary.fields.map',
                'grid' => 'col-md-6',
                'isRequired' => \App\Helpers\FieldVisibilityHelper::isFieldRequired('map'),
                'latitude' => $beneficiary->latitude ?? '',
                'longitude' => $beneficiary->longitude ?? '',
            ])
        @endfield
    </div>
    <button type="submit" class="btn btn-primary mt-3">
        {{ trans('global.update') }}
    </button> 
    @if(getSetting('auto_accept_beneficiary') == 'yes' && auth()->user()->is_beneficiary && $beneficiary->canRequestOrder())
        <button type="submit" class="btn btn-success mt-3" name="redirect_to" value="request_order">
            {{ trans('cruds.beneficiary.extra.update_and_request_order') }}
        </button>
    @endif
</form>

@section('scripts')
    @parent
    <script>
        handleCityChange('{{ $beneficiary->city_id ?? '' }}');

        $('#city_id').on('change', function() {
            handleCityChange($(this).val());
        });

        function handleCityChange(value) {
            $.ajax({
                url: '{{ route('frontend.getDistrictsByCity') }}',
                type: 'GET',
                data: {
                    city_id: value
                },
                success: function(response) {
                    $('#district_id').empty();
                    $.each(response, function(key, value) {
                        $('#district_id').append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        }

        function updateFullAddress() {
            var fullAddress = $('#building_number').val() + ' - ' +
                $('#street').val() + ' - ' +
                $('#district_id option:selected').text().trim() + ' - ' +
                $('#city_id option:selected').text().trim() + ' - ' +
                $('#postal_code').val() + ' - ' +
                $('#building_additional_number').val();
                
            $('#address').val(fullAddress);
        }

        // Add change handlers for each field
        $('#building_number').on('change', updateFullAddress);
        $('#street').on('change', updateFullAddress); 
        $('#district_id').on('change', updateFullAddress);
        $('#city_id').on('change', updateFullAddress);
        $('#postal_code').on('change', updateFullAddress);
        $('#building_additional_number').on('change', updateFullAddress);
    </script>
@endsection
