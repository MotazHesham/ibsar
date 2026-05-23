@extends('layouts.master')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/signature-capture.css') }}">
@endsection
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.beneficiaryOrdersManagement.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.beneficiaryOrder.title'),
                'url' => route('admin.beneficiary-orders.index'),
            ],
            ['title' => trans('global.show') . ' ' . trans('cruds.beneficiaryOrder.title_singular'), 'url' => '#'],
        ];
        $pageTitle =
            trans('global.show') . ' ' . trans('cruds.beneficiaryOrder.title_singular') . ' #' . $beneficiaryOrder->id;
    @endphp
    @include('partials.breadcrumb')

    <!-- Start::row-1 -->
    <div class="row">
        <div class="col-xxl-6">
            @include('admin.beneficiaryOrders.partials.info')

            @include('admin.beneficiaryOrders.partials.donation-allocation')
        </div>
        <div class="col-xxl-6">
            <div class="mb-2">
                @include('admin.beneficiaryOrders.partials.edit-status')
            </div>
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
                        <li class="nav-item flex-fill" role="presentation">
                            <button class="nav-link border border-dashed rounded-bottom-0 px-3" id="activity-tab"
                                data-bs-toggle="tab" data-bs-target="#activity-tab-pane" type="button" role="tab"
                                aria-controls="activity-tab-pane" aria-selected="false" tabindex="-1">
                                {{ trans('cruds.beneficiaryOrder.extra.activity') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content">
                    <div class="tab-pane show active overflow-hidden p-0 border-0" id="followups-tab-pane" role="tabpanel"
                        aria-labelledby="followups-tab" tabindex="0">
                        @include('admin.beneficiaryOrders.partials.followups')
                    </div>
                    <div class="tab-pane overflow-hidden p-0 border-0" id="activity-tab-pane" role="tabpanel"
                        aria-labelledby="activity-tab" tabindex="0">
                        <ul class="list-unstyled profile-timeline" id="activity-timeline" style="max-height: 35rem;">
                            @include('partials.activity', ['activityLogs' => $activityLogs])
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--End::row-1 --> 

@endsection


@section('scripts')
    <script src="{{ asset('js/signature-capture.js') }}"></script>
    <script>
        new SimpleBar(document.getElementById('activity-timeline'), {
            autoHide: true
        });
        new SimpleBar(document.getElementById('wrapper-order-followups-to-scroll'), {
            autoHide: true
        });

        // State management object
        const state = {
            currentPage: 1,
            isLoading: false,
            hasMorePages: '{{ $activityLogs->hasMorePages() ? true : false }}'
        };

        function loadMoreActivities(timeline, observer, loadingIndicator) {
            if (state.isLoading || !state.hasMorePages) return;
            const beneficiaryOrderId = '{{ $beneficiaryOrder->id }}';

            state.isLoading = true;
            loadingIndicator.style.display = 'block';
            state.currentPage++;

            $.ajax({
                url: `/admin/beneficiary-orders/${beneficiaryOrderId}`,
                type: 'GET',
                data: {
                    page: state.currentPage
                },
                success: function(response) {
                    $('#activity-timeline .simplebar-content').append(response.html);
                    loadingIndicator.style.display = 'none';
                    if (!response.hasMorePages) {
                        // No more content
                        state.hasMorePages = false;
                        loadingIndicator.style.display = 'none';
                        // Unobserve the last item since we won't need to load more
                        const items = timeline.querySelectorAll('li');
                        if (items.length > 0) {
                            observer.unobserve(items[items.length - 1]);
                        }
                    } else {
                        observeLastItem(observer);
                    }
                    state.isLoading = false;
                },
                error: function(xhr, status, error) {
                    console.error('Error loading more activities:', error);
                    loadingIndicator.style.display = 'none';
                    state.isLoading = false;
                }
            });
        }
    </script>
@endsection
