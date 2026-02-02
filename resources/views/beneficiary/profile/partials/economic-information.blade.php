<div class="row g-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-end gap-2 justify-content-between align-items-center flex-wrap mb-3"> 
                                    <span class="avatar avatar-rounded bg-primary-transparent">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><line x1="128" y1="72" x2="128" y2="88" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/><line x1="128" y1="168" x2="128" y2="184" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/><circle cx="128" cy="128" r="96" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/><path d="M104,168h36a20,20,0,0,0,0-40H116a20,20,0,0,1,0-40h36" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/></svg>
                                    </span>
                                    <h4 class="mb-0 mt-3 fw-smeibold">{{ $beneficiary->total_incomes - $beneficiary->total_expenses }}</h4>
                                    <div class="fs-12 text-muted fw-medium">{{ trans('cruds.beneficiary.profile.net_income') }}</div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-end gap-2 justify-content-between align-items-center flex-wrap mb-3"> 
                                    <span class="avatar avatar-rounded bg-success-transparent">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256"><rect width="256" height="256" fill="none"/><circle cx="128" cy="144" r="40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/><path d="M72,216a65,65,0,0,1,112,0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/><path d="M164,72.55a32,32,0,1,1,39.63,45.28c14.33,3.1,27.89,14.84,36.4,26.17" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/><path d="M16,144c8.51-11.33,22.06-23.07,36.4-26.17A32,32,0,1,1,92,72.55" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="24"/></svg>
                                    </span>
                                    <h4 class="mb-0 mt-3 fw-smeibold">{{ $beneficiary->economicCategory() }}</h4>
                                    <div class="fs-12 text-muted fw-medium">{{ trans('cruds.beneficiary.profile.category') }}</div> 
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <span class="ribbon-3 top-right ribbon-success">
                    <span>+<i class="fe fe-dollar-sign"></i></span>
                </span>
                <h5 class="card-title mb-4">{{ trans('cruds.beneficiary.fields.incomes') }}</h5>
                <div class="row">
                    @foreach ($incomes as $income) 
                        <div class=" col-md-6"> 
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control"
                                    placeholder="name@example.com" disabled
                                    value="{{ $beneficiary->incomes && json_decode($beneficiary->incomes) ? json_decode($beneficiary->incomes)->{$income->id} : null }}" 
                                    >
                                <label>{{ trans($income->name) }}</label>
                            </div>  
                        </div>
                    @endforeach
                    <div class="col-md-12">
                        <div class="form-floating mb-3 floating-success">
                            <input type="text" class="form-control" disabled value="{{ $beneficiary->total_incomes }}">
                            <label>{{ trans('global.total') }}</label>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <span class="ribbon-3 top-right ribbon-danger"> 
                    <span>-<i class="fe fe-dollar-sign"></i></span>
                </span>
                <h5 class="card-title mb-4">{{ trans('cruds.beneficiary.fields.expenses') }}</h5>
                <div class="row"> 
                    @foreach ($expenses as $expense)
                        <div class=" col-md-6"> 
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control"
                                    placeholder="name@example.com" disabled
                                    value="{{ $beneficiary->expenses && json_decode($beneficiary->expenses) ? json_decode($beneficiary->expenses)->{$expense->id} : null }}" 
                                    >
                                <label>{{ trans($expense->name) }}</label>
                            </div>  
                        </div>
                    @endforeach
                    <div class="col-md-12">
                        <div class="form-floating mb-3 floating-danger">
                            <input type="text" class="form-control" disabled value="{{ $beneficiary->total_expenses }}">
                            <label>{{ trans('global.total') }}</label>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>