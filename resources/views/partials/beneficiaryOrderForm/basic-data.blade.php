<div class="row">
    @if (auth()->user()->user_type == 'staff')
        @include('utilities.form.select', [
            'name' => 'beneficiary_id',
            'label' => 'cruds.beneficiaryOrder.fields.beneficiary',
            'isRequired' => true,
            'grid' => 'col-md-6',
            'options' => $beneficiaries,
            'search' => true,
        ])
    @endif
    @if (getSetting('enable_request_for_family_members') == 'yes')
        @include('utilities.form.select', [
            'name' => 'beneficiary_family_id',
            'label' => 'cruds.beneficiaryOrder.fields.beneficiary_family',
            'isRequired' => false,
            'grid' => 'col-md-6',
            'options' => $beneficiaryFamilies ?? [
                '' => trans('global.pleaseSelect'),
            ],
            'search' => true,
        ])
    @endif

    @include('utilities.form.text', [
        'name' => 'title',
        'label' => 'cruds.beneficiaryOrder.fields.title',
        'isRequired' => true,
        'grid' => 'col-md-6',
    ])

    @include('utilities.form.textarea', [
        'name' => 'description',
        'label' => 'cruds.beneficiaryOrder.fields.description',
        'isRequired' => true,
        'grid' => 'col-md-12',
        'editor' => true,
    ])

    @include('utilities.form.dropzone', [
        'name' => 'attachment',
        'id' => 'attachment',
        'label' => 'cruds.beneficiaryOrder.fields.attachment',
        'isRequired' => false,
        'grid' => 'col-md-12',
        'url' => route('admin.beneficiary-orders.storeMedia'),
    ])
</div>

@section('scripts')
    @parent
    <script>   

        /* Health Condition Toggle */
        function handleBeneficiaryFamilyToggle(value) { 
            @if (getSetting('enable_request_for_family_members') == 'yes') 
                fetch('{{ route('beneficiary-families.get-by-beneficiary') }}?' + new URLSearchParams({
                    beneficiary_id: value
                }))
                .then(response => response.json())
                .then(data => {
                    const familySelect = document.getElementById('beneficiary_family_id');
                    familySelect.innerHTML = '';
                    const option = document.createElement('option'); 
                    option.textContent = '{{ trans('global.pleaseSelect') }}';
                    familySelect.appendChild(option);
                    Object.entries(data).forEach(([key, value]) => {
                        const option = document.createElement('option');
                        option.value = key;
                        option.textContent = value;
                        familySelect.appendChild(option);
                    });
                });
            @endif
        } 
        $('#beneficiary_id').on('select2:select', function (e) {
            handleBeneficiaryFamilyToggle(e.params.data.id);
        }); 
    </script>
@endsection
