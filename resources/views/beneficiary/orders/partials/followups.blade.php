<div class="card custom-card overflow-hidden">
    
    <div class="card-body p-0 position-relative" id="wrapper-order-followups-to-scroll" style="max-height: 35rem;"> 
        <div id="wrapper-order-followups">
            @include('admin.beneficiaryOrderFollowups.index', [
                'beneficiaryOrderFollowups' => $beneficiaryOrder->beneficiaryOrderFollowups,
            ])
        </div>  
    </div>
</div>
