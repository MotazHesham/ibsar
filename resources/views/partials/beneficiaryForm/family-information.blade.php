<!-- Add Button -->
<div class="mb-3">
    <div class="alert alert-light border mb-3">
        <strong>{{ trans('cruds.beneficiary.fields.children_count') }}:</strong>
        <span id="beneficiary-children-count">{{ $beneficiary->children_count ?? 0 }}</span>
        <small class="text-muted d-block mt-1">يُحسب تلقائياً من أفراد الأسرة (ابن / ابنة)</small>
    </div>
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
