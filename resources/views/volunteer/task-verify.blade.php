@extends('layouts.custom-master')

@php
    $bodyClass = 'bg-white';
@endphp

@section('content')
    <div class="container py-5 text-center">
        <div class="card mx-auto shadow-sm" style="max-width: 360px;">
            <div class="card-body py-5">
                <i class="ri-checkbox-circle-fill text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3">{{ trans('frontend.volunteer.task') }} #{{ $task->id }}</h4>
                <p class="text-muted mb-1">{{ $task->name }}</p>
                <p class="text-muted small">{{ $task->date }} @if($task->arrive_time) – {{ \Carbon\Carbon::parse($task->arrive_time)->format('H:i') }} @endif</p>
                <p class="text-success fw-medium mb-0">{{ getSetting('site_name') }}</p>
            </div>
        </div>
    </div>
@endsection
