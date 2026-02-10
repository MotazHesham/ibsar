@extends('layouts.master')
@section('content')
    @php
        $breadcrumbs = [
            ['title' => trans('cruds.servicesManagment.title'), 'url' => '#'],
            ['title' => trans('cruds.volunteerTask.title'), 'url' => route('admin.volunteer-tasks.index')],
            ['title' => trans('cruds.volunteerTask.title_singular') . ' #' . $volunteerTask->id . ' - QR', 'url' => '#'],
        ];
    @endphp
    @include('partials.breadcrumb')

    <div class="card">
        <div class="card-header p-3">
            <h6 class="card-title">{{ trans('cruds.volunteerTask.title_singular') }} #{{ $volunteerTask->id }} - QR Code</h6>
        </div>
        <div class="card-body text-center">
            <p class="mb-3">{{ trans('cruds.volunteerTask.fields.name') }}: <strong>{{ $volunteerTask->name }}</strong></p>
            <p class="mb-3">{{ trans('cruds.volunteerTask.fields.volunteer') }}: <strong>{{ $volunteerTask->volunteer?->name }}</strong></p>
            <div class="d-inline-block p-3 bg-light rounded">
                <img src="{{ qrCodeGenerate(route('admin.volunteer-tasks.show', $volunteerTask->id)) }}" alt="QR Code" style="max-width: 200px;">
            </div>
            <div class="mt-3">
                <a class="btn btn-primary-light rounded-pill btn-wave" href="{{ route('admin.volunteer-tasks.show', $volunteerTask->id) }}">{{ trans('global.back') }}</a>
            </div>
        </div>
    </div>
@endsection
