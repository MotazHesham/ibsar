<div class="card custom-card">
    <div class="card-header justify-content-between">
        <div class="card-title">
            {{ trans('cruds.beneficiaryOrder.extra.info') }}
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-4 gap-2 flex-wrap">
            <span class="avatar avatar-lg me-1 bg-primary-gradient p-2">
                @if(str_starts_with($beneficiaryOrder->service_type, 'dynamic_'))
                    @php
                        $dynamicServiceId = str_replace('dynamic_', '', $beneficiaryOrder->service_type);
                        $dynamicService = \App\Models\DynamicService::find($dynamicServiceId);
                    @endphp
                    @if($dynamicService && $dynamicService->icon)
                        <img src="{{ $dynamicService->icon }}" class="card-img" alt="{{ $dynamicService->title }}">
                    @else
                        <img src="{{ asset('assets/images/services/dynamic.png') }}" class="card-img" alt="Dynamic Service">
                    @endif
                @else
                    <img src="{{ asset('assets/images/services/' . $beneficiaryOrder->service_type . '.png') }}"
                        class="card-img" alt="...">
                @endif
            </span>
            <div>
                <h6 class="fw-medium mb-2 task-title">
                    {{ $beneficiaryOrder->title ?? '' }}
                </h6>
                <span class="badge bg-{{ $beneficiaryOrder->status->badge_class ?? 'primary' }}-transparent">
                    {{ $beneficiaryOrder->status->name ?? '' }}
                </span>
                <span class="text-muted fs-12"><i class="ri-circle-fill text-success mx-2 fs-9"></i>
                    {{ trans('global.last_updated') }}
                    {{ $beneficiaryOrder->updated_at->diffForHumans() }}
                </span>
            </div>
            <div class="ms-auto align-self-start">
                <div class="dropdown">
                    <a aria-label="anchor" href="javascript:void(0);" class="btn btn-icon btn-sm btn-primary-light"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fe fe-more-vertical"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item"
                                href="{{ route('admin.beneficiary-orders.edit', $beneficiaryOrder->id) }}"><i
                                    class="ri-edit-line align-middle me-1 d-inline-block"></i>{{ trans('global.edit') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="text-muted mb-4">
            @if(str_starts_with($beneficiaryOrder->service_type, 'dynamic_'))
                @php
                    $dynamicServiceId = str_replace('dynamic_', '', $beneficiaryOrder->service_type);
                    $dynamicService = \App\Models\DynamicService::find($dynamicServiceId);
                @endphp
                <h5>({{ $dynamicService ? $dynamicService->title : 'Dynamic Service' }})</h5>
            @else
                <h5>({{ $beneficiaryOrder->service->title ?? '' }})</h5>
            @endif
        </p>
        <div class="fs-19 fw-medium mb-2">{{ trans('cruds.beneficiaryOrder.fields.description') }} :</div>
        <p class="text-muted mb-4">
            {!! $beneficiaryOrder->description ?? '' !!} 
        </p> 
        <div class="card">
            <div class="card-body">
                @if($beneficiaryOrder->service_type == 'courses')
                    @includeIf('admin.beneficiaryOrders.partials.courses-info')
                @elseif($beneficiaryOrder->service_type == 'financial')
                    @includeIf('admin.beneficiaryOrders.partials.financial-info')
                @elseif($beneficiaryOrder->service_type == 'social')
                    @includeIf('admin.beneficiaryOrders.partials.social-info')
                @elseif($beneficiaryOrder->service_type == 'consultant')
                    @includeIf('admin.beneficiaryOrders.partials.consultant-info')
                @elseif($beneficiaryOrder->service_type == 'loan')
                    @includeIf('admin.beneficiaryOrders.partials.loan-info')
                @elseif(str_starts_with($beneficiaryOrder->service_type, 'dynamic_'))
                    @includeIf('admin.beneficiaryOrders.partials.dynamic-service-info')
                @endif
            </div>
        </div>

        <div class="d-flex gap-5 mb-4 flex-wrap justify-content-between">
            @if ($beneficiaryOrder->beneficiary)
                <div class="d-flex align-items-center gap-2">
                    @include('utilities.user-avatar', ['user' => $beneficiaryOrder->beneficiary->user])
                    <div>
                        <span
                            class="d-block fs-14 fw-medium">{{ $beneficiaryOrder->beneficiary->user->name ?? '' }}</span>
                        <span class="fs-12 text-muted">{{ trans('cruds.beneficiaryOrder.fields.beneficiary') }}</span>
                    </div>
                </div>
            @endif
            @if ($beneficiaryOrder->specialist)
                <div class="d-flex align-items-center gap-2">
                    @include('utilities.user-avatar', ['user' => $beneficiaryOrder->specialist])
                    <div>
                        <span class="d-block fs-14 fw-medium">{{ $beneficiaryOrder->specialist->name ?? '' }}</span>
                        <span class="fs-12 text-muted">{{ trans('cruds.beneficiaryOrder.fields.specialist') }}</span>
                    </div>
                </div>
            @endif
            <div class="d-flex align-items-center gap-2 me-3">
                <span class="avatar avatar-md avatar-rounded me-1 bg-success"><i
                        class="ri-calendar-event-line fs-18 lh-1 align-middle"></i></span>
                <div>
                    <div class="fw-medium mb-0 task-title">
                        {{ trans('cruds.beneficiaryOrder.fields.created_at') }}
                    </div>
                    <span class="fs-12 text-muted">{{ $beneficiaryOrder->created_at ?? '' }}</span>
                </div>
            </div> 
        </div> 
        @if ($beneficiaryOrder->attachment)
            <li class="list-group-item">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="lh-1">
                        <span class="avatar avatar-rounded p-2 bg-light">
                            @if ($beneficiaryOrder->attachment->type == 'image')
                                <img src="{{ $beneficiaryOrder->attachment->getUrl('thumb') }}" alt="">
                            @else
                                <div class="avatar avatar-sm text-primary">
                                    <i class="ti ti-file-description fs-24"></i>
                                </div>
                            @endif
                        </span>
                    </div>
                    <div class="flex-fill">
                        <a href="{{ $beneficiaryOrder->attachment->getUrl() }}" target="_blank"><span
                                class="d-block fw-medium">{{ trans('cruds.beneficiaryOrder.fields.attachment') }}</span></a>
                        <span
                            class="d-block text-muted fs-12 fw-normal">{{ formatFileSize($beneficiaryOrder->attachment->size) }}</span>
                    </div>
                </div>
            </li>
        @endif
    </div>
</div> 

