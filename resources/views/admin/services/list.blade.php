@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            [
                'title' => trans('global.list') . ' ' . trans('cruds.service.title'),
                'url' => route('admin.services.index'),
            ],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        {{-- <div class="col-xl-2">
            <div class="card custom-card">
                <div class="ribbon-2 ribbon-primary ribbon-left">
                    <span class="ribbon-text">دورات</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/services/courses.png') }}" class="card-img mb-3" alt="...">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <div class="fw-medium text-dark">الدورات
                            <span class="badge bg-primary-transparent fs-10">
                                {{ $coursesCount ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-link">
                            {{ trans('global.view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-2">
            <div class="card custom-card">
                <div class="ribbon-2 ribbon-secondary ribbon-left">
                    <span class="ribbon-text">إسكان</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/services/housing.png') }}" class="card-img mb-3" alt="...">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <div class="fw-medium text-dark">المباني
                            <span class="badge bg-primary-transparent fs-10">
                                {{ $buildingsCount ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('admin.buildings.index') }}" class="btn btn-link">
                            {{ trans('global.view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-2">
            <div class="card custom-card">
                <div class="ribbon-2 ribbon-warning ribbon-left">
                    <span class="ribbon-text">ماليه</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/services/financial.png') }}" class="card-img mb-3"
                        alt="...">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <div class="fw-medium text-dark">الخدمات
                            <span class="badge bg-primary-transparent fs-10">
                                {{ $serviceCounts['financial'] ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('admin.services.index', ['type' => 'financial']) }}" class="btn btn-link">
                            {{ trans('global.view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="col-xl-2">
            <div class="card custom-card">
                <div class="ribbon-2 ribbon-info ribbon-left">
                    <span class="ribbon-text">اجتماعية</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/services/social.png') }}" class="card-img mb-3" alt="...">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <div class="fw-medium text-dark">الخدمات
                            <span class="badge bg-primary-transparent fs-10">
                                {{ $serviceCounts['social'] ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('admin.services.index', ['type' => 'social']) }}" class="btn btn-link">
                            {{ trans('global.view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-xl-2">
            <div class="card custom-card">
                <div class="ribbon-2 ribbon-success ribbon-left">
                    <span class="ribbon-text">استشارات</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/services/consultant.png') }}" class="card-img mb-3"
                        alt="...">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <div class="fw-medium text-dark">المستشارين
                            <span class="badge bg-primary-transparent fs-10">
                                {{ $consultantsCount ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('admin.consultants.index') }}" class="btn btn-link">
                            {{ trans('global.view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <div class="col-xl-2">
            <div class="card custom-card">
                <div class="ribbon-2 ribbon-danger ribbon-left">
                    <span class="ribbon-text">قروض</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/services/loan.png') }}" class="card-img mb-3" alt="...">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <div class="fw-medium text-dark">الخدمات
                            <span class="badge bg-primary-transparent fs-10">
                                {{ $serviceCounts['loan'] ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('admin.loans.index') }}" class="btn btn-link">
                            {{ trans('global.view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="col-xl-2">
            <div class="card custom-card">
                <div class="ribbon-2 ribbon-secondary ribbon-left">
                    <span class="ribbon-text">ديناميكي</span>
                </div>
                <div class="card-body">
                    <img src="{{ asset('assets/images/services/dynamic.png') }}" class="card-img mb-3"
                        alt="...">
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center">
                        <div class="fw-medium text-dark">الخدمات
                            <span class="badge bg-primary-transparent fs-10">
                                {{ $dynamicServicesCount ?? 0 }}
                            </span>
                        </div>
                        <a href="{{ route('admin.dynamic-services.index') }}" class="btn btn-link">
                            {{ trans('global.view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
