@extends('layouts.master-beneficiary')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrder.extra.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.extra.title'),
                'url' => route('beneficiary.beneficiary-orders.index'),
            ],
            [
                'title' => trans('global.show') . ' ' . trans('cruds.beneficiaryOrder.extra.title_singular'),
                'url' => '#',
            ],
        ];
        $page_title =
            trans('global.show') . ' ' . trans('cruds.beneficiaryOrder.extra.title') . ' #' . $beneficiaryOrder->id;
    @endphp
    @include('partials.breadcrumb')

    <!-- Start::row-1 -->
    <div class="row">
        <div class="col-md-6">
            @include('beneficiary.orders.partials.info')
        </div>
        <div class="col-md-6">
            <div class="mb-4">
                @include('beneficiary.orders.partials.status')
            </div>

            @if ($dynamicService && $dynamicService->category == 'training')
                <div class="card custom-card mt-4">
                    <div class="card-header">
                        <div class="card-title">
                            {{ trans('cruds.beneficiaryOrder.extra.attendance') }}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $beneficiaryOrder->beneficiary_id }}"
                                alt="">
                            <p class="text-muted mt-3 mb-0">
                                {{ trans('cruds.beneficiaryOrder.extra.scan_qr_attendance') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <div class="card custom-card justify-content-between">
                <div class="card-header">

                    <ul class="nav nav-tabs tab-style-7 scaleX profile-settings-tab" id="myTab4" role="tablist">
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link border border-dashed rounded-bottom-0 px-3 active" id="followups-tab"
                                data-bs-toggle="tab" data-bs-target="#followups-tab-pane" type="button" role="tab"
                                aria-controls="followups-tab-pane" aria-selected="true">
                                {{ trans('cruds.beneficiaryOrder.extra.followups') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <div class="tab-pane show active overflow-hidden p-0 border-0" id="followups-tab-pane" role="tabpanel"
                        aria-labelledby="followups-tab" tabindex="0">
                        @include('beneficiary.orders.partials.followups')
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!--End::row-1 -->
@endsection


@section('scripts')
    <script>
        new SimpleBar(document.getElementById('wrapper-order-followups-to-scroll'), {
            autoHide: true
        });
    </script>
@endsection
