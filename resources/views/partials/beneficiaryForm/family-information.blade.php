<!-- Add Button -->
<div class="mb-3">
    <button type="button" class="btn btn-secondary-light"
        onclick="showAjaxModal('{{ route(($routeName ?? 'admin.beneficiary-families.create')) }}',{beneficiary_id: {{ $beneficiary->id }}})">
        <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('cruds.beneficiaryFamily.title_singular') }}
    </button>
</div>

<div id="wrapper-family-information" style="overflow: auto;">
    @include(($viewName ?? 'admin.beneficiaryFamilies.index'), [
        'beneficiaryFamilies' => $beneficiary->beneficiaryFamilies,
    ])
</div> 
