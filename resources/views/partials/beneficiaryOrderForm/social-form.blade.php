
@include('utilities.form.select', [
    'name' => 'service_id',
    'label' => 'cruds.beneficiaryOrder.fields.service',
    'isRequired' => true, 
    'options' => $services,
    'search' => true,
])


@include('partials.beneficiaryOrderForm.basic-data') 


<div class="d-grid gap-2 col-6 mx-auto">
    <button class="btn btn-primary rounded-pill btn-wave" type="submit" id="submitBtn">
        {{ trans('global.save') }}
    </button>
</div>