@extends('layouts.landing-master')
@section('content')

    @include('frontend.partials.slider', ['sliders' => $sliders])

    <section class="section bg-white" id="volunteer-join">
        <div class="container">
            <div class="heading-section">
                <div class="heading-subtitle"><span class="text-primary fw-semibold">{{ trans('cruds.volunteer.title_singular') }}</span></div>
                <hr class="center-diamond">
                <div class="heading-title">{{ trans('frontend.volunteer.join_title') }}</div>
                <div class="heading-description">{{ trans('frontend.volunteer.join_description') }}</div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('frontend.volunteer.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">{{ trans('cruds.volunteer.fields.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="identity_num" class="form-label">{{ trans('cruds.volunteer.fields.identity_num') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('identity_num') is-invalid @enderror" id="identity_num" name="identity_num"
                                            value="{{ old('identity_num') }}" required>
                                        @error('identity_num')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">{{ trans('cruds.volunteer.fields.email') }} <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_number" class="form-label">{{ trans('cruds.volunteer.fields.phone_number') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number"
                                            value="{{ old('phone_number') }}" required>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="interest" class="form-label">{{ trans('cruds.volunteer.fields.interest') }}</label>
                                        <input type="text" class="form-control @error('interest') is-invalid @enderror" id="interest" name="interest"
                                            value="{{ old('interest') }}">
                                        @error('interest')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="initiative_name" class="form-label">{{ trans('cruds.volunteer.fields.initiative_name') }}</label>
                                        <input type="text" class="form-control @error('initiative_name') is-invalid @enderror" id="initiative_name" name="initiative_name"
                                            value="{{ old('initiative_name') }}">
                                        @error('initiative_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="prev_experience" class="form-label">{{ trans('cruds.volunteer.fields.prev_experience') }}</label>
                                        <textarea class="form-control @error('prev_experience') is-invalid @enderror" id="prev_experience" name="prev_experience" rows="3">{{ old('prev_experience') }}</textarea>
                                        @error('prev_experience')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="photo" class="form-label">{{ trans('cruds.volunteer.fields.photo') }} <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo"
                                            accept="image/jpeg,image/jpg,image/png,image/gif" required>
                                        <small class="text-muted">{{ trans('cruds.volunteer.fields.photo_helper') }}</small>
                                        @error('photo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cv" class="form-label">{{ trans('cruds.volunteer.fields.cv') }}</label>
                                        <input type="file" class="form-control @error('cv') is-invalid @enderror" id="cv" name="cv"
                                            accept=".pdf,.doc,.docx">
                                        <small class="text-muted">{{ trans('cruds.volunteer.fields.cv_helper') }}</small>
                                        @error('cv')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="ri-send-plane-line me-2"></i>{{ trans('frontend.volunteer.submit') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
