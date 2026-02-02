@extends('layouts.master')

@section('styles')
@endsection

@section('content')
    <!-- Start::page-header -->
    <div class="d-flex align-items-center justify-content-between page-header-breadcrumb flex-wrap gap-2">
        <div>
            <nav>
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0);">
                            {{ trans('cruds.dashboard.title') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ trans('cruds.dashboard.analytics') }}</li>
                </ol>
            </nav>
            <h1 class="page-title fw-medium fs-18 mb-0">{{ trans('cruds.dashboard.analytics') }}</h1>
        </div>
    </div>
    <!-- End::page-header -->

    <!-- Start::row-1 -->
    <div class="row">
        @can('statistics_access')
            <div class="col-xxl-7">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            {{ trans('cruds.dashboard.orders_acceptance_chart') }}
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="orders-acceptance-chart"></div>
                    </div>
                </div>
            </div>
        @endcan
        <div class="@can('statistics_access') col-xxl-5 @else col-xxl-12 @endcan">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fw-medium mb-2">{{ trans('cruds.dashboard.total_beneficiaries') }}</p>
                                    <h4 class="fw-medium mb-0">{{ $total_beneficiaries->approved ?? 0 }}</h4>
                                </div>
                                <div>
                                    <span class="avatar avatar-lg bg-primary avatar-rounded">
                                        <i class="ri-git-repository-private-line fs-20"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex mt-2 flex-wrap gap-1">
                                <a href="{{ route('admin.beneficiaries.index') }}"
                                    class="fs-12 text-primary d-inline-flex align-items-center">
                                    <span>{{ trans('global.show_all') }}</span><i
                                        class="ti ti-chevron-right rtl-transform-icon ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fw-medium mb-2">{{ trans('cruds.dashboard.total_new_beneficiaries') }}</p>
                                    <h4 class="fw-medium mb-0">{{ $total_beneficiaries->pending ?? 0 }}</h4>
                                </div>
                                <div>
                                    <span class="avatar avatar-lg bg-success avatar-rounded">
                                        <i class="ri-parent-line fs-20"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex mt-2 flex-wrap gap-1">
                                <a href="{{ route('admin.beneficiaries.index', ['status' => 'pending']) }}"
                                    class="fs-12 text-primary d-inline-flex align-items-center">
                                    <span>{{ trans('global.show_all') }}</span><i
                                        class="ti ti-chevron-right rtl-transform-icon ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fw-medium mb-2">{{ trans('cruds.dashboard.total_orders') }}</p>
                                    <h4 class="fw-medium mb-0">{{ $total_orders->finished ?? 0 }}</h4>
                                </div>
                                <div>
                                    <span class="avatar avatar-lg bg-info avatar-rounded">
                                        <i class="ri-bell-line fs-20"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex mt-2 flex-wrap gap-1">
                                <a href="{{ route('admin.beneficiary-orders.index', ['status' => 'finished']) }}"
                                    class="fs-12 text-primary d-inline-flex align-items-center">
                                    <span>{{ trans('global.show_all') }}</span><i
                                        class="ti ti-chevron-right rtl-transform-icon ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="fw-medium mb-2">{{ trans('cruds.dashboard.total_new_orders') }}</p>
                                    <h4 class="fw-medium mb-0">{{ $total_orders->current ?? 0 }}</h4>
                                </div>
                                <div>
                                    <span class="avatar avatar-lg bg-secondary avatar-rounded">
                                        <i class="ri-stack-line fs-20"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex mt-2 flex-wrap gap-1">
                                <a href="{{ route('admin.beneficiary-orders.index', ['status' => 'current']) }}"
                                    class="fs-12 text-primary d-inline-flex align-items-center">
                                    <span>{{ trans('global.show_all') }}</span><i
                                        class="ti ti-chevron-right rtl-transform-icon ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                @can('statistics_access')
                    <div class="col-xl-12 col-md-12 col-sm-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex mb-3">
                                    <div class="fw-medium fs-15 mb-0">{{ trans('cruds.dashboard.orders_by_martial_status') }}
                                    </div>
                                </div>
                                @php
                                    $sum_order_count = $orders_group_by_martial_status->sum('order_count');
                                @endphp
                                @if ($sum_order_count > 0)
                                    <ul class="list-unstyled profit-by-sale-list">

                                        @foreach ($orders_group_by_martial_status as $raw)
                                            @php
                                                $status_name = json_decode($raw->status_name, true);
                                                $badge_class = randomBadgeClass($status_name[app()->getLocale()] ?? '');
                                            @endphp
                                            <li>
                                                <div class="d-flex justify-content-between align-items-top">
                                                    <div class="d-flex">
                                                        <span
                                                            class="avatar avatar-rounded avatar-md bg-{{ $badge_class }}-transparent"><i
                                                                class='bx bx-wallet-alt fs-18'></i></span>
                                                        <div class="d-flex flex-column ms-2">
                                                            <p class="fw-medium mb-0">
                                                                {{ $status_name[app()->getLocale()] ?? '' }}</p>
                                                            <p class="fs-12 text-muted mb-0">{{ $raw->order_count }}
                                                                {{ trans('cruds.dashboard.orders') }}</p>
                                                        </div>
                                                    </div>
                                                    <h6 class="fw-medium mb-0">{{ $raw->order_count }}</h6>
                                                </div>
                                                <div class="progress progress-xs mt-2 mb-0" role="progressbar"
                                                    aria-label="Basic example"
                                                    aria-valuenow="{{ ($raw->order_count / $sum_order_count) * 100 }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $badge_class }}"
                                                        style="width: {{ ($raw->order_count / $sum_order_count) * 100 }}%">
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
        </div>
        <!--End::row-1 -->

        <!-- Start::row-3 -->
        <div class="@can('statistics_access') col-xxl-8 @else col-xxl-6 @endcan">
            <div class="card custom-card overflow-hidden">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title">{{ trans('cruds.dashboard.latest_orders') }}</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        {{ trans('cruds.beneficiaryOrder.fields.id') }}
                                    </th>
                                    <th scope="col">
                                        {{ trans('cruds.beneficiaryOrder.fields.beneficiary') }}
                                    </th>
                                    <th scope="col">
                                        {{ trans('cruds.beneficiaryOrder.fields.title') }}
                                    </th>
                                    <th scope="col">
                                        {{ trans('cruds.beneficiaryOrder.fields.service_type') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latest_orders as $raw)
                                    <tr>
                                        <td>{{ $raw->id }}</td>
                                        <td>{{ $raw->beneficiary->user->name ?? '' }}</td>
                                        <td>{{ $raw->title }}</td>
                                        <td>
                                            <div class="avatar avatar-sm avatar-rounded">
                                                <img src="{{ asset('assets/images/services/' . $raw->service_type . '.png') }}"
                                                    alt="img">
                                            </div> 
                                            {{ $raw->service_type ? (\App\Helpers\DynamicServiceHelper::isDynamicService($raw->service_type) ? \App\Helpers\DynamicServiceHelper::getServiceTitle($raw->service_type) : \App\Models\Service::TYPE_SELECT[$raw->service_type]) : '' }}
                                        </td>
                                        <td>
                                            @can('beneficiary_order_show')
                                                <a class="btn btn-sm btn-icon btn-primary-light"
                                                    href="{{ route('admin.beneficiary-orders.show', $raw->id) }}">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @can('statistics_access')
            <div class="col-xxl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            {{ trans('cruds.dashboard.orders_stats.title') }}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="fs-12 text-muted d-flex align-items-center gap-2">
                            <h5 class="mb-0 fw-meidum">{{ $total_orders->total ?? 0 }}</h5>
                            <div>{{ trans('cruds.dashboard.orders_stats.total') }}</div>
                        </div>
                        <div class="progress progress-xl my-4 mt-2">
                            @if ($total_orders->total > 0)
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ ($total_orders->current / $total_orders->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_orders->current / $total_orders->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.orders_stats.current') }}">
                                    {{ round(($total_orders->current / $total_orders->total) * 100) }}%</div>
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ ($total_orders->finished / $total_orders->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_orders->finished / $total_orders->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.orders_stats.finished') }}">
                                    {{ round(($total_orders->finished / $total_orders->total) * 100) }}%</div>
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ ($total_orders->rejected / $total_orders->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_orders->rejected / $total_orders->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.orders_stats.rejected') }}">
                                    {{ round(($total_orders->rejected / $total_orders->total) * 100) }}%</div>
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ ($total_orders->archived / $total_orders->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_orders->archived / $total_orders->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.orders_stats.archived') }}">
                                    {{ round(($total_orders->archived / $total_orders->total) * 100) }}%</div>
                            @endif
                        </div>
                        <ul class="list-group acquisitions-list mt-1">
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.orders_stats.current') }}
                                <span class="badge float-end bg-info-transparent">{{ $total_orders->current ?? 0 }}</span>
                            </li>
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.orders_stats.finished') }}
                                <span class="badge float-end bg-success-transparent">{{ $total_orders->finished ?? 0 }}</span>
                            </li>
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.orders_stats.rejected') }}
                                <span
                                    class="badge float-end bg-secondary-transparent">{{ $total_orders->rejected ?? 0 }}</span>
                            </li>
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.orders_stats.archived') }}
                                <span class="badge float-end bg-warning-transparent">{{ $total_orders->archived ?? 0 }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endcan
        <!-- End::row-3 -->

        <!-- Start::row-4 -->

        @can('statistics_access')
            <div class="col-xxl-4">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            {{ trans('cruds.dashboard.beneficiary_stats.title') }}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="fs-12 text-muted d-flex align-items-center gap-2">
                            <h5 class="mb-0 fw-meidum">{{ $total_beneficiaries->total ?? 0 }}</h5>
                            <div>{{ trans('cruds.dashboard.beneficiary_stats.total') }}</div>
                        </div>
                        <div class="progress progress-xl my-4 mt-2">
                            @if ($total_beneficiaries->total > 0)
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ ($total_beneficiaries->uncompleted / $total_beneficiaries->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_beneficiaries->uncompleted / $total_beneficiaries->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.beneficiary_stats.uncompleted') }}">
                                    {{ round(($total_beneficiaries->uncompleted / $total_beneficiaries->total) * 100) }}%</div>
                                <div class="progress-bar bg-info" role="progressbar"
                                    style="width: {{ ($total_beneficiaries->rejected / $total_beneficiaries->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_beneficiaries->rejected / $total_beneficiaries->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.beneficiary_stats.rejected') }}">
                                    {{ round(($total_beneficiaries->rejected / $total_beneficiaries->total) * 100) }}%</div>
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ ($total_beneficiaries->pending / $total_beneficiaries->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_beneficiaries->pending / $total_beneficiaries->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.beneficiary_stats.pending') }}">
                                    {{ round(($total_beneficiaries->pending / $total_beneficiaries->total) * 100) }}%</div>
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ ($total_beneficiaries->approved / $total_beneficiaries->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_beneficiaries->approved / $total_beneficiaries->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.beneficiary_stats.approved') }}">
                                    {{ round(($total_beneficiaries->approved / $total_beneficiaries->total) * 100) }}%</div>
                                <div class="progress-bar bg-warning" role="progressbar"
                                    style="width: {{ ($total_beneficiaries->archived / $total_beneficiaries->total) * 100 }}%"
                                    aria-valuenow="{{ ($total_beneficiaries->archived / $total_beneficiaries->total) * 100 }}"
                                    aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-title="{{ trans('cruds.dashboard.beneficiary_stats.archived') }}">
                                    {{ round(($total_beneficiaries->archived / $total_beneficiaries->total) * 100) }}%</div>
                            @endif
                        </div>
                        <ul class="list-group acquisitions-list mt-1">
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.beneficiary_stats.uncompleted') }}
                                <span
                                    class="badge float-end bg-primary-transparent">{{ $total_beneficiaries->uncompleted ?? 0 }}</span>
                            </li>
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.beneficiary_stats.rejected') }}
                                <span
                                    class="badge float-end bg-info-transparent">{{ $total_beneficiaries->rejected ?? 0 }}</span>
                            </li>
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.beneficiary_stats.pending') }}
                                <span
                                    class="badge float-end bg-success-transparent">{{ $total_beneficiaries->pending ?? 0 }}</span>
                            </li>
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.beneficiary_stats.approved') }}
                                <span
                                    class="badge float-end bg-secondary-transparent">{{ $total_beneficiaries->approved ?? 0 }}</span>
                            </li>
                            <li class="list-group-item">
                                {{ trans('cruds.dashboard.beneficiary_stats.archived') }}
                                <span
                                    class="badge float-end bg-warning-transparent">{{ $total_beneficiaries->archived ?? 0 }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endcan
        <div class="@can('statistics_access') col-xxl-8 @else col-xxl-6 @endcan">
            <div class="card custom-card overflow-hidden">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title">{{ trans('cruds.dashboard.latest_beneficiaries') }}</div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table text-nowrap table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">
                                        {{ trans('cruds.beneficiary.fields.id') }}
                                    </th>
                                    <th scope="col">
                                        {{ trans('cruds.beneficiary.fields.user') }}
                                    </th>
                                    <th scope="col">
                                        {{ trans('cruds.user.fields.phone') }}
                                    </th>
                                    <th scope="col">
                                        {{ trans('cruds.user.fields.identity_num') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latest_beneficiaries as $raw)
                                    <tr>
                                        <td>{{ $raw->id }}</td>
                                        <td>
                                            @if ($raw->user)
                                                @include('utilities.user-avatar', ['user' => $raw->user])
                                            @endif
                                            {{ $raw->user->name ?? '' }}
                                        </td>
                                        <td><i class="ri-phone-line me-2 align-middle fs-14 text-muted"></i>
                                            {{ $raw->user->phone ?? '' }}</td>
                                        <td>{{ $raw->user->identity_num ?? '' }}</td>
                                        <td>
                                            @can('beneficiary_show')
                                                <a class="btn btn-sm btn-icon btn-primary-light"
                                                    href="{{ route('admin.beneficiaries.show', $raw->id) }}">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End::row-4 -->

@endsection

@section('scripts')
    <!-- Apex Charts JS -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        var chart = {
            series: [{
                name: 'مقبول',
                data: {{ json_encode($orders_acceptance_chart_data->pluck('accepted')) }},
                type: "bar",
            }, {
                name: 'مرفوض',
                data: {{ json_encode($orders_acceptance_chart_data->pluck('rejected')) }},
                type: "bar",
            }, {
                name: 'منجز',
                data: {{ json_encode($orders_acceptance_chart_data->pluck('done')) }},
                type: "bar",
            }],
            chart: {
                height: 300,
                type: "bar",
                toolbar: {
                    show: false,
                },
                stacked: true,
                fontFamily: 'Nunito, sans-serif',
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                endingShape: 'rounded',
                colors: ['transparent'],
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 3
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                axisBorder: {
                    show: false,
                },
            },
            legend: {
                show: true,
                position: 'bottom',
                offsetY: 10,
                fontSize: "13px",
                markers: {
                    size: 4,
                    shape: 'circle',
                },
            },
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            colors: ['var(--primary-color)', 'rgb(40, 200, 235)', 'rgb(133, 204, 65)', "var(--primary005)"],
            plotOptions: {
                bar: {
                    columnWidth: "15%",
                    borderRadiusApplication: 'around',
                    borderRadiusWhenStacked: 'all',
                    borderRadius: 2,
                }
            },
            fill: {
                opacity: 1
            },
        };
        @can('statistics_access')
            var chart1 = new ApexCharts(document.querySelector("#orders-acceptance-chart"), chart);
            chart1.render();
        @endcan
    </script>
@endsection
