@extends('layouts.master-beneficiary')
@section('styles')
    <style>
        .profile-card .profile-banner-img::before {
            opacity: 0.5;
        }

        .profile-card .profile-banner-img {
            max-height: 300px;
            overflow: hidden;
        }
    </style>
@endsection
@section('content')
    @php 
        $pageTitle = trans('global.profile');
    @endphp
    @include('partials.breadcrumb')

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card profile-card">
                <div class="profile-banner-img">
                    <img src="{{ asset('assets/profile-cover.png') }}" class="card-img-top" alt="...">
                </div>
                <div class="card-body pb-0 position-relative">
                    <div class="row profile-content">
                        <div class="col-xl-9">
                            <div class="card custom-card overflow-hidden border">
                                <div class="card-body">
                                    <ul class="nav nav-tabs tab-style-6 mb-3 p-0" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link w-100 text-start active" id="profile-about-tab"
                                                data-bs-toggle="tab" data-bs-target="#profile-about-tab-pane" type="button"
                                                role="tab" aria-controls="profile-about-tab-pane"
                                                aria-selected="true"><i class="ti ti-user me-2 text-dark"></i>{{ trans('cruds.beneficiary.profile.about') }}</button>
                                        </li> 
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link w-100 text-start" id="family-info-tab"
                                                data-bs-toggle="tab" data-bs-target="#family-info-tab-pane" type="button"
                                                role="tab" aria-controls="family-info-tab-pane"
                                                aria-selected="false"><i class="ti ti-users me-2 text-success"></i>{{ trans('cruds.beneficiary.profile.family_info') }}</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link w-100 text-start" id="economic-info-tab"
                                                data-bs-toggle="tab" data-bs-target="#economic-info-tab-pane" type="button"
                                                role="tab" aria-controls="economic-info-tab-pane"
                                                aria-selected="false"><i class="ti ti-wallet me-2 text-warning"></i>{{ trans('cruds.beneficiary.profile.economic_info') }}</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link w-100 text-start" id="documents-tab"
                                                data-bs-toggle="tab" data-bs-target="#documents-tab-pane" type="button"
                                                role="tab" aria-controls="documents-tab-pane"
                                                aria-selected="false"><i class="ti ti-files me-2 text-danger"></i>{{ trans('cruds.beneficiary.profile.documents') }}</button>
                                        </li> 
                                    </ul>
                                    <div class="tab-content" id="profile-tabs">
                                        <div class="tab-pane show active p-0 border-0" id="profile-about-tab-pane"
                                            role="tabpanel" aria-labelledby="profile-about-tab" tabindex="0">
                                            @include('beneficiary.profile.partials.about')
                                        </div>
                                        <div class="tab-pane" id="family-info-tab-pane" role="tabpanel"
                                            aria-labelledby="family-info-tab" tabindex="0">
                                            @include('partials.beneficiaryForm.family-information',[
                                                'routeName' => 'beneficiary.beneficiary-families.create'
                                            ])
                                        </div>
                                        <div class="tab-pane" id="economic-info-tab-pane" role="tabpanel"
                                            aria-labelledby="economic-info-tab" tabindex="0">
                                            @include('beneficiary.profile.partials.economic-information')
                                        </div>
                                        <div class="tab-pane" id="documents-tab-pane" role="tabpanel"
                                            aria-labelledby="documents-tab" tabindex="0">
                                            @include('beneficiary.profile.partials.documents')
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            @include('beneficiary.profile.partials.sidebar')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

