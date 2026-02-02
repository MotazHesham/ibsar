@extends('layouts.landing-master')
@section('content')

    @include('frontend.partials.slider')

    <!-- Start:: Section-2 -->
    <section class="section bg-white" id="about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <h4 class="text-primary fw-semibold">{{ trans('frontend.course_attendance.title') }}</h4>
                                <p class="text-muted">{{ trans('frontend.course_attendance.description') }}</p>
                            </div>

                            <form method="POST" action="{{ route('frontend.course-attendance.check') }}">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <div class="mb-3">
                                    <label for="identity_number" class="form-label">{{ trans('frontend.course_attendance.identity_number') }}</label>
                                    <input type="text"
                                        class="form-control @error('identity_number') is-invalid @enderror"
                                        id="identity_number" name="identity_number" placeholder="{{ trans('frontend.course_attendance.identity_number') }}"
                                        value="{{ old('identity_number') }}" required>
                                    @error('identity_number')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>{{ trans('frontend.course_attendance.check_attendance') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End:: Section-2 -->
@endsection
