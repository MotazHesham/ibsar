<form method="POST" action="{{ route(($routeName ?? 'admin.beneficiaries.update'), $beneficiary->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="step" value="economic_information">
    
    <div class="row gy-4">
        @field('incomes')
        <div class="col-md-6">
            <h5 class="mb-3">{{ trans('cruds.beneficiary.fields.incomes') }}</h5>
            <div class="row">
                @foreach ($incomes as $income)
                    @include('utilities.form.text', [
                        'name' => 'incomes[' . $income->id . ']',
                        'label' => $income->name,
                        'isRequired' => $income->is_required,
                        'grid' => 'col-md-6',
                        'type' => $income->data_type,
                        'value' =>
                            $beneficiary->incomes && json_decode($beneficiary->incomes)
                                ? json_decode($beneficiary->incomes)->{$income->id}
                                : null,
                        'helperBlock' => '',
                    ])
                @endforeach
            </div>
        </div>
        @endfield
        <div class="col-md-6">
            <h5 class="mb-3">{{ trans('cruds.beneficiary.fields.expenses') }}</h5>
            <div class="row">
                @php
                    $beneficiaryExpenses = json_decode($beneficiary->expenses);
                @endphp
                <div class="col-md-12">
                    <div class="row"> 
                        @field('accommodation_type')
                        @include('utilities.form.select', [
                            'name' => 'accommodation_type_id',
                            'label' => 'cruds.beneficiary.fields.accommodation_type',
                            'grid' => 'col',
                            'isRequired' => true,
                            'options' => \App\Models\AccommodationType::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), ''),
                            'value' => $beneficiary->accommodation_type_id,
                        ])
                        @endfield
                        @field('accommodation_entity_charity')
                        <div class="col" id="accommodation_entity_charity_wrapper" style="display: none;">
                            @include('utilities.form.select', [
                                'name' => 'accommodation_entity_charity_id',
                                'label' => 'cruds.beneficiary.fields.accommodation_entity', 
                                'isRequired' => false,
                                'options' => $accommodation_entities_charity,
                                'value' => $beneficiary->accommodation_entity_id,
                            ])
                        </div>
                        @endfield
                        @field('accommodation_entity_social')
                        <div class="col" id="accommodation_entity_social_wrapper" style="display: none;">
                            @include('utilities.form.select', [
                                'name' => 'accommodation_entity_social_id',
                                'label' => 'cruds.beneficiary.fields.accommodation_entity', 
                                'isRequired' => false,
                                'options' => $accommodation_entities_social,
                                'value' => $beneficiary->accommodation_entity_id,
                            ])
                        </div>
                        @endfield
                        @field('accommodation_rent')
                        <div class="col" id="accommodation_rent_wrapper" style="display: none;">
                            @include('utilities.form.text', [
                                'name' => 'expenses[accommodation_rent]',
                                'label' => 'cruds.beneficiary.fields.accommodation_rent', 
                                'isRequired' => false, 
                                'type' => 'number',
                                'value' => $beneficiaryExpenses->accommodation_rent ?? null,
                            ])
                        </div>
                        @endfield
                        @field('accommodation_rent_late')
                        <div class="col" id="accommodation_rent_late_wrapper" style="display: none;">
                            @include('utilities.form.text', [
                                'name' => 'expenses[accommodation_rent_late]',
                                'label' => 'cruds.beneficiary.fields.accommodation_rent_late', 
                                'isRequired' => false, 
                                'type' => 'number',
                                'value' => $beneficiaryExpenses->accommodation_rent_late ?? null,
                            ])
                        </div>
                        @endfield
                    </div>
                </div>
                @field('expenses')
                    @foreach ($expenses as $expense)
                        @include('utilities.form.text', [
                            'name' => 'expenses[' . $expense->id . ']',
                            'label' => $expense->name,
                            'isRequired' => $expense->is_required,
                            'grid' => 'col-md-6',
                            'type' => $expense->data_type,
                            'value' =>
                                $beneficiaryExpenses && $beneficiaryExpenses->{$expense->id}
                                    ? $beneficiaryExpenses->{$expense->id}
                                    : null,
                            'helperBlock' => '',
                        ])
                    @endforeach
                @endfield
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary mt-3" name="step" value="economic_information">
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

        handleAccommodationTypeToggle('{{ $beneficiary->accommodation_type_id ?? '' }}'); 

        function handleAccommodationTypeToggle(value) { 
            var AccommodationEntityCharityWrapper = document.getElementById('accommodation_entity_charity_wrapper');
            var AccommodationEntitySocialWrapper = document.getElementById('accommodation_entity_social_wrapper');
            var AccommodationRentWrapper = document.getElementById('accommodation_rent_wrapper');
            var AccommodationRentLateWrapper = document.getElementById('accommodation_rent_late_wrapper');

            if (value == 1) {
                AccommodationEntityCharityWrapper.style.display = 'none';
                AccommodationEntitySocialWrapper.style.display = 'none';
                AccommodationRentWrapper.style.display = 'block';
                AccommodationRentLateWrapper.style.display = 'block';
            } else if(value == 3) {
                AccommodationEntityCharityWrapper.style.display = 'none';
                AccommodationEntitySocialWrapper.style.display = 'block';
                AccommodationRentWrapper.style.display = 'none';
                AccommodationRentLateWrapper.style.display = 'none';
            } else if(value == 4) {
                AccommodationEntityCharityWrapper.style.display = 'block';
                AccommodationEntitySocialWrapper.style.display = 'none';
                AccommodationRentWrapper.style.display = 'none';
                AccommodationRentLateWrapper.style.display = 'none';
            }else{
                AccommodationEntityCharityWrapper.style.display = 'none';
                AccommodationEntitySocialWrapper.style.display = 'none';
                AccommodationRentWrapper.style.display = 'none';
                AccommodationRentLateWrapper.style.display = 'none';
            }
        }


        var AccommodationTypeSelect = document.getElementById('accommodation_type_id'); 

        if (AccommodationTypeSelect) {
            AccommodationTypeSelect.addEventListener('change', function() {
                handleAccommodationTypeToggle(this.value);
            });
        }
    </script>
@endsection
