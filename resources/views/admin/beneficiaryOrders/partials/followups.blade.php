<div class="card custom-card overflow-hidden">
    <div class="card-header justify-content-between"> 
        <button type="button" class="btn btn-secondary-light"
            onclick="showAjaxModal('{{ route('admin.beneficiary-order-followups.create') }}',{beneficiary_order_id: {{ $beneficiaryOrder->id }}})">
            <i class="fas fa-plus"></i> {{ trans('global.add') }} {{ trans('cruds.beneficiaryOrderFollowup.title_singular') }}
        </button>
    </div>
    <div class="card-body p-0 position-relative" id="wrapper-order-followups-to-scroll" style="max-height: 35rem;"> 
        <div id="wrapper-order-followups">
            @include('admin.beneficiaryOrderFollowups.index', [
                'beneficiaryOrderFollowups' => $beneficiaryOrder->beneficiaryOrderFollowups,
            ])
        </div>  
    </div>
</div>
